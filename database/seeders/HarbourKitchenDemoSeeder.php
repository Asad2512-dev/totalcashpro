<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AttendanceLogType;
use App\Enums\CashUpShift;
use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceIntegrationProvider;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\AppNotification;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\CashUp;
use App\Models\FinanceBankAccount;
use App\Models\FinanceIncomeEntry;
use App\Models\FinanceIntegrationConnection;
use App\Models\FinancePayrollRun;
use App\Models\FinanceSupplierPayment;
use App\Models\InventoryCategory;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Spending;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use App\Support\Reports\ReportCenterCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Full demo dataset for Harbour Kitchen Group (ava@harbourkitchen.test).
 * Safe to re-run — uses updateOrCreate / scoped deletes for generated history.
 */
final class HarbourKitchenDemoSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::query()->where('slug', 'harbour-kitchen-group')->first();
        if ($org === null) {
            $this->command?->warn('Harbour Kitchen Group not found — run DemoDataSeeder first.');

            return;
        }

        $ava = User::query()->where('email', 'ava@harbourkitchen.test')->first();
        if ($ava === null) {
            $this->command?->warn('Ava Morgan not found.');

            return;
        }

        $ava->update([
            'organization_id' => $org->id,
            'email_verified_at' => $ava->email_verified_at ?? now(),
            'onboarding_completed_at' => $ava->onboarding_completed_at ?? now()->subDays(30),
        ]);

        $branches = $org->branches()->orderBy('id')->get();
        $dockside = $branches->firstWhere('name', 'Dockside') ?? $branches->last();
        $central = $branches->firstWhere('name', 'Harbour Central') ?? $branches->first();
        $staff = User::query()
            ->where('organization_id', $org->id)
            ->whereNotNull('pin_hash')
            ->orderBy('id')
            ->get();

        $ownerId = (int) $org->owner_user_id;

        $mainAccount = FinanceBankAccount::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'name' => 'Main Operating Account',
            ],
            [
                'branch_id' => $dockside->id,
                'bank_name' => 'Barclays',
                'sort_code' => '20-00-00',
                'account_number_last4' => '4821',
                'opening_balance' => 12500.00,
                'is_default' => true,
                'is_active' => true,
            ],
        );

        FinanceBankAccount::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'name' => 'Central Float Account',
            ],
            [
                'branch_id' => $central->id,
                'bank_name' => 'HSBC',
                'sort_code' => '40-00-00',
                'account_number_last4' => '9012',
                'opening_balance' => 3200.00,
                'is_default' => false,
                'is_active' => true,
            ],
        );

        foreach (FinanceIntegrationProvider::cases() as $provider) {
            FinanceIntegrationConnection::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'provider' => $provider->value,
                ],
                [
                    'status' => 'disconnected',
                    'settings' => ['demo' => true],
                ],
            );
        }

        $this->seedFinanceIncome($org, $dockside, $central, $mainAccount, $ownerId);
        $this->seedBillsAndSpendings($org, $dockside, $central, $mainAccount, $ownerId);
        $this->seedSuppliers($org, $dockside, $central, $mainAccount, $ownerId);
        $this->seedPayroll($org, $dockside, $staff, $ownerId);
        $this->seedCashUpHistory($org, $dockside, $central, $ownerId);
        $this->seedInventory($org, $dockside, $central, $ownerId);
        $this->seedAttendanceExtras($org, $dockside, $staff);
        $this->seedNotifications($ava, $staff);

        ReportCenterCache::bump((int) $org->id);

        $this->command?->info('');
        $this->command?->info('═══════════════════════════════════════════════════════');
        $this->command?->info('  Harbour Kitchen Group — full demo ready');
        $this->command?->info('═══════════════════════════════════════════════════════');
        $this->command?->info('  Business admin: ava@harbourkitchen.test / password');
        $this->command?->info('  Staff login:    staff.harbour-kitchen-group@totalcashpro.test / password');
        $this->command?->info('  Staff PINs:     1000 Jamie · 1001 Priya · 1002 Marcus · 1122 Sofia · 1234 Noah');
        $this->command?->info('  Branches:     Harbour Central (London) · Dockside (Brighton)');
        $this->command?->info('  Plan:         Professional (all modules unlocked)');
        $this->command?->info('═══════════════════════════════════════════════════════');
    }

    private function seedFinanceIncome(
        Organization $org,
        Branch $dockside,
        Branch $central,
        FinanceBankAccount $account,
        int $ownerId,
    ): void {
        $entries = [
            ['branch' => $dockside, 'title' => 'Friday night takings', 'gross' => 1842.50, 'days' => 1, 'status' => FinanceStatus::Paid, 'source' => FinanceIncomeSource::CashUp],
            ['branch' => $dockside, 'title' => 'Weekend card settlement', 'gross' => 920.00, 'days' => 3, 'status' => FinanceStatus::Paid, 'source' => FinanceIncomeSource::Manual],
            ['branch' => $dockside, 'title' => 'Catering invoice #104', 'gross' => 650.00, 'days' => 7, 'status' => FinanceStatus::Approved, 'source' => FinanceIncomeSource::Manual],
            ['branch' => $central, 'title' => 'Harbour Central lunch service', 'gross' => 1120.00, 'days' => 2, 'status' => FinanceStatus::Paid, 'source' => FinanceIncomeSource::CashUp],
            ['branch' => $central, 'title' => 'Corporate lunch booking', 'gross' => 480.00, 'days' => 5, 'status' => FinanceStatus::Draft, 'source' => FinanceIncomeSource::Other],
            ['branch' => $dockside, 'title' => 'Deliveroo weekly payout', 'gross' => 388.40, 'days' => 4, 'status' => FinanceStatus::Paid, 'source' => FinanceIncomeSource::Other],
            ['branch' => $dockside, 'title' => 'Uber Eats settlement', 'gross' => 295.20, 'days' => 4, 'status' => FinanceStatus::Approved, 'source' => FinanceIncomeSource::Other],
            ['branch' => $central, 'title' => 'Private event deposit', 'gross' => 750.00, 'days' => 12, 'status' => FinanceStatus::Approved, 'source' => FinanceIncomeSource::Manual],
        ];

        foreach ($entries as $entry) {
            $gross = (float) $entry['gross'];
            $net = round($gross / 1.2, 2);
            $vat = round($gross - $net, 2);
            $date = now()->subDays((int) $entry['days'])->toDateString();
            /** @var FinanceStatus $status */
            $status = $entry['status'];

            FinanceIncomeEntry::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $entry['branch']->id,
                    'title' => $entry['title'],
                    'income_date' => $date,
                ],
                [
                    'bank_account_id' => $account->id,
                    'source' => $entry['source'],
                    'net_amount' => $net,
                    'vat_amount' => $vat,
                    'gross_amount' => $gross,
                    'status' => $status,
                    'approved_at' => in_array($status, [FinanceStatus::Approved, FinanceStatus::Paid], true) ? now()->subDays((int) $entry['days'] - 1) : null,
                    'paid_at' => $status === FinanceStatus::Paid ? now()->subDays((int) $entry['days'] - 1) : null,
                    'created_by' => $ownerId,
                ],
            );
        }

        for ($d = 14; $d <= 45; $d += 7) {
            foreach ([$dockside, $central] as $branch) {
                $gross = 800 + (($d + $branch->id) % 9) * 45;
                $net = round($gross / 1.2, 2);
                FinanceIncomeEntry::query()->updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'branch_id' => $branch->id,
                        'title' => 'Weekly manual income',
                        'income_date' => now()->subDays($d)->toDateString(),
                    ],
                    [
                        'bank_account_id' => $account->id,
                        'source' => FinanceIncomeSource::Manual,
                        'net_amount' => $net,
                        'vat_amount' => round($gross - $net, 2),
                        'gross_amount' => $gross,
                        'status' => FinanceStatus::Paid,
                        'paid_at' => now()->subDays($d - 1),
                        'approved_at' => now()->subDays($d - 1),
                        'created_by' => $ownerId,
                    ],
                );
            }
        }
    }

    private function seedBillsAndSpendings(
        Organization $org,
        Branch $dockside,
        Branch $central,
        FinanceBankAccount $account,
        int $ownerId,
    ): void {
        $bills = [
            ['branch' => $dockside, 'title' => 'Gas & electricity', 'vendor' => 'Brighton Energy', 'gross' => 420.00, 'status' => 'paid', 'due' => -12],
            ['branch' => $dockside, 'title' => 'POS terminal rental', 'vendor' => 'SumUp', 'gross' => 49.00, 'status' => 'approved', 'due' => 8],
            ['branch' => $central, 'title' => 'Water rates Q2', 'vendor' => 'Thames Water', 'gross' => 310.00, 'status' => 'approved', 'due' => 14],
            ['branch' => $central, 'title' => 'Equipment lease', 'vendor' => 'KitchenLease Ltd', 'gross' => 890.00, 'status' => 'draft', 'due' => 21],
            ['branch' => $dockside, 'title' => 'Overdue supplier statement', 'vendor' => 'Coastal Wholesale', 'gross' => 156.80, 'status' => 'overdue', 'due' => -5],
        ];

        foreach ($bills as $bill) {
            $gross = (float) $bill['gross'];
            Bill::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $bill['branch']->id,
                    'title' => $bill['title'],
                ],
                [
                    'vendor' => $bill['vendor'],
                    'category' => 'utilities',
                    'amount' => $gross,
                    'net_amount' => round($gross / 1.2, 2),
                    'vat_amount' => round($gross - ($gross / 1.2), 2),
                    'gross_amount' => $gross,
                    'due_date' => now()->addDays((int) $bill['due'])->toDateString(),
                    'status' => $bill['status'],
                    'bank_account_id' => $account->id,
                    'approved_at' => in_array($bill['status'], ['approved', 'paid', 'overdue'], true) ? now()->subDays(3) : null,
                    'paid_at' => $bill['status'] === 'paid' ? now()->subDays(2) : null,
                    'created_by' => $ownerId,
                ],
            );
        }

        $spendings = [
            ['branch' => $dockside, 'title' => 'Staff uniforms', 'amount' => 89.99, 'status' => 'approved', 'days' => 1],
            ['branch' => $dockside, 'title' => 'Window cleaner', 'amount' => 35.00, 'status' => 'draft', 'days' => 0],
            ['branch' => $central, 'title' => 'Fresh flowers', 'amount' => 42.00, 'status' => 'paid', 'days' => 3],
            ['branch' => $central, 'title' => 'Menu printing', 'amount' => 118.00, 'status' => 'paid', 'days' => 9],
        ];

        foreach ($spendings as $row) {
            $gross = (float) $row['amount'];
            Spending::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $row['branch']->id,
                    'title' => $row['title'],
                    'spent_date' => now()->subDays((int) $row['days'])->toDateString(),
                ],
                [
                    'category' => 'supplies',
                    'amount' => $gross,
                    'net_amount' => round($gross / 1.2, 2),
                    'vat_amount' => round($gross - ($gross / 1.2), 2),
                    'gross_amount' => $gross,
                    'status' => $row['status'],
                    'payment_method' => 'card',
                    'bank_account_id' => $account->id,
                    'approved_at' => in_array($row['status'], ['approved', 'paid'], true) ? now()->subDays((int) $row['days']) : null,
                    'paid_at' => $row['status'] === 'paid' ? now()->subDays((int) $row['days']) : null,
                    'created_by' => $ownerId,
                ],
            );
        }
    }

    private function seedSuppliers(
        Organization $org,
        Branch $dockside,
        Branch $central,
        FinanceBankAccount $account,
        int $ownerId,
    ): void {
        $suppliers = [
            ['name' => 'Coastal Meats', 'branch' => $dockside, 'contact' => 'Mike Turner'],
            ['name' => 'London Dairy Direct', 'branch' => $central, 'contact' => 'Sarah Mills'],
            ['name' => 'EcoPack Supplies', 'branch' => $dockside, 'contact' => 'Jen Wu'],
        ];

        foreach ($suppliers as $index => $def) {
            $supplier = Supplier::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'name' => $def['name'],
                ],
                [
                    'branch_id' => $def['branch']->id,
                    'contact_name' => $def['contact'],
                    'email' => strtolower(str_replace(' ', '.', $def['name'])).'@supplier.test',
                    'phone' => '+44 7700 220'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ],
            );

            $pending = SupplierInvoice::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'supplier_id' => $supplier->id,
                    'invoice_no' => strtoupper(substr($def['name'], 0, 3)).'-P'.($index + 1),
                ],
                [
                    'branch_id' => $def['branch']->id,
                    'invoice_date' => now()->subDays(2)->toDateString(),
                    'due_date' => now()->addDays(14)->toDateString(),
                    'amount' => 320.00 + ($index * 40),
                    'net_amount' => 266.67 + ($index * 33),
                    'vat_amount' => 53.33 + ($index * 7),
                    'gross_amount' => 320.00 + ($index * 40),
                    'description' => 'Weekly delivery',
                    'status' => SupplierInvoiceStatus::Pending->value,
                    'approved_at' => now()->subDay(),
                ],
            );

            $paidInvoice = SupplierInvoice::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'supplier_id' => $supplier->id,
                    'invoice_no' => strtoupper(substr($def['name'], 0, 3)).'-PAID'.($index + 1),
                ],
                [
                    'branch_id' => $def['branch']->id,
                    'invoice_date' => now()->subDays(18)->toDateString(),
                    'due_date' => now()->subDays(4)->toDateString(),
                    'amount' => 210.00 + ($index * 25),
                    'net_amount' => 175.00 + ($index * 20),
                    'vat_amount' => 35.00 + ($index * 5),
                    'gross_amount' => 210.00 + ($index * 25),
                    'description' => 'Previous delivery — settled',
                    'status' => SupplierInvoiceStatus::Paid->value,
                    'approved_at' => now()->subDays(16),
                ],
            );

            FinanceSupplierPayment::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'supplier_invoice_id' => $paidInvoice->id,
                    'payment_date' => now()->subDays(10)->toDateString(),
                ],
                [
                    'branch_id' => $def['branch']->id,
                    'bank_account_id' => $account->id,
                    'net_amount' => (float) $paidInvoice->net_amount,
                    'vat_amount' => (float) $paidInvoice->vat_amount,
                    'gross_amount' => (float) $paidInvoice->gross_amount,
                    'reference' => 'BACS-'.str_pad((string) ($index + 100), 4, '0', STR_PAD_LEFT),
                    'status' => 'paid',
                    'created_by' => $ownerId,
                ],
            );

            unset($pending);
        }
    }

    private function seedPayroll(Organization $org, Branch $dockside, $staff, int $ownerId): void
    {
        $weeks = [
            ['offset' => 0, 'status' => FinanceStatus::Draft],
            ['offset' => 7, 'status' => FinanceStatus::Approved],
            ['offset' => 14, 'status' => FinanceStatus::Paid],
        ];

        foreach ($weeks as $week) {
            $start = now()->startOfWeek()->subWeeks((int) ($week['offset'] / 7));
            $end = $start->copy()->endOfWeek();
            /** @var FinanceStatus $status */
            $status = $week['status'];

            $run = FinancePayrollRun::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'week_start' => $start->toDateString(),
                ],
                [
                    'week_end' => $end->toDateString(),
                    'payment_due_date' => $end->copy()->addDays(3)->toDateString(),
                    'status' => $status,
                    'notes' => 'Weekly payroll run (seeded)',
                    'approved_at' => in_array($status, [FinanceStatus::Approved, FinanceStatus::Paid], true) ? $end->copy()->addDay() : null,
                    'paid_at' => $status === FinanceStatus::Paid ? $end->copy()->addDays(3) : null,
                    'created_by' => $ownerId,
                ],
            );

            foreach ($staff->where('branch_id', $dockside->id)->take(3) as $member) {
                $hours = 24 + ($member->id % 8);
                $gross = round($hours * (float) ($member->hourly_rate ?? 12), 2);
                Wage::query()->updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'branch_id' => $dockside->id,
                        'user_id' => $member->id,
                        'period_start' => $start->toDateString(),
                    ],
                    [
                        'payroll_run_id' => $run->id,
                        'hours_worked' => $hours,
                        'amount' => $gross,
                        'net_amount' => $gross,
                        'vat_amount' => 0,
                        'gross_amount' => $gross,
                        'period_end' => $end->toDateString(),
                        'payment_due_date' => $end->copy()->addDays(3)->toDateString(),
                        'from_attendance' => true,
                        'status' => match ($status) {
                            FinanceStatus::Paid => WageStatus::Paid->value,
                            FinanceStatus::Approved => WageStatus::Approved->value,
                            default => WageStatus::Pending->value,
                        },
                        'approved_at' => in_array($status, [FinanceStatus::Approved, FinanceStatus::Paid], true) ? $end->copy()->addDay() : null,
                        'notes' => 'Payroll run '.$start->format('d M'),
                        'created_by' => $ownerId,
                    ],
                );
            }
        }
    }

    private function seedCashUpHistory(Organization $org, Branch $dockside, Branch $central, int $ownerId): void
    {
        CashUp::query()->where('organization_id', $org->id)->delete();

        foreach ([$dockside, $central] as $branchIndex => $branch) {
            for ($day = 0; $day < 45; $day++) {
                $date = now()->subDays($day)->toDateString();
                $multiplier = 1 + (($day + $branchIndex) % 7) * 0.08;

                foreach (CashUpShift::cases() as $shift) {
                    $coins = round(10 + ($day % 5) * 2.5, 2) * $multiplier;
                    $notes = round(80 + ($day % 6) * 15, 2) * $multiplier;
                    $cards = round(120 + ($day % 4) * 25, 2) * $multiplier;
                    $expenses = round(5 + ($day % 3) * 4, 2);
                    $online = round(40 + ($day % 5) * 12, 2) * $multiplier;
                    $deductions = round($online * 0.1, 2);

                    CashUp::query()->updateOrCreate(
                        [
                            'organization_id' => $org->id,
                            'branch_id' => $branch->id,
                            'cashup_date' => $date,
                            'shift' => $shift->value,
                        ],
                        [
                            'coins_total' => $coins,
                            'coins_detail' => [['coin' => '£1', 'qty' => (int) max(1, $coins)]],
                            'notes_total' => $notes,
                            'notes_detail' => [['note' => '£20', 'qty' => (int) max(1, $notes / 20), 'is_qty' => true]],
                            'cards_total' => $cards,
                            'cards_detail' => [['payment_type' => 'Card Machine 1', 'type' => 'machine', 'amount' => $cards]],
                            'expenses_total' => $expenses,
                            'expenses_detail' => [['description' => 'Supplies', 'amount' => $expenses]],
                            'online_orders_total' => $online,
                            'online_orders_detail' => [
                                ['platform' => 'Uber Eats', 'amount' => round($online * 0.55, 2)],
                                ['platform' => 'Deliveroo', 'amount' => round($online * 0.45, 2)],
                            ],
                            'platform_deductions_total' => $deductions,
                            'platform_deductions_detail' => [
                                ['platform' => 'Uber Eats', 'amount' => round($deductions * 0.55, 2)],
                                ['platform' => 'Deliveroo', 'amount' => round($deductions * 0.45, 2)],
                            ],
                            'created_by' => $ownerId,
                        ],
                    );
                }
            }
        }
    }

    private function seedInventory(Organization $org, Branch $dockside, Branch $central, int $ownerId): void
    {
        $categories = [
            ['branch' => $dockside, 'name' => 'Dry Goods', 'items' => [
                ['name' => 'Burger Buns', 'pcs' => 240, 'limit' => 60],
                ['name' => 'Frozen Fries 2kg', 'pcs' => 18, 'limit' => 24],
            ]],
            ['branch' => $dockside, 'name' => 'Beverages', 'items' => [
                ['name' => 'Soft Drink Cans', 'pcs' => 96, 'limit' => 48],
                ['name' => 'Oat Milk 1L', 'pcs' => 12, 'limit' => 20],
            ]],
            ['branch' => $central, 'name' => 'Front of House', 'items' => [
                ['name' => 'Napkins pack', 'pcs' => 8, 'limit' => 15],
                ['name' => 'Takeaway bags', 'pcs' => 25, 'limit' => 30],
            ]],
        ];

        foreach ($categories as $catDef) {
            $category = InventoryCategory::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $catDef['branch']->id,
                    'name' => $catDef['name'],
                ],
                ['description' => $catDef['name'].' stock'],
            );

            foreach ($catDef['items'] as $itemDef) {
                $item = InventoryItem::query()->updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'branch_id' => $catDef['branch']->id,
                        'name' => $itemDef['name'],
                    ],
                    [
                        'category_id' => $category->id,
                        'packaging' => 'unit',
                        'pcs_per_box' => 1,
                        'stock_total_pcs' => $itemDef['pcs'],
                        'stock_limit' => $itemDef['limit'],
                    ],
                );

                InventoryCount::query()->updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'branch_id' => $catDef['branch']->id,
                        'item_id' => $item->id,
                        'notes' => 'Weekly stock check',
                    ],
                    [
                        'diff_pcs' => -3,
                        'new_pcs' => max(0, $itemDef['pcs'] - 3),
                        'created_by' => $ownerId,
                    ],
                );
            }
        }
    }

    private function seedAttendanceExtras(Organization $org, Branch $dockside, $staff): void
    {
        $member = $staff->first();
        if ($member === null) {
            return;
        }

        AttendanceBreak::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'user_id' => $member->id,
                'break_started_at' => now()->startOfDay()->addHours(12),
            ],
            [
                'branch_id' => $dockside->id,
                'break_ended_at' => now()->startOfDay()->addHours(12)->addMinutes(30),
            ],
        );

        for ($d = 7; $d <= 35; $d += 7) {
            $day = now()->subDays($d)->startOfDay()->addHours(9);
            AttendanceLog::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockIn->value,
                    'logged_at' => $day,
                ],
                ['branch_id' => $dockside->id],
            );
            AttendanceLog::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockOut->value,
                    'logged_at' => $day->copy()->addHours(7),
                ],
                ['branch_id' => $dockside->id],
            );
        }
    }

    private function seedNotifications(User $ava, $staff): void
    {
        $notifications = [
            ['user' => $ava, 'title' => 'Low stock alert — Paper Cups', 'body' => 'Dockside packaging is below the reorder limit.', 'type' => 'alert', 'read' => false],
            ['user' => $ava, 'title' => 'Payroll run ready for approval', 'body' => 'This week\'s Dockside payroll is waiting for review.', 'type' => 'info', 'read' => false],
            ['user' => $ava, 'title' => 'Supplier invoice due soon', 'body' => 'Fresh Produce Co invoice FP-1001 is due in 10 days.', 'type' => 'info', 'read' => true],
            ['user' => $ava, 'title' => 'Cash up completed', 'body' => 'Morning shift cash up saved for Dockside.', 'type' => 'success', 'read' => true],
        ];

        foreach ($notifications as $note) {
            AppNotification::query()->updateOrCreate(
                [
                    'user_id' => $note['user']->id,
                    'title' => $note['title'],
                ],
                [
                    'body' => $note['body'],
                    'type' => $note['type'],
                    'priority' => $note['type'] === 'alert' ? 'high' : 'normal',
                    'read_at' => $note['read'] ? now()->subHours(2) : null,
                ],
            );
        }

        $staffUser = $staff->first();
        if ($staffUser !== null) {
            AppNotification::query()->updateOrCreate(
                [
                    'user_id' => $staffUser->id,
                    'title' => 'Your rota is published',
                ],
                [
                    'body' => 'Next week\'s shifts are now available in Staff Rota.',
                    'type' => 'info',
                    'priority' => 'normal',
                    'read_at' => null,
                ],
            );
        }
    }
}
