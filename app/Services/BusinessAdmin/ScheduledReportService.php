<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\ReportDatePreset;
use App\Enums\ReportType;
use App\Models\ScheduledReport;
use App\Models\User;
use App\Services\Mail\MailSender;
use App\Support\Reports\ReportDateRangeResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class ScheduledReportService implements ServiceInterface
{
    public function __construct(
        private readonly ReportCenterService $reports,
        private readonly ReportExportService $export,
        private readonly MailSender $mail,
    ) {}

    /**
     * @return Collection<int, ScheduledReport>
     */
    public function list(User $user): Collection
    {
        return ScheduledReport::query()
            ->where('organization_id', $user->organization_id)
            ->latest()
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data): ScheduledReport
    {
        return ScheduledReport::query()->create([
            'organization_id' => $user->organization_id,
            'name' => $data['name'],
            'saved_report_id' => $data['saved_report_id'] ?? null,
            'report_type' => $data['report_type'] ?? ReportType::ProfitLoss->value,
            'branch_id' => $data['branch_id'] ?? null,
            'filters' => $data['filters'] ?? ['preset' => ReportDatePreset::ThisWeek->value],
            'format' => $data['format'] ?? 'email',
            'frequency' => $data['frequency'],
            'run_at' => $data['run_at'] ?? '08:00',
            'recipients' => $data['recipients'] ?? [$user->email],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'created_by' => $user->id,
        ]);
    }

    public function runDueReports(): int
    {
        $count = 0;
        $now = now();

        $reports = ScheduledReport::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (ScheduledReport $r) => $this->isDue($r, $now));

        foreach ($reports as $scheduled) {
            $this->runOne($scheduled);
            $scheduled->update(['last_run_at' => $now]);
            $count++;
        }

        return $count;
    }

    public function runOne(ScheduledReport $scheduled): void
    {
        $user = User::query()->find($scheduled->created_by)
            ?? User::query()->where('organization_id', $scheduled->organization_id)->whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

        if ($user === null) {
            return;
        }

        abort_unless((int) $user->organization_id === (int) $scheduled->organization_id, 403);

        $filters = $scheduled->filters ?? [];
        $preset = ReportDatePreset::tryFrom($filters['preset'] ?? 'this_week') ?? ReportDatePreset::ThisWeek;
        $range = ReportDateRangeResolver::resolve($preset, $filters['from'] ?? null, $filters['to'] ?? null);
        $filter = new \App\Support\Reports\ReportCenterFilter(
            datePreset: $preset,
            from: $range['from'],
            to: $range['to'],
            branchId: $scheduled->branch_id,
            reportType: ReportType::tryFrom($scheduled->report_type ?? 'profit_loss') ?? ReportType::ProfitLoss,
            employeeId: null,
            status: null,
            compareMode: \App\Enums\ReportCompareMode::None,
            compareBranchId: null,
            compareEmployeeId: null,
        );

        $data = $this->reports->build($user, $filter);
        $recipients = $scheduled->recipients ?? [];

        if ($recipients === []) {
            return;
        }

        $orgName = $user->organization?->name ?? 'Your business';
        $subject = sprintf('TotalCashPro · %s · %s', $scheduled->name ?? 'Scheduled report', $data['range']['label'] ?? '');
        $revenue = number_format((float) ($data['kpis']['revenue'] ?? 0), 2);
        $profit = number_format((float) ($data['kpis']['profit'] ?? 0), 2);

        $text = <<<TEXT
TotalCashPro

{$scheduled->name}

{$orgName}
{$data['range']['label']}

Revenue: £{$revenue}
Net profit: £{$profit}

View your dashboard for full details.

TEXT;

        $html = nl2br(e($text)).'<p><a href="'.route('business-admin.executive.index', absolute: true).'">View Dashboard</a></p>';

        $this->mail->trySend(fn () => $this->mail->send($recipients, $subject, $text, $html, 'scheduled-report'));
    }

    private function isDue(ScheduledReport $scheduled, \Illuminate\Support\Carbon $now): bool
    {
        if ($scheduled->last_run_at && $scheduled->last_run_at->isSameDay($now)) {
            return false;
        }

        $runAt = $scheduled->run_at ? Carbon::parse($scheduled->run_at) : $now->copy()->setTime(8, 0);

        if ($now->format('H:i') < $runAt->format('H:i')) {
            return false;
        }

        return match ($scheduled->frequency) {
            'daily' => true,
            'weekly' => $now->isMonday(),
            'monthly' => $now->day === 1,
            'quarterly' => $now->day === 1 && in_array($now->month, [1, 4, 7, 10], true),
            'yearly' => $now->day === 1 && $now->month === 1,
            default => false,
        };
    }
}
