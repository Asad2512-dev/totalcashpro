<?php

declare(strict_types=1);

namespace Database\Seeders\HarbourKitchen;

use App\Enums\AlertStatus;
use App\Enums\AlertType;
use App\Enums\AttendanceLogType;
use App\Enums\BreakType;
use App\Enums\BudgetCategory;
use App\Enums\CashDrawerStatus;
use App\Enums\CashUpShift;
use App\Enums\CashUpStatus;
use App\Enums\DeliveryStatus;
use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Enums\InventoryStocktakeStatus;
use App\Enums\LeaveType;
use App\Enums\OrganizationStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\RequestStatus;
use App\Enums\RoleSlug;
use App\Enums\RotaVersionStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\BusinessAlert;
use App\Models\CashDrawer;
use App\Models\CashUp;
use App\Models\CrmCustomer;
use App\Models\CrmCustomerVisit;
use App\Models\Delivery;
use App\Models\FinanceBankAccount;
use App\Models\FinanceIncomeEntry;
use App\Models\FinancePayrollRun;
use App\Models\GoodsReceivedLine;
use App\Models\GoodsReceivedNote;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryStocktake;
use App\Models\InventoryStocktakeItem;
use App\Models\KioskBreakType;
use App\Models\LeaveRequest;
use App\Models\Organization;
use App\Models\OrganizationKioskSetting;
use App\Models\Plan;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Rider;
use App\Models\Role;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\ShiftSwapRequest;
use App\Models\Spending;
use App\Models\StaffAvailability;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use App\Services\BusinessAdmin\CashReconciliationService;
use App\Support\Reports\ReportCenterCache;
use App\Support\Security\StaffPinHasher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class HarbourKitchenRealisticSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $ctx = [];

    public function run(): void
    {
        $this->seedFoundation();
        $this->seedKiosk();
        $this->seedRota();
        $this->seedAttendance();
        $this->seedCashUps();
        $this->seedInventory();
        $this->seedSuppliersAndProcurement();
        $this->seedFinance();
        $this->seedPayroll();
        $this->seedCrm();
        $this->seedHr();
        $this->seedBudgetsAndAlerts();
        $this->seedStocktakes();

        ReportCenterCache::bump((int) $this->ctx['org']->id);

        $this->printCredentials();
    }

    private function seedFoundation(): void
    {
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $riderRole = Role::query()->where('slug', RoleSlug::Rider->value)->first();
        $pro = Plan::query()->where('slug', 'professional')->firstOrFail();

        $ava = User::query()->updateOrCreate(
            ['email' => 'ava@harbourkitchen.test'],
            [
                'name' => 'Ava Morgan',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'status' => 'active',
                'email_verified_at' => now()->subDays(60),
                'onboarding_completed_at' => now()->subDays(55),
            ],
        );

        $org = Organization::query()->updateOrCreate(
            ['slug' => 'harbour-kitchen-group'],
            [
                'name' => 'Harbour Kitchen Group',
                'email' => 'ops@harbourkitchen.test',
                'phone' => '+44 20 7946 0958',
                'country' => 'GB',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'owner_user_id' => $ava->id,
                'status' => OrganizationStatus::Active,
                'opens_at' => '08:00',
                'closes_at' => '23:00',
                'settings' => [
                    'cash' => ['default_opening_float' => 100.00],
                    'industry' => 'restaurant_hospitality',
                ],
            ],
        );

        $ava->update(['organization_id' => $org->id]);

        Subscription::query()->updateOrCreate(
            ['organization_id' => $org->id],
            [
                'plan_id' => $pro->id,
                'status' => SubscriptionStatus::Active->value,
                'starts_at' => now()->subMonths(6),
                'current_period_start' => now()->startOfMonth(),
                'current_period_end' => now()->endOfMonth(),
            ],
        );

        $branchDefs = [
            ['name' => 'Dockside Kitchen', 'slug' => 'dockside-kitchen', 'city' => 'Brighton', 'postcode' => 'BN1 1AA', 'address' => '12 Marina Parade, Brighton'],
            ['name' => 'Harbour Central', 'slug' => 'harbour-central', 'city' => 'London', 'postcode' => 'E14 5AB', 'address' => '48 Harbour Way, Canary Wharf, London'],
            ['name' => 'Riverside', 'slug' => 'riverside', 'city' => 'Kingston', 'postcode' => 'KT1 1HL', 'address' => '3 Thames Street, Kingston upon Thames'],
        ];

        $branches = collect();
        foreach ($branchDefs as $i => $def) {
            $branches->push(Branch::query()->updateOrCreate(
                ['organization_id' => $org->id, 'slug' => $def['slug']],
                [
                    'name' => $def['name'],
                    'address' => $def['address'],
                    'city' => $def['city'],
                    'postcode' => $def['postcode'],
                    'phone' => '+44 7700 900'.str_pad((string) (10 + $i), 2, '0', STR_PAD_LEFT),
                    'email' => Str::slug($def['slug']).'@harbourkitchen.test',
                    'status' => 'open',
                    'opening_hours' => ['mon' => '08:00-23:00', 'sun' => '09:00-22:00'],
                    'receipt_footer' => 'Thank you for dining with Harbour Kitchen Group',
                    'settings' => ['till_count' => $i === 0 ? 3 : 2],
                ],
            ));
        }

        $dockside = $branches[0];
        $central = $branches[1];
        $riverside = $branches[2];

        $staffDefs = [
            ['Dockside Kitchen', [['Jamie Cole', 'jamie.cole@harbourkitchen.test', 1001], ['Priya Shah', 'priya.shah@harbourkitchen.test', 1002], ['Marcus Lee', 'marcus.lee@harbourkitchen.test', 1003], ['Elena Voss', 'elena.voss@harbourkitchen.test', 1004], ['Tom Wright', 'tom.wright@harbourkitchen.test', 1005]]],
            ['Harbour Central', [['Sofia Reed', 'sofia.reed@harbourkitchen.test', 1006], ['Noah Blake', 'noah.blake@harbourkitchen.test', 1007], ['Amara Singh', 'amara.singh@harbourkitchen.test', 1008], ['Jake Foster', 'jake.foster@harbourkitchen.test', 1009], ['Lily Chen', 'lily.chen@harbourkitchen.test', 1010]]],
            ['Riverside', [['Owen Harris', 'owen.harris@harbourkitchen.test', 1011], ['Mia Patel', 'mia.patel@harbourkitchen.test', 1012], ['Ryan O\'Brien', 'ryan.obrien@harbourkitchen.test', 1013], ['Zara Ahmed', 'zara.ahmed@harbourkitchen.test', 1014], ['Ben Cooper', 'ben.cooper@harbourkitchen.test', 1015]]],
        ];

        $staff = collect();
        $pin = 1001;
        foreach ($staffDefs as [$branchName, $members]) {
            $branch = $branches->firstWhere('name', $branchName);
            foreach ($members as $idx => [$name, $email, $staffPin]) {
                $rate = 11.50 + ($idx * 0.75);
                $staff->push(User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('password'),
                        'role_id' => $staffRole->id,
                        'organization_id' => $org->id,
                        'branch_id' => $branch->id,
                        'phone' => '+44 7700 '.str_pad((string) $pin, 6, '0', STR_PAD_LEFT),
                        'pin_hash' => StaffPinHasher::hash((string) $staffPin),
                        'hourly_rate' => $rate,
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ],
                ));
            }
        }

        $dockside->update(['manager_user_id' => $staff->firstWhere('email', 'jamie.cole@harbourkitchen.test')?->id]);

        $tillConfig = [
            'Dockside Kitchen' => 3,
            'Harbour Central' => 2,
            'Riverside' => 2,
        ];
        $drawers = collect();
        foreach ($tillConfig as $branchName => $count) {
            $branch = $branches->firstWhere('name', $branchName);
            for ($t = 1; $t <= $count; $t++) {
                $code = sprintf('TILL-%02d', $t);
                $drawers->push(CashDrawer::query()->updateOrCreate(
                    ['organization_id' => $org->id, 'branch_id' => $branch->id, 'code' => $code],
                    [
                        'name' => "Till {$t}",
                        'opening_balance' => 100,
                        'current_balance' => 100,
                        'is_active' => true,
                        'status' => CashDrawerStatus::Active->value,
                        'settings' => ['default_opening_float' => 100],
                        'created_by' => $ava->id,
                    ],
                ));
            }
        }

        if ($riderRole) {
            $riderUser = User::query()->updateOrCreate(
                ['email' => 'rider@harbourkitchen.test'],
                [
                    'name' => 'Alex Rider',
                    'password' => Hash::make('password'),
                    'role_id' => $riderRole->id,
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );
            Rider::query()->updateOrCreate(
                ['organization_id' => $org->id, 'user_id' => $riderUser->id],
                ['branch_ids' => $branches->pluck('id')->all(), 'phone' => '+44 7700 888001', 'vehicle' => 'Van', 'is_active' => true],
            );
            $this->ctx['rider'] = $riderUser;
        }

        FinanceBankAccount::query()->updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Main Operating Account'],
            [
                'branch_id' => $dockside->id,
                'bank_name' => 'Barclays',
                'sort_code' => '20-00-00',
                'account_number_last4' => '4821',
                'opening_balance' => 25000,
                'is_default' => true,
                'is_active' => true,
            ],
        );

        $this->ctx = array_merge($this->ctx, compact('org', 'ava', 'branches', 'dockside', 'central', 'riverside', 'staff', 'drawers'));
    }

    private function seedKiosk(): void
    {
        $org = $this->ctx['org'];
        OrganizationKioskSetting::query()->updateOrCreate(
            ['organization_id' => $org->id],
            ['display_name' => 'Staff Clock', 'show_attendance_list' => true, 'default_branch_id' => $this->ctx['dockside']->id],
        );

        foreach (['Lunch', 'Jumma', 'Namaz', 'Tea Break'] as $i => $name) {
            KioskBreakType::query()->updateOrCreate(
                ['organization_id' => $org->id, 'slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_paid' => in_array($name, ['Jumma', 'Namaz', 'Tea Break'], true),
                    'max_duration_minutes' => $name === 'Lunch' ? 60 : 30,
                    'is_active' => true,
                    'display_order' => $i + 1,
                ],
            );
        }
    }

    private function seedRota(): void
    {
        $org = $this->ctx['org'];
        $sections = collect();
        foreach ($this->ctx['branches'] as $branch) {
            foreach (['Kitchen', 'Front of House', 'Grill'] as $i => $name) {
                $sections->push(RotaSection::query()->updateOrCreate(
                    ['organization_id' => $org->id, 'branch_id' => $branch->id, 'name' => $name],
                    ['color' => ['#0F766E', '#16A34A', '#B45309'][$i]],
                ));
            }
        }

        for ($w = 0; $w < 2; $w++) {
            $weekStart = now()->startOfWeek()->subWeeks(1 - $w);
            foreach ($this->ctx['branches'] as $branch) {
                $version = RotaVersion::query()->updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'branch_id' => $branch->id,
                        'week_start' => $weekStart->toDateString(),
                        'version_number' => 1,
                    ],
                    ['status' => RotaVersionStatus::Published, 'published_at' => $weekStart->copy()->subDay()],
                );

                $branchStaff = $this->ctx['staff']->where('branch_id', $branch->id)->values();
                $branchSections = $sections->where('branch_id', $branch->id)->values();

                foreach ($branchStaff as $si => $member) {
                    for ($d = 0; $d < 5; $d++) {
                        $date = $weekStart->copy()->addDays($d)->toDateString();
                        $section = $branchSections[$si % $branchSections->count()];
                        foreach ([['Morning', '09:00', '15:00'], ['Evening', '17:00', '22:00']] as $idx => [$type, $start, $end]) {
                            if ($type === 'Evening' && $d % 2 !== 0) {
                                continue;
                            }
                            RotaShift::query()->firstOrCreate(
                                [
                                    'organization_id' => $org->id,
                                    'branch_id' => $branch->id,
                                    'user_id' => $member->id,
                                    'shift_date' => $date,
                                    'shift_type' => $type,
                                ],
                                [
                                    'rota_version_id' => $version->id,
                                    'rota_section_id' => $section->id,
                                    'start_time' => Carbon::parse("{$date} {$start}"),
                                    'end_time' => Carbon::parse("{$date} {$end}"),
                                    'status' => 'published',
                                ],
                            );
                        }
                    }
                }
            }
        }
    }

    private function seedAttendance(): void
    {
        $org = $this->ctx['org'];

        foreach ($this->ctx['staff'] as $index => $member) {
            for ($d = 14; $d >= 1; $d--) {
                if ($d % 7 === 0) {
                    continue;
                }
                $day = now()->subDays($d)->startOfDay();
                $late = $index === 2 && $d === 3;
                $clockIn = $day->copy()->setTime(9, $late ? 25 : 0);
                AttendanceLog::query()->create([
                    'organization_id' => $org->id,
                    'branch_id' => $member->branch_id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockIn->value,
                    'logged_at' => $clockIn,
                ]);

                if ($d === 1 && $index === 0) {
                    continue;
                }

                AttendanceLog::query()->create([
                    'organization_id' => $org->id,
                    'branch_id' => $member->branch_id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockOut->value,
                    'logged_at' => $day->copy()->setTime(17, 5),
                ]);

                if ($d === 5 && $index === 1) {
                    AttendanceBreak::query()->create([
                        'organization_id' => $org->id,
                        'branch_id' => $member->branch_id,
                        'user_id' => $member->id,
                        'break_type' => BreakType::Lunch,
                        'break_started_at' => $day->copy()->setTime(12, 30),
                        'break_ended_at' => $day->copy()->setTime(13, 0),
                        'is_paid' => false,
                        'status' => 'completed',
                    ]);
                }
            }
        }
    }

    private function seedCashUps(): void
    {
        $org = $this->ctx['org'];
        $recon = app(CashReconciliationService::class);
        $ownerId = $this->ctx['ava']->id;

        $scenarios = [
            ['branch' => 'Dockside Kitchen', 'till' => 'TILL-01', 'shift' => 'Morning', 'daysAgo' => 0, 'noteQty' => 11, 'expense' => 50, 'cards' => 0],
            ['branch' => 'Dockside Kitchen', 'till' => 'TILL-01', 'shift' => 'Evening', 'daysAgo' => 0, 'noteQty' => 9, 'expense' => 35, 'cards' => 180],
            ['branch' => 'Dockside Kitchen', 'till' => 'TILL-02', 'shift' => 'Evening', 'daysAgo' => 0, 'noteQty' => 7, 'expense' => 20, 'cards' => 120],
            ['branch' => 'Harbour Central', 'till' => 'TILL-01', 'shift' => 'Morning', 'daysAgo' => 1, 'noteQty' => 10, 'expense' => 40, 'cards' => 0],
            ['branch' => 'Harbour Central', 'till' => 'TILL-02', 'shift' => 'Evening', 'daysAgo' => 1, 'noteQty' => 8, 'expense' => 25, 'cards' => 200],
            ['branch' => 'Riverside', 'till' => 'TILL-01', 'shift' => 'Evening', 'daysAgo' => 2, 'noteQty' => 6, 'expense' => 15, 'cards' => 95],
        ];

        foreach ($scenarios as $s) {
            $branch = $this->ctx['branches']->firstWhere('name', $s['branch']);
            $drawer = $this->ctx['drawers']->first(fn ($d) => $d->branch_id === $branch->id && $d->code === $s['till']);
            if (! $drawer) {
                continue;
            }

            $opening = 100.0;
            $coinsTotal = 12.40;
            $notesTotal = $s['noteQty'] * 50.0;
            $actualCash = round($notesTotal + $coinsTotal, 2);
            $expenses = (float) $s['expense'];
            $reconciled = $recon->reconcile($opening, $actualCash, $expenses);

            CashUp::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $branch->id,
                    'cash_drawer_id' => $drawer->id,
                    'cashup_date' => now()->subDays($s['daysAgo'])->toDateString(),
                    'shift' => CashUpShift::from($s['shift']),
                ],
                [
                    'opening_float' => $opening,
                    'coins_total' => $coinsTotal,
                    'coins_detail' => [['coin' => '£1', 'qty' => 8], ['coin' => '20p', 'qty' => 12]],
                    'notes_total' => $notesTotal,
                    'notes_detail' => [['note' => '£50', 'qty' => $s['noteQty'], 'is_qty' => true]],
                    'cards_total' => (float) $s['cards'],
                    'cards_detail' => $s['cards'] > 0 ? [['payment_type' => 'Card Machine', 'type' => 'machine', 'amount' => $s['cards']]] : [],
                    'expenses_total' => $expenses,
                    'expenses_detail' => [['description' => 'Supplies', 'amount' => $expenses]],
                    'online_orders_total' => 45.00,
                    'online_orders_detail' => [['platform' => 'Uber Eats', 'amount' => 45]],
                    'cash_sales_total' => $reconciled['cash_sales'],
                    'expected_cash' => $reconciled['expected_cash'],
                    'actual_cash' => $reconciled['actual_cash'],
                    'variance' => $reconciled['variance'],
                    'status' => CashUpStatus::Approved->value,
                    'approved_at' => now(),
                    'locked_at' => now(),
                    'created_by' => $ownerId,
                ],
            );

            $drawer->update(['last_cash_up_at' => now(), 'current_balance' => $reconciled['actual_cash']]);
        }
    }

    private function seedInventory(): void
    {
        $org = $this->ctx['org'];
        $categories = ['Meat', 'Vegetables', 'Frozen', 'Dry Goods', 'Drinks', 'Packaging', 'Cleaning'];
        $items = [
            ['Chicken Breast', 'Meat', 45, 20, 8.50],
            ['Beef Mince', 'Meat', 30, 15, 9.20],
            ['Salmon Fillet', 'Frozen', 18, 10, 12.00],
            ['Potatoes', 'Vegetables', 80, 25, 1.20],
            ['Onions', 'Vegetables', 60, 20, 0.80],
            ['Tomatoes', 'Vegetables', 40, 15, 1.50],
            ['Frozen Chips', 'Frozen', 50, 20, 3.50],
            ['Pizza Dough', 'Frozen', 25, 10, 2.80],
            ['Flour 16kg', 'Dry Goods', 12, 4, 14.00],
            ['Olive Oil 5L', 'Dry Goods', 8, 3, 22.00],
            ['Coca-Cola 330ml', 'Drinks', 120, 48, 0.65],
            ['Sparkling Water', 'Drinks', 96, 36, 0.45],
            ['Paper Cups 12oz', 'Packaging', 180, 40, 0.12],
            ['Takeaway Boxes', 'Packaging', 200, 50, 0.18],
            ['Floor Cleaner', 'Cleaning', 6, 2, 8.50],
        ];

        $catMap = [];
        foreach ($categories as $i => $name) {
            $catMap[$name] = InventoryCategory::query()->updateOrCreate(
                ['organization_id' => $org->id, 'branch_id' => $this->ctx['dockside']->id, 'name' => $name],
                ['description' => $name.' supplies'],
            );
        }

        $extra = ['Rice 20kg', 'Pasta Penne', 'Mozzarella', 'Cheddar Block', 'Butter', 'Eggs Tray', 'Lettuce', 'Cucumber', 'Peppers', 'Mushrooms', 'Garlic', 'Ginger', 'Coconut Milk', 'Soy Sauce', 'Ketchup', 'Mayonnaise', 'Napkins', 'Gloves', 'Sanitiser', 'Bin Bags', 'Sprite', 'Orange Juice', 'Lamb Chops', 'Prawns', 'Tofu'];
        foreach ($extra as $j => $name) {
            $cat = $categories[$j % count($categories)];
            $items[] = [$name, $cat, rand(10, 80), rand(5, 20), round(rand(50, 1500) / 100, 2)];
        }

        foreach ($this->ctx['branches'] as $branch) {
            foreach ($items as [$name, $catName, $stock, $limit, $cost]) {
                InventoryItem::query()->updateOrCreate(
                    ['organization_id' => $org->id, 'branch_id' => $branch->id, 'name' => $name],
                    [
                        'category_id' => $catMap[$catName]->id ?? null,
                        'sku' => strtoupper(Str::slug(substr($name, 0, 8))).'-'.$branch->id,
                        'stock_total_pcs' => $stock,
                        'stock_limit' => $limit,
                        'cost_price' => $cost,
                        'selling_price' => round($cost * 2.5, 2),
                    ],
                );
            }
        }
    }

    private function seedSuppliersAndProcurement(): void
    {
        $org = $this->ctx['org'];
        $ownerId = $this->ctx['ava']->id;
        $suppliers = collect([
            ['Fresh Produce Co', 'food', 'orders@freshproduce.test'],
            ['Harbour Beverages', 'beverage', 'sales@harbourbev.test'],
            ['PackRight Ltd', 'packaging', 'hello@packright.test'],
            ['CleanPro Supplies', 'cleaning', 'orders@cleanpro.test'],
            ['Premium Meats UK', 'meat', 'trade@premiummeats.test'],
            ['Thames Valley Foods', 'food', 'accounts@thamesvalley.test'],
        ]);

        $supplierModels = collect();
        foreach ($suppliers as $i => [$name, $type, $email]) {
            $branch = $this->ctx['branches'][$i % 3];
            $supplierModels->push(Supplier::query()->updateOrCreate(
                ['organization_id' => $org->id, 'name' => $name],
                [
                    'branch_id' => $branch->id,
                    'contact_name' => 'Accounts Team',
                    'email' => $email,
                    'phone' => '+44 20 7000 '.str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ],
            ));
        }

        $po = PurchaseOrder::query()->updateOrCreate(
            ['organization_id' => $org->id, 'po_number' => 'PO-HK-0001'],
            [
                'branch_id' => $this->ctx['dockside']->id,
                'supplier_id' => $supplierModels[0]->id,
                'status' => PurchaseOrderStatus::Received->value,
                'ordered_at' => now()->subDays(4),
                'expected_at' => now()->subDays(2),
                'subtotal' => 248.50,
                'vat_total' => 49.70,
                'total' => 298.20,
                'created_by' => $ownerId,
                'approved_at' => now()->subDays(5),
                'approved_by' => $ownerId,
            ],
        );

        $line = PurchaseOrderLine::query()->updateOrCreate(
            ['purchase_order_id' => $po->id, 'description' => 'Weekly vegetables'],
            ['quantity' => 1, 'unit_cost' => 248.50, 'line_total' => 248.50, 'quantity_received' => 1],
        );

        $delivery = Delivery::query()->updateOrCreate(
            ['purchase_order_id' => $po->id],
            [
                'organization_id' => $org->id,
                'branch_id' => $this->ctx['dockside']->id,
                'rider_id' => Rider::query()->where('organization_id', $org->id)->value('id'),
                'status' => DeliveryStatus::Delivered,
                'expected_delivery_at' => now()->subDays(2),
                'delivered_at' => now()->subDays(2),
            ],
        );

        $grn = GoodsReceivedNote::query()->updateOrCreate(
            ['purchase_order_id' => $po->id, 'grn_number' => 'GRN-HK-0001'],
            [
                'organization_id' => $org->id,
                'branch_id' => $this->ctx['dockside']->id,
                'delivery_id' => $delivery->id,
                'received_at' => now()->subDays(2),
                'received_by' => $ownerId,
                'status' => 'completed',
            ],
        );

        GoodsReceivedLine::query()->updateOrCreate(
            ['goods_received_note_id' => $grn->id, 'purchase_order_line_id' => $line->id],
            ['quantity_received' => 1, 'quantity_accepted' => 1],
        );

        SupplierInvoice::query()->updateOrCreate(
            ['organization_id' => $org->id, 'invoice_no' => 'FP-2026-001'],
            [
                'supplier_id' => $supplierModels[0]->id,
                'branch_id' => $this->ctx['dockside']->id,
                'invoice_date' => now()->subDays(2)->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'amount' => 298.20,
                'description' => 'Weekly produce delivery',
                'status' => SupplierInvoiceStatus::Pending->value,
            ],
        );

        SupplierInvoice::query()->updateOrCreate(
            ['organization_id' => $org->id, 'invoice_no' => 'PM-2026-042'],
            [
                'supplier_id' => $supplierModels[4]->id,
                'branch_id' => $this->ctx['central']->id,
                'invoice_date' => now()->subDays(20)->toDateString(),
                'due_date' => now()->subDays(5)->toDateString(),
                'amount' => 1250.00,
                'status' => SupplierInvoiceStatus::Overdue->value,
            ],
        );
    }

    private function seedFinance(): void
    {
        $org = $this->ctx['org'];
        $ownerId = $this->ctx['ava']->id;

        Bill::query()->updateOrCreate(
            ['organization_id' => $org->id, 'branch_id' => $this->ctx['dockside']->id, 'title' => 'Monthly rent - Dockside'],
            ['vendor' => 'Harbour Properties', 'category' => 'rent', 'amount' => 3200, 'gross_amount' => 3200, 'due_date' => now()->addDays(10), 'status' => 'approved', 'created_by' => $ownerId],
        );

        Spending::query()->updateOrCreate(
            ['organization_id' => $org->id, 'branch_id' => $this->ctx['central']->id, 'title' => 'Equipment repair', 'spent_date' => now()->subDays(3)->toDateString()],
            ['category' => 'maintenance', 'amount' => 185, 'gross_amount' => 185, 'status' => 'paid', 'created_by' => $ownerId],
        );

        foreach (CashUp::query()->where('organization_id', $org->id)->get() as $cashUp) {
            FinanceIncomeEntry::query()->updateOrCreate(
                ['organization_id' => $org->id, 'reference_type' => CashUp::class, 'reference_id' => $cashUp->id],
                [
                    'branch_id' => $cashUp->branch_id,
                    'source' => FinanceIncomeSource::CashUp,
                    'title' => 'Cash up '.$cashUp->cashup_date?->format('d M').' '.$cashUp->shift?->value,
                    'net_amount' => $cashUp->revenueTotal(),
                    'gross_amount' => $cashUp->revenueTotal(),
                    'income_date' => $cashUp->cashup_date,
                ],
            );
        }
    }

    private function seedPayroll(): void
    {
        $org = $this->ctx['org'];
        $weekStart = now()->startOfWeek()->subWeek();

        $run = FinancePayrollRun::query()->updateOrCreate(
            ['organization_id' => $org->id, 'branch_id' => $this->ctx['dockside']->id, 'week_start' => $weekStart->toDateString()],
            [
                'week_end' => $weekStart->copy()->endOfWeek()->toDateString(),
                'payment_due_date' => $weekStart->copy()->endOfWeek()->addWeek()->toDateString(),
                'status' => FinanceStatus::Approved,
                'created_by' => $this->ctx['ava']->id,
            ],
        );

        foreach ($this->ctx['staff']->where('branch_id', $this->ctx['dockside']->id)->take(3) as $member) {
            $hours = 32 + random_int(0, 8);
            Wage::query()->updateOrCreate(
                ['organization_id' => $org->id, 'branch_id' => $this->ctx['dockside']->id, 'user_id' => $member->id, 'payroll_run_id' => $run->id],
                [
                    'hours_worked' => $hours,
                    'amount' => round($hours * (float) $member->hourly_rate, 2),
                    'status' => WageStatus::Pending->value,
                    'notes' => 'Week '.$weekStart->format('d M'),
                    'created_by' => $this->ctx['ava']->id,
                ],
            );
        }
    }

    private function seedCrm(): void
    {
        $org = $this->ctx['org'];
        $names = ['Alice Turner', 'Ben Hughes', 'Carla Mendez', 'David Kim', 'Emma Walsh', 'Frank Osei', 'Grace Liu', 'Henry Moss', 'Isla Grant', 'James Park', 'Katie Bell', 'Liam Fox', 'Maya Roy', 'Nina Cole', 'Oscar Webb', 'Paula Dean', 'Quinn Shaw', 'Rita Gomez', 'Sam Ellis', 'Tara Mills'];
        foreach ($names as $i => $name) {
            $customer = CrmCustomer::query()->updateOrCreate(
                ['organization_id' => $org->id, 'email' => Str::slug($name).'@example.test'],
                [
                    'branch_id' => $this->ctx['branches'][$i % 3]->id,
                    'name' => $name,
                    'phone' => '+44 7700 2'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'marketing_preferences' => ['email' => $i % 3 === 0],
                ],
            );
            if ($i < 8) {
                CrmCustomerVisit::query()->create([
                    'crm_customer_id' => $customer->id,
                    'organization_id' => $org->id,
                    'branch_id' => $customer->branch_id,
                    'visited_at' => now()->subDays(rand(1, 30)),
                    'spend_amount' => rand(15, 85),
                ]);
            }
        }
    }

    private function seedHr(): void
    {
        $org = $this->ctx['org'];
        $member = $this->ctx['staff']->first();

        LeaveRequest::query()->updateOrCreate(
            ['user_id' => $member->id, 'start_date' => now()->addDays(14)->toDateString()],
            ['organization_id' => $org->id, 'type' => LeaveType::Holiday, 'end_date' => now()->addDays(18)->toDateString(), 'status' => RequestStatus::Pending, 'reason' => 'Family holiday'],
        );

        LeaveRequest::query()->updateOrCreate(
            ['user_id' => $this->ctx['staff'][5]->id, 'start_date' => now()->subDays(10)->toDateString()],
            ['organization_id' => $org->id, 'type' => LeaveType::Sick, 'end_date' => now()->subDays(9)->toDateString(), 'status' => RequestStatus::Approved, 'reason' => 'Flu'],
        );

        foreach ($this->ctx['staff']->take(5) as $s) {
            StaffAvailability::query()->updateOrCreate(
                ['user_id' => $s->id, 'day_of_week' => 1],
                ['organization_id' => $org->id, 'branch_id' => $s->branch_id, 'start_time' => '09:00', 'end_time' => '22:00', 'is_available' => true],
            );
        }

        $shift = RotaShift::query()->where('organization_id', $org->id)->first();
        if ($shift) {
            ShiftSwapRequest::query()->updateOrCreate(
                ['organization_id' => $org->id, 'rota_shift_id' => $shift->id, 'requester_id' => $this->ctx['staff'][1]->id],
                ['target_user_id' => $this->ctx['staff'][2]->id, 'status' => RequestStatus::Pending, 'reason' => 'Medical appointment'],
            );
        }
    }

    private function seedBudgetsAndAlerts(): void
    {
        $org = $this->ctx['org'];
        $budget = Budget::query()->updateOrCreate(
            ['organization_id' => $org->id, 'year' => now()->year, 'name' => now()->format('F').' Operating Budget'],
            ['month' => now()->month, 'currency' => 'GBP', 'created_by' => $this->ctx['ava']->id],
        );

        foreach ([BudgetCategory::Revenue, BudgetCategory::FoodCost, BudgetCategory::Wages, BudgetCategory::Rent] as $cat) {
            BudgetLine::query()->updateOrCreate(
                ['budget_id' => $budget->id, 'category' => $cat->value],
                ['amount' => match ($cat) {
                    BudgetCategory::Revenue => 45000,
                    BudgetCategory::FoodCost => 12000,
                    BudgetCategory::Wages => 15000,
                    BudgetCategory::Rent => 9600,
                    default => 5000,
                }],
            );
        }

        BusinessAlert::query()->updateOrCreate(
            ['organization_id' => $org->id, 'alert_type' => AlertType::LowStock, 'title' => 'Low stock: Floor Cleaner'],
            ['message' => 'Floor Cleaner is below minimum stock at Dockside Kitchen.', 'status' => AlertStatus::Open, 'branch_id' => $this->ctx['dockside']->id],
        );
    }

    private function seedStocktakes(): void
    {
        $org = $this->ctx['org'];
        foreach ($this->ctx['branches'] as $bi => $branch) {
            for ($w = 0; $w < 2; $w++) {
                $weekStart = now()->startOfWeek()->subWeeks($w + 1);
                $stocktake = InventoryStocktake::query()->updateOrCreate(
                    ['organization_id' => $org->id, 'branch_id' => $branch->id, 'week_start' => $weekStart->toDateString()],
                    [
                        'week_end' => $weekStart->copy()->endOfWeek()->toDateString(),
                        'status' => InventoryStocktakeStatus::Completed,
                        'created_by' => $this->ctx['ava']->id,
                        'approved_at' => $weekStart->copy()->addDays(6),
                    ],
                );

                $items = InventoryItem::query()->where('branch_id', $branch->id)->limit(5)->get();
                foreach ($items as $item) {
                    InventoryStocktakeItem::query()->updateOrCreate(
                        ['inventory_stocktake_id' => $stocktake->id, 'inventory_item_id' => $item->id],
                        [
                            'system_qty' => $item->stock_total_pcs,
                            'counted_qty' => max(0, $item->stock_total_pcs - ($bi === 0 ? 2 : 0)),
                            'difference_qty' => $bi === 0 ? -2 : 0,
                        ],
                    );
                }
            }
        }
    }

    private function printCredentials(): void
    {
        $this->command?->info('');
        $this->command?->info('═══════════════════════════════════════════════════════');
        $this->command?->info('  Harbour Kitchen Group — realistic staging data');
        $this->command?->info('═══════════════════════════════════════════════════════');
        $this->command?->info('  Super Admin:    admin@totalcashpro.com / admin123');
        $this->command?->info('  Business Admin: ava@harbourkitchen.test / password');
        $this->command?->info('  Rider:          rider@harbourkitchen.test / password');
        $this->command?->info('  Staff:          *@harbourkitchen.test / password');
        $this->command?->info('  Staff PINs:     1001–1015 (unique, see seeder)');
        $this->command?->info('  Branches:       Dockside Kitchen · Harbour Central · Riverside');
        $this->command?->info('  Tills:          7 total (£100 opening float each)');
        $this->command?->info('  Kiosk:          /kiosk');
        $this->command?->info('═══════════════════════════════════════════════════════');
    }
}
