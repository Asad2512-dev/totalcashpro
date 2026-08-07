<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceStatus;
use App\Enums\WageStatus;
use App\Models\FinancePayrollRun;
use App\Models\User;
use App\Models\Wage;
use App\Repositories\Contracts\FinancePayrollRunRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Contracts\WageRepositoryInterface;
use App\Services\BusinessAdmin\AttendanceService;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Support\Carbon;

final class FinancePayrollService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly WageRepositoryInterface $wages,
        private readonly FinancePayrollRunRepositoryInterface $payrollRuns,
        private readonly StaffRepositoryInterface $staff,
        private readonly AttendanceService $attendance,
    ) {}

    public function list(User $user, string $period = 'current'): mixed
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        $query = Wage::query()
            ->with(['user', 'payrollRun'])
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        if ($period === 'draft') {
            $query->where('status', WageStatus::Draft->value);
        } elseif ($period === 'approved') {
            $query->whereIn('status', [WageStatus::Approved->value, WageStatus::Pending->value]);
        } elseif ($period === 'paid') {
            $query->where('status', WageStatus::Paid->value);
        }

        return $query->latest('created_at')->paginate(20)->withQueryString();
    }

    /**
     * @return array<string, mixed>
     */
    public function formMeta(User $user): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        $baseQuery = Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        return [
            'staff' => $this->staff->activeStaff($orgId, $branchId),
            'payroll_runs' => $this->payrollRuns->listForBranch($orgId, $branchId),
            'summary' => [
                'draft_total' => (float) (clone $baseQuery)->where('status', WageStatus::Draft->value)->sum('gross_amount'),
                'approved_total' => (float) (clone $baseQuery)->whereIn('status', [WageStatus::Approved->value, WageStatus::Pending->value])->sum('gross_amount'),
                'paid_month_total' => (float) (clone $baseQuery)
                    ->where('status', WageStatus::Paid->value)
                    ->whereMonth('paid_date', now()->month)
                    ->whereYear('paid_date', now()->year)
                    ->sum('gross_amount'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeManual(User $user, array $data): Wage
    {
        $staff = User::query()->findOrFail((int) $data['user_id']);

        if ((int) $staff->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $branchId = (int) ($staff->branch_id ?: $this->branchContext->requireBranchId($user));
        $hours = (float) $data['hours_worked'];
        $rate = (float) ($data['hourly_rate'] ?? $staff->hourly_rate ?? 0);
        $gross = round($hours * $rate, 2);

        return Wage::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'user_id' => $staff->id,
            'hours_worked' => $hours,
            'amount' => $gross,
            'net_amount' => $gross,
            'vat_amount' => 0,
            'gross_amount' => $gross,
            'notes' => $data['notes'] ?? null,
            'status' => WageStatus::Draft->value,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Generate draft payroll from attendance for a week with delayed payment date.
     *
     * @param  array<string, mixed>  $data
     */
    public function generateFromAttendance(User $user, array $data): FinancePayrollRun
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $weekStart = Carbon::parse($data['week_start'] ?? now()->startOfWeek())->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $paymentDue = isset($data['payment_due_date'])
            ? Carbon::parse($data['payment_due_date'])
            : $weekEnd->copy()->addWeek();

        $existing = $this->payrollRuns->findForWeek((int) $user->organization_id, $branchId, $weekStart->toDateString());

        if ($existing !== null) {
            return $existing->load('wages.user');
        }

        $run = $this->payrollRuns->createRun([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'payment_due_date' => $paymentDue->toDateString(),
            'status' => FinanceStatus::Draft->value,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        $report = $this->attendance->weeklyReport($user, $weekStart->toDateString());

        foreach ($report['report'] as $row) {
            $member = $row['user'];
            $hours = (float) $row['total_hours'];
            if ($hours <= 0) {
                continue;
            }

            $gross = round((float) $row['total_pay'], 2);

            Wage::query()->create([
                'organization_id' => $user->organization_id,
                'branch_id' => $branchId,
                'user_id' => $member->id,
                'payroll_run_id' => $run->id,
                'hours_worked' => $hours,
                'amount' => $gross,
                'net_amount' => $gross,
                'vat_amount' => 0,
                'gross_amount' => $gross,
                'period_start' => $weekStart->toDateString(),
                'period_end' => $weekEnd->toDateString(),
                'payment_due_date' => $paymentDue->toDateString(),
                'from_attendance' => true,
                'status' => WageStatus::Draft->value,
                'created_by' => $user->id,
            ]);
        }

        return $run->load('wages.user');
    }

    public function approveRun(User $user, int $runId): FinancePayrollRun
    {
        $run = $this->authorizeRun($user, $runId);

        Wage::query()
            ->where('payroll_run_id', $run->id)
            ->where('status', WageStatus::Draft->value)
            ->update([
                'status' => WageStatus::Approved->value,
                'approved_at' => now(),
            ]);

        $run->update([
            'status' => FinanceStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $run->refresh()->load('wages.user');
    }

    public function approveWage(User $user, int $wageId): Wage
    {
        $wage = $this->authorizeWage($user, $wageId);
        $wage->update([
            'status' => WageStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $wage->refresh();
    }

    public function markPaid(User $user, int $wageId): Wage
    {
        $wage = $this->authorizeWage($user, $wageId);
        $wage->update([
            'status' => WageStatus::Paid->value,
            'paid_date' => now()->toDateString(),
        ]);

        if ($wage->payroll_run_id) {
            $pending = Wage::query()
                ->where('payroll_run_id', $wage->payroll_run_id)
                ->whereIn('status', [WageStatus::Draft->value, WageStatus::Approved->value, WageStatus::Pending->value])
                ->count();

            if ($pending === 0) {
                FinancePayrollRun::query()->whereKey($wage->payroll_run_id)->update([
                    'status' => FinanceStatus::Paid->value,
                    'paid_at' => now(),
                ]);
            }
        }

        return $wage->refresh();
    }

    /**
     * Weekly wages view — approved wages grouped by payment due week.
     *
     * @return array<string, mixed>
     */
    public function weeklyWages(User $user, ?string $weekStart = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $start = $weekStart ? Carbon::parse($weekStart)->startOfWeek() : now()->startOfWeek();

        $wages = Wage::query()
            ->with('user')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', [WageStatus::Approved->value, WageStatus::Pending->value, WageStatus::Paid->value])
            ->whereDate('payment_due_date', '>=', $start->toDateString())
            ->whereDate('payment_due_date', '<=', $start->copy()->addDays(6)->toDateString())
            ->orderBy('payment_due_date')
            ->get();

        return [
            'week_start' => $start,
            'wages' => $wages,
            'total' => (float) $wages->sum('gross_amount'),
        ];
    }

    private function authorizeRun(User $user, int $runId): FinancePayrollRun
    {
        $run = FinancePayrollRun::query()->findOrFail($runId);

        if ((int) $run->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $run;
    }

    private function authorizeWage(User $user, int $wageId): Wage
    {
        $wage = Wage::query()->findOrFail($wageId);

        if ((int) $wage->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $wage;
    }
}
