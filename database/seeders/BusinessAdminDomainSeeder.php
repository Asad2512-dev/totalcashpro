<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AttendanceLogType;
use App\Enums\CashUpShift;
use App\Enums\RoleSlug;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\AttendanceLog;
use App\Models\BranchKiosk;
use App\Models\Branch;
use App\Models\CashUp;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Role;
use App\Models\RotaGroup;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Models\Spending;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Support\Security\StaffPinHasher;

final class BusinessAdminDomainSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::query()->where('slug', 'harbour-kitchen-group')->first();
        if ($org === null) {
            $this->command?->warn('Harbour Kitchen Group not found — run DemoDataSeeder first.');

            return;
        }

        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $branches = $org->branches()->orderBy('id')->get();
        $dockside = $branches->firstWhere('name', 'Dockside') ?? $branches->last();
        $central = $branches->firstWhere('name', 'Harbour Central') ?? $branches->first();

        $org->update([
            'opens_at' => '08:00',
            'closes_at' => '23:00',
            'settings' => array_merge($org->settings ?? [], [
                'strict_rota_clockin' => '0',
                'strict_business_hours' => '0',
            ]),
        ]);

        // Reuse the DemoDataSeeder staff account (PIN 1000 is unique per org).
        $jamie = User::query()->updateOrCreate(
            ['email' => 'staff.harbour-kitchen-group@totalcashpro.test'],
            [
                'name' => 'Jamie Cole',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'pin_hash' => StaffPinHasher::hash('1000'),
                'hourly_rate' => 12.50,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $staffMembers = [
            ['name' => 'Priya Shah', 'email' => 'priya@harbourkitchen.test', 'pin' => '1001', 'rate' => 13.00, 'branch' => $dockside],
            ['name' => 'Marcus Lee', 'email' => 'marcus@harbourkitchen.test', 'pin' => '1002', 'rate' => 11.75, 'branch' => $dockside],
            ['name' => 'Sofia Reed', 'email' => 'sofia@harbourkitchen.test', 'pin' => '1122', 'rate' => 14.00, 'branch' => $central],
            ['name' => 'Noah Blake', 'email' => 'noah@harbourkitchen.test', 'pin' => '1234', 'rate' => 12.00, 'branch' => $central],
        ];

        $staff = collect([$jamie]);
        foreach ($staffMembers as $member) {
            $user = User::query()->updateOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $staffRole->id,
                    'organization_id' => $org->id,
                    'branch_id' => $member['branch']->id,
                    'pin_hash' => StaffPinHasher::hash($member['pin']),
                    'hourly_rate' => $member['rate'],
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );
            $staff->push($user);
        }

        $sectionDefs = [
            ['name' => 'Burgers', 'color' => '#563d7c'],
            ['name' => 'Fries', 'color' => '#0F766E'],
            ['name' => 'Front', 'color' => '#16A34A'],
            ['name' => 'Grill', 'color' => '#B45309'],
        ];

        $sections = collect();
        foreach ($sectionDefs as $def) {
            $sections->push(RotaSection::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'name' => $def['name'],
                ],
                ['color' => $def['color']],
            ));
        }

        $kitchenGroup = RotaGroup::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'name' => 'Kitchen',
            ],
            ['color' => '#007bff', 'display_order' => 1],
        );
        $frontGroup = RotaGroup::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'name' => 'Front of House',
            ],
            ['color' => '#16A34A', 'display_order' => 2],
        );

        DB::table('rota_group_user')->whereIn('user_id', $staff->pluck('id'))->delete();
        $kitchenGroup->users()->sync($staff->take(3)->pluck('id')->all());
        $frontGroup->users()->sync($staff->slice(3)->pluck('id')->all());

        $weekStart = now()->startOfWeek();
        RotaShift::query()
            ->where('organization_id', $org->id)
            ->whereBetween('shift_date', [$weekStart->toDateString(), $weekStart->copy()->endOfWeek()->toDateString()])
            ->delete();

        $dockStaff = $staff->where('branch_id', $dockside->id)->values();
        foreach ($dockStaff as $index => $member) {
            for ($d = 0; $d < 5; $d++) {
                $date = $weekStart->copy()->addDays($d)->toDateString();
                $section = $sections[$index % $sections->count()];

                $this->createShift($org, $dockside, $member, $section, $date, 'Morning', '09:00', '15:00');
                if ($d % 2 === 0) {
                    $this->createShift($org, $dockside, $member, $section, $date, 'Evening', '17:00', '22:00');
                }
            }
        }

        AttendanceLog::query()
            ->where('organization_id', $org->id)
            ->where('logged_at', '>=', now()->subDays(7)->startOfDay())
            ->delete();

        foreach ($dockStaff as $index => $member) {
            for ($d = 0; $d < 5; $d++) {
                $day = now()->startOfWeek()->addDays($d);
                AttendanceLog::query()->create([
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockIn->value,
                    'logged_at' => $day->copy()->setTime(9, $index * 5),
                ]);
                AttendanceLog::query()->create([
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'user_id' => $member->id,
                    'type' => AttendanceLogType::ClockOut->value,
                    'logged_at' => $day->copy()->setTime(15, 10 + $index),
                ]);
            }
        }

        $supplier = Supplier::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'name' => 'Fresh Produce Co',
            ],
            [
                'branch_id' => $dockside->id,
                'contact_name' => 'Helen Park',
                'email' => 'orders@freshproduce.test',
                'phone' => '+44 7700 111222',
                'address' => '12 Market Road, Brighton',
                'status' => 'active',
            ],
        );

        SupplierInvoice::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'supplier_id' => $supplier->id,
                'invoice_no' => 'FP-1001',
            ],
            [
                'branch_id' => $dockside->id,
                'invoice_date' => now()->subDays(4)->toDateString(),
                'due_date' => now()->addDays(10)->toDateString(),
                'amount' => 248.50,
                'description' => 'Weekly produce delivery',
                'status' => SupplierInvoiceStatus::Pending->value,
            ],
        );

        $category = InventoryCategory::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'name' => 'Packaging',
            ],
            ['description' => 'Cups, lids and boxes'],
        );

        InventoryItem::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'name' => 'Paper Cups 12oz',
            ],
            [
                'category_id' => $category->id,
                'packaging' => 'box',
                'pcs_per_box' => 50,
                'stock_total_pcs' => 180,
                'stock_limit' => 40,
            ],
        );

        foreach ([CashUpShift::Morning, CashUpShift::Evening] as $shift) {
            CashUp::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'cashup_date' => now()->toDateString(),
                    'shift' => $shift->value,
                ],
                [
                    'coins_total' => 12.40,
                    'coins_detail' => [['coin' => '£1', 'qty' => 8], ['coin' => '20p', 'qty' => 12]],
                    'notes_total' => 95.00,
                    'notes_detail' => [['note' => '£20', 'qty' => 3, 'is_qty' => true], ['note' => '£10', 'qty' => 2, 'is_qty' => true], ['note' => 'Extra Coin (Float)', 'amount' => 5, 'is_qty' => false]],
                    'cards_total' => 140.00,
                    'cards_detail' => [['payment_type' => 'Card Machine 1', 'type' => 'machine', 'amount' => 150], ['payment_type' => 'Refunds', 'type' => 'refund', 'amount' => 10]],
                    'expenses_total' => 8.50,
                    'expenses_detail' => [['description' => 'Milk', 'amount' => 8.50]],
                    'online_orders_total' => 64.00,
                    'online_orders_detail' => [['platform' => 'Uber Eats', 'amount' => 34], ['platform' => 'Deliveroo', 'amount' => 30]],
                    'platform_deductions_total' => 6.40,
                    'platform_deductions_detail' => [['platform' => 'Uber Eats', 'amount' => 3.40], ['platform' => 'Deliveroo', 'amount' => 3.00]],
                    'created_by' => $org->owner_user_id,
                ],
            );
        }

        foreach ($dockStaff->take(2) as $member) {
            Wage::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'branch_id' => $dockside->id,
                    'user_id' => $member->id,
                    'notes' => 'Week seed wage',
                ],
                [
                    'hours_worked' => 28,
                    'amount' => round(28 * (float) $member->hourly_rate, 2),
                    'status' => WageStatus::Pending->value,
                    'created_by' => $org->owner_user_id,
                ],
            );
        }

        Bill::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'title' => 'Monthly rent',
            ],
            [
                'vendor' => 'Harbour Properties',
                'category' => 'rent',
                'amount' => 2400.00,
                'net_amount' => 2400.00,
                'vat_amount' => 0,
                'gross_amount' => 2400.00,
                'due_date' => now()->addDays(10)->toDateString(),
                'status' => 'approved',
                'created_by' => $org->owner_user_id,
            ],
        );

        Bill::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $central->id,
                'title' => 'Business insurance',
            ],
            [
                'vendor' => 'CoverSure',
                'category' => 'insurance',
                'amount' => 185.00,
                'net_amount' => 154.17,
                'vat_amount' => 30.83,
                'gross_amount' => 185.00,
                'due_date' => now()->addDays(5)->toDateString(),
                'status' => 'approved',
                'created_by' => $org->owner_user_id,
            ],
        );

        Spending::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'title' => 'Cleaning supplies',
                'spent_date' => now()->subDays(2)->toDateString(),
            ],
            [
                'category' => 'supplies',
                'amount' => 46.50,
                'net_amount' => 38.75,
                'vat_amount' => 7.75,
                'gross_amount' => 46.50,
                'status' => 'paid',
                'payment_method' => 'card',
                'notes' => 'Floor cleaner and cloths',
                'created_by' => $org->owner_user_id,
            ],
        );

        Spending::query()->updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $dockside->id,
                'title' => 'Social media ads',
                'spent_date' => now()->subDays(6)->toDateString(),
            ],
            [
                'category' => 'marketing',
                'amount' => 120.00,
                'net_amount' => 100.00,
                'vat_amount' => 20.00,
                'gross_amount' => 120.00,
                'status' => 'paid',
                'payment_method' => 'bank',
                'created_by' => $org->owner_user_id,
            ],
        );

        foreach ($branches as $branch) {
            BranchKiosk::query()->firstOrCreate(
                ['branch_id' => $branch->id],
                [
                    'organization_id' => $org->id,
                    'name' => $branch->name.' Kiosk',
                    'token' => \Illuminate\Support\Str::random(64),
                    'welcome_message' => 'Welcome — enter your PIN to clock in or out.',
                    'show_photos' => true,
                    'is_enabled' => true,
                ],
            );
        }

        $this->command?->info('Business admin domain data seeded for Harbour Kitchen Group (Dockside-focused).');
        $this->command?->info('Staff PINs: 1000 Jamie, 1001 Priya, 1002 Marcus, 1122 Sofia, 1234 Noah');
    }

    private function createShift(
        Organization $org,
        Branch $branch,
        User $member,
        RotaSection $section,
        string $date,
        string $type,
        string $start,
        string $end,
    ): void {
        $startAt = Carbon::parse($date.' '.$start);
        $endAt = Carbon::parse($date.' '.$end);
        if ($endAt->lte($startAt)) {
            $endAt->addDay();
        }

        RotaShift::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $member->id,
            'rota_section_id' => $section->id,
            'shift_date' => $date,
            'start_time' => $startAt,
            'end_time' => $endAt,
            'shift_type' => $type,
        ]);
    }
}
