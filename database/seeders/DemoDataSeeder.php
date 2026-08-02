<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AccessRequestStatus;
use App\Enums\CouponType;
use App\Enums\DiscountType;
use App\Enums\OrganizationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PublishStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\AccessRequest;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds realistic Super Admin sample data so every module can be exercised.
 * Safe to re-run: clears prior demo rows keyed by known emails/slugs first.
 */
final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->first();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->first();
        $superAdmin = User::query()->where('email', 'admin@totalcashpro.com')->firstOrFail();
        $basic = Plan::query()->where('slug', 'basic')->firstOrFail();
        $pro = Plan::query()->where('slug', 'professional')->firstOrFail();

        $businesses = [
            [
                'name' => 'Harbour Kitchen Group',
                'slug' => 'harbour-kitchen-group',
                'email' => 'ops@harbourkitchen.test',
                'owner' => ['name' => 'Ava Morgan', 'email' => 'ava@harbourkitchen.test'],
                'status' => OrganizationStatus::Active,
                'plan' => $pro,
                'sub' => SubscriptionStatus::Active,
                'branches' => [['name' => 'Harbour Central', 'city' => 'London'], ['name' => 'Dockside', 'city' => 'Brighton']],
                'paid_months' => 5,
            ],
            [
                'name' => 'Oak Street Bakery',
                'slug' => 'oak-street-bakery',
                'email' => 'hello@oakstreet.test',
                'owner' => ['name' => 'Tom Reed', 'email' => 'tom@oakstreet.test'],
                'status' => OrganizationStatus::Trial,
                'plan' => $basic,
                'sub' => SubscriptionStatus::Trialing,
                'branches' => [['name' => 'Main Bakery', 'city' => 'Manchester']],
                'paid_months' => 0,
            ],
            [
                'name' => 'Northbridge Retail',
                'slug' => 'northbridge-retail',
                'email' => 'finance@northbridge.test',
                'owner' => ['name' => 'Sara Khan', 'email' => 'sara@northbridge.test'],
                'status' => OrganizationStatus::Active,
                'plan' => $basic,
                'sub' => SubscriptionStatus::Active,
                'branches' => [['name' => 'Northbridge HQ', 'city' => 'Leeds'], ['name' => 'City Market', 'city' => 'York']],
                'paid_months' => 3,
            ],
            [
                'name' => 'Riverbend Cafe',
                'slug' => 'riverbend-cafe',
                'email' => 'team@riverbend.test',
                'owner' => ['name' => 'James Cole', 'email' => 'james@riverbend.test'],
                'status' => OrganizationStatus::Suspended,
                'plan' => $basic,
                'sub' => SubscriptionStatus::Suspended,
                'branches' => [['name' => 'Riverbend', 'city' => 'Bristol']],
                'paid_months' => 1,
            ],
            [
                'name' => 'Cedar Hospitality',
                'slug' => 'cedar-hospitality',
                'email' => 'billing@cedar.test',
                'owner' => ['name' => 'Mia Chen', 'email' => 'mia@cedar.test'],
                'status' => OrganizationStatus::Cancelled,
                'plan' => $pro,
                'sub' => SubscriptionStatus::Cancelled,
                'branches' => [['name' => 'Cedar House', 'city' => 'Edinburgh']],
                'paid_months' => 2,
            ],
            [
                'name' => 'Summit Pantry',
                'slug' => 'summit-pantry',
                'email' => 'admin@summitpantry.test',
                'owner' => ['name' => 'Noah Price', 'email' => 'noah@summitpantry.test'],
                'status' => OrganizationStatus::Pending,
                'plan' => $basic,
                'sub' => SubscriptionStatus::Expired,
                'branches' => [['name' => 'Summit Store', 'city' => 'Cardiff']],
                'paid_months' => 0,
            ],
            [
                'name' => 'Greenfield Markets',
                'slug' => 'greenfield-markets',
                'email' => 'owners@greenfield.test',
                'owner' => ['name' => 'Ellie Brooks', 'email' => 'ellie@greenfield.test'],
                'status' => OrganizationStatus::Active,
                'plan' => $pro,
                'sub' => SubscriptionStatus::Lifetime,
                'branches' => [['name' => 'Greenfield Main', 'city' => 'Birmingham'], ['name' => 'Westside', 'city' => 'Coventry']],
                'paid_months' => 4,
            ],
            [
                'name' => 'Lakeside Deli',
                'slug' => 'lakeside-deli',
                'email' => 'info@lakeside.test',
                'owner' => ['name' => 'Chris Patel', 'email' => 'chris@lakeside.test'],
                'status' => OrganizationStatus::Active,
                'plan' => $basic,
                'sub' => SubscriptionStatus::Free,
                'branches' => [['name' => 'Lakeside Counter', 'city' => 'Oxford']],
                'paid_months' => 0,
            ],
        ];

        foreach ($businesses as $index => $item) {
            $owner = User::query()->updateOrCreate(
                ['email' => $item['owner']['email']],
                [
                    'name' => $item['owner']['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole?->id,
                    'status' => 'active',
                    'email_verified_at' => now()->subDays(20 - $index),
                ],
            );

            $organization = Organization::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'phone' => '+44 7700 900'.str_pad((string) ($index + 10), 2, '0', STR_PAD_LEFT),
                    'country' => 'GB',
                    'currency' => 'GBP',
                    'timezone' => 'Europe/London',
                    'owner_user_id' => $owner->id,
                    'status' => $item['status'],
                    'trial_starts_at' => $item['sub'] === SubscriptionStatus::Trialing ? now()->subDays(3) : null,
                    'trial_ends_at' => $item['sub'] === SubscriptionStatus::Trialing ? now()->addDays(11) : null,
                    'created_at' => now()->subDays(40 - ($index * 4)),
                    'updated_at' => now()->subDays($index),
                ],
            );

            $owner->update(['organization_id' => $organization->id]);

            if ($staffRole) {
                $staff = User::query()->updateOrCreate(
                    ['email' => 'staff.'.$item['slug'].'@totalcashpro.test'],
                    [
                        'name' => 'Staff · '.$item['name'],
                        'password' => Hash::make('password'),
                        'role_id' => $staffRole->id,
                        'organization_id' => $organization->id,
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ],
                );
            }

            foreach ($item['branches'] as $bIndex => $branchData) {
                $branch = Branch::query()->updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'slug' => Str::slug($branchData['name']),
                    ],
                    [
                        'name' => $branchData['name'],
                        'city' => $branchData['city'],
                        'address' => $branchData['city'].' High Street',
                        'status' => $organization->status === OrganizationStatus::Suspended ? 'closed' : 'open',
                        'staff_count' => 3 + $bIndex,
                    ],
                );

                if (isset($staff) && $bIndex === 0) {
                    $staff->update(['branch_id' => $branch->id]);
                }
            }

            $subscription = Subscription::query()->updateOrCreate(
                ['organization_id' => $organization->id],
                [
                    'plan_id' => $item['plan']->id,
                    'status' => $item['sub'],
                    'starts_at' => now()->subMonths(max(1, $item['paid_months'] ?: 1)),
                    'trial_starts_at' => $item['sub'] === SubscriptionStatus::Trialing ? now()->subDays(3) : null,
                    'trial_ends_at' => $item['sub'] === SubscriptionStatus::Trialing ? now()->addDays(11) : null,
                    'current_period_start' => now()->startOfMonth(),
                    'current_period_end' => match ($item['sub']) {
                        SubscriptionStatus::Lifetime => null,
                        SubscriptionStatus::Trialing => now()->addDays(11),
                        SubscriptionStatus::Expired, SubscriptionStatus::Cancelled => now()->subDays(5),
                        default => now()->endOfMonth(),
                    },
                    'cancelled_at' => $item['sub'] === SubscriptionStatus::Cancelled ? now()->subDays(5) : null,
                    'ends_at' => in_array($item['sub'], [SubscriptionStatus::Cancelled, SubscriptionStatus::Expired], true)
                        ? now()->subDays(5)
                        : null,
                ],
            );

            for ($m = 0; $m < $item['paid_months']; $m++) {
                $paidAt = now()->startOfMonth()->subMonths($m)->addDays(2);
                $amount = (float) $item['plan']->price_monthly;
                $invoice = Invoice::query()->updateOrCreate(
                    ['number' => 'INV-'.strtoupper(substr($item['slug'], 0, 4)).'-'.($m + 1)],
                    [
                        'organization_id' => $organization->id,
                        'subscription_id' => $subscription->id,
                        'amount' => $amount,
                        'currency' => 'GBP',
                        'status' => 'paid',
                        'due_at' => $paidAt->copy()->subDay(),
                        'paid_at' => $paidAt,
                        'created_at' => $paidAt,
                        'updated_at' => $paidAt,
                    ],
                );

                Payment::query()->updateOrCreate(
                    ['provider_reference' => 'PAY-'.strtoupper(substr($item['slug'], 0, 4)).'-'.($m + 1)],
                    [
                        'organization_id' => $organization->id,
                        'invoice_id' => $invoice->id,
                        'amount' => $amount,
                        'currency' => 'GBP',
                        'provider' => 'manual',
                        'status' => PaymentStatus::Paid,
                        'method' => 'card',
                        'paid_at' => $paidAt,
                        'created_at' => $paidAt,
                        'updated_at' => $paidAt,
                    ],
                );
            }

            if ($index === 2) {
                Payment::query()->updateOrCreate(
                    ['provider_reference' => 'PAY-FAIL-NB-1'],
                    [
                        'organization_id' => $organization->id,
                        'amount' => (float) $basic->price_monthly,
                        'currency' => 'GBP',
                        'provider' => 'manual',
                        'status' => PaymentStatus::Failed,
                        'method' => 'card',
                        'paid_at' => null,
                    ],
                );
            }
        }

        Coupon::query()->updateOrCreate(
            ['code' => 'LAUNCH20'],
            [
                'type' => CouponType::Percentage,
                'value' => 20,
                'max_uses' => 100,
                'used_count' => 12,
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(3),
                'status' => 'active',
                'plan_id' => $basic->id,
            ],
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'FLAT10'],
            [
                'type' => CouponType::Fixed,
                'value' => 10,
                'max_uses' => 50,
                'used_count' => 4,
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addMonth(),
                'status' => 'active',
            ],
        );

        $harbour = Organization::query()->where('slug', 'harbour-kitchen-group')->first();
        if ($harbour) {
            Discount::query()->updateOrCreate(
                ['organization_id' => $harbour->id, 'notes' => 'Founding partner rate'],
                [
                    'type' => DiscountType::CustomPrice,
                    'grant_type' => 'custom_price',
                    'value' => null,
                    'custom_price' => 39.00,
                    'status' => 'active',
                    'starts_at' => now()->subMonths(2),
                    'ends_at' => now()->addYear(),
                ],
            );
        }

        $lakeside = Organization::query()->where('slug', 'lakeside-deli')->first();
        if ($lakeside) {
            Discount::query()->updateOrCreate(
                ['organization_id' => $lakeside->id, 'notes' => 'Lifetime access'],
                [
                    'type' => DiscountType::Percentage,
                    'grant_type' => 'lifetime',
                    'value' => 100,
                    'custom_price' => 0,
                    'status' => 'active',
                    'starts_at' => now()->subMonth(),
                    'ends_at' => null,
                ],
            );
        }

        AccessRequest::query()->updateOrCreate(
            ['email' => 'newbiz@coastalcafe.test'],
            [
                'business_name' => 'Coastal Cafe Co',
                'owner_name' => 'Priya Shah',
                'phone' => '+44 7700 90100',
                'country' => 'GB',
                'business_type' => 'Cafe',
                'number_of_employees' => '6-20',
                'selected_plan' => SubscriptionPlan::Professional,
                'status' => AccessRequestStatus::Pending,
                'additional_notes' => 'Looking to start next month across two locations.',
            ],
        );

        AccessRequest::query()->updateOrCreate(
            ['email' => 'rejected@oldmill.test'],
            [
                'business_name' => 'Old Mill Foods',
                'owner_name' => 'Dan Foster',
                'phone' => '+44 7700 90101',
                'country' => 'GB',
                'business_type' => 'Retail',
                'number_of_employees' => '1-5',
                'selected_plan' => SubscriptionPlan::Basic,
                'status' => AccessRequestStatus::Rejected,
                'admin_notes' => 'Incomplete business details.',
                'reviewed_by' => $superAdmin->id,
                'reviewed_at' => now()->subDays(2),
            ],
        );

        $tickets = [
            ['org' => 'harbour-kitchen-group', 'subject' => 'Need help connecting a second till', 'priority' => TicketPriority::High, 'status' => TicketStatus::Open],
            ['org' => 'oak-street-bakery', 'subject' => 'Trial access question', 'priority' => TicketPriority::Normal, 'status' => TicketStatus::Pending],
            ['org' => 'northbridge-retail', 'subject' => 'Invoice copy for March', 'priority' => TicketPriority::Low, 'status' => TicketStatus::Closed],
        ];

        foreach ($tickets as $tIndex => $ticketData) {
            $org = Organization::query()->where('slug', $ticketData['org'])->first();
            $ticket = SupportTicket::query()->updateOrCreate(
                ['ticket_number' => 'TCP-'.str_pad((string) ($tIndex + 1001), 4, '0', STR_PAD_LEFT)],
                [
                    'organization_id' => $org?->id,
                    'user_id' => $superAdmin->id,
                    'subject' => $ticketData['subject'],
                    'body' => 'Customer asked about: '.$ticketData['subject'],
                    'priority' => $ticketData['priority'],
                    'status' => $ticketData['status'],
                ],
            );

            SupportTicketReply::query()->updateOrCreate(
                [
                    'support_ticket_id' => $ticket->id,
                    'body' => 'Thanks for reaching out — we are looking into this now.',
                ],
                [
                    'user_id' => $superAdmin->id,
                    'is_internal' => false,
                ],
            );
        }

        Announcement::query()->updateOrCreate(
            ['title' => 'April platform maintenance'],
            [
                'body' => 'Scheduled maintenance this Sunday 02:00–04:00 BST.',
                'audience' => 'everyone',
                'channel' => 'both',
                'status' => PublishStatus::Published,
                'scheduled_at' => now()->addDays(3),
                'published_at' => now()->subDay(),
            ],
        );

        Announcement::query()->updateOrCreate(
            ['title' => 'Professional plan feature drop'],
            [
                'body' => 'New branch reports are rolling out for Professional accounts.',
                'audience' => 'professional',
                'channel' => 'in_app',
                'target_plan_slug' => 'professional',
                'status' => PublishStatus::Draft,
                'scheduled_at' => now()->addWeek(),
            ],
        );

        AppNotification::query()->updateOrCreate(
            ['user_id' => $superAdmin->id, 'title' => 'New business request waiting'],
            [
                'body' => 'Coastal Cafe Co submitted an access request.',
                'type' => 'alert',
                'priority' => 'high',
                'read_at' => null,
            ],
        );

        AppNotification::query()->updateOrCreate(
            ['user_id' => $superAdmin->id, 'title' => 'Payment failed for Northbridge'],
            [
                'body' => 'A card payment failed and needs follow-up.',
                'type' => 'info',
                'priority' => 'normal',
                'read_at' => now()->subHour(),
            ],
        );

        ContactMessage::query()->updateOrCreate(
            ['email' => 'hello@prospect.test', 'subject' => 'Enterprise pricing question'],
            [
                'name' => 'Alex Rivera',
                'message' => 'Do you support 40+ branches and custom SSO?',
            ],
        );

        ActivityLog::query()->create([
            'actor_id' => $superAdmin->id,
            'actor_name' => $superAdmin->name,
            'event' => 'demo.seeded',
            'description' => 'Demo Super Admin dataset seeded',
            'properties' => ['source' => 'DemoDataSeeder'],
            'created_at' => now(),
        ]);

        AuditLog::query()->create([
            'user_id' => $superAdmin->id,
            'action' => 'demo.seeded',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'DemoDataSeeder',
            'new_values' => ['organizations' => count($businesses)],
            'created_at' => now(),
        ]);
    }
}
