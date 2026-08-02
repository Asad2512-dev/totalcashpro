<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\OrganizationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PublishStatus;
use App\Enums\SubscriptionStatus;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\CmsFaq;
use App\Models\CmsFeature;
use App\Models\CmsHeroSection;
use App\Models\CmsPage;
use App\Models\CmsTestimonial;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\EmailTemplate;
use App\Models\MediaAsset;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SuperAdminListingService implements ServiceInterface
{
    /**
     * @var array<string, array{title: string, description: string, active: string, action?: string, layout?: string, showFilters?: bool}>
     */
    private array $meta = [
        'businesses' => ['title' => 'Businesses', 'description' => 'Customer businesses with owners, plans and branches.', 'active' => 'businesses', 'action' => 'Add Business', 'createRoute' => 'super-admin.organizations.create', 'layout' => 'businesses'],
        'organizations' => ['title' => 'Businesses', 'description' => 'Customer businesses with owners, plans and branches.', 'active' => 'businesses', 'action' => 'Add Business', 'createRoute' => 'super-admin.organizations.create', 'layout' => 'businesses'],
        'business-requests' => ['title' => 'Business Requests', 'description' => 'Inbound for customer access requests.', 'active' => 'business-requests'],
        'branches' => ['title' => 'Businesses', 'description' => 'Manage branches from each business detail page.', 'active' => 'businesses', 'action' => 'Add Business', 'createRoute' => 'super-admin.organizations.create', 'layout' => 'businesses'],
        'users' => ['title' => 'Users', 'description' => 'Platform users across Super Admin, Admin and Staff roles.', 'active' => 'users', 'action' => 'Invite User', 'createRoute' => 'super-admin.users.create', 'layout' => 'users'],
        'plans' => ['title' => 'Plans', 'description' => 'Subscription plan catalogue for Basic, Professional and future Enterprise.', 'active' => 'plans', 'action' => 'Create Plan', 'createRoute' => 'super-admin.plans.create', 'layout' => 'plans', 'showFilters' => false],
        'subscriptions' => ['title' => 'Subscriptions', 'description' => 'Active, trial and past-due subscriptions.', 'active' => 'subscriptions', 'action' => 'Create Subscription', 'createRoute' => 'super-admin.subscriptions.create'],
        'coupons' => ['title' => 'Coupons', 'description' => 'Promotional coupon codes.', 'active' => 'coupons', 'action' => 'Create Coupon', 'createRoute' => 'super-admin.coupons.create'],
        'discounts' => ['title' => 'Discounts', 'description' => 'Business-specific discounts and custom pricing.', 'active' => 'discounts', 'action' => 'Add Discount', 'createRoute' => 'super-admin.discounts.create'],
        'trials' => ['title' => 'Trials', 'description' => 'Trial accounts and conversion tracking.', 'active' => 'trials'],
        'payments' => ['title' => 'Payments', 'description' => 'Payment attempts and invoices.', 'active' => 'payments', 'action' => 'Record Payment', 'createRoute' => 'super-admin.payments.create'],
        'revenue' => ['title' => 'Revenue', 'description' => 'Revenue overview from paid payments.', 'active' => 'revenue', 'showFilters' => false],
        'analytics' => ['title' => 'Analytics', 'description' => 'Growth metrics calculated from live data.', 'active' => 'analytics', 'showFilters' => false],
        'support' => ['title' => 'Support Tickets', 'description' => 'Customer support inbox.', 'active' => 'support', 'action' => 'New Ticket', 'createRoute' => 'super-admin.support.create'],
        'announcements' => ['title' => 'Announcements', 'description' => 'In-app and email announcements.', 'active' => 'announcements', 'action' => 'Create Announcement', 'createRoute' => 'super-admin.announcements.create'],
        'notifications' => ['title' => 'Notifications', 'description' => 'System and admin notifications.', 'active' => 'notifications', 'action' => 'Create Notification', 'createRoute' => 'super-admin.notifications.create'],
        'email-templates' => ['title' => 'Email Templates', 'description' => 'Transactional and marketing email templates.', 'active' => 'email-templates', 'action' => 'New Template', 'createRoute' => 'super-admin.email-templates.create'],
        'media' => ['title' => 'Media Library', 'description' => 'Uploaded assets for CMS and product screenshots.', 'active' => 'media', 'action' => 'Upload', 'createRoute' => 'super-admin.media.create'],
        'contact-messages' => ['title' => 'Contact Messages', 'description' => 'Messages submitted from the marketing contact form.', 'active' => 'contact-messages'],
        'audit-logs' => ['title' => 'Audit Logs', 'description' => 'Sensitive action history.', 'active' => 'audit-logs'],
        'activity' => ['title' => 'Activity Logs', 'description' => 'Recent platform activity stream.', 'active' => 'activity'],
        'system-health' => ['title' => 'System Health', 'description' => 'Infrastructure checks based on application state.', 'active' => 'system-health', 'showFilters' => false],
        'settings' => ['title' => 'Settings', 'description' => 'Platform configuration for brand, SEO, email and payments.', 'active' => 'settings', 'layout' => 'settings', 'showFilters' => false],
        'roles' => ['title' => 'Roles', 'description' => 'Super Admin, Admin and Staff role definitions.', 'active' => 'roles', 'action' => 'Create Role', 'createRoute' => 'super-admin.roles.create'],
        'permissions' => ['title' => 'Permissions', 'description' => 'Granular permission matrix.', 'active' => 'permissions', 'action' => 'Create Permission', 'createRoute' => 'super-admin.permissions.create'],
        'profile' => ['title' => 'Profile', 'description' => 'Your Super Admin profile.', 'active' => 'profile', 'action' => 'Edit Profile', 'createRoute' => 'super-admin.profile.edit', 'showFilters' => false],
        'cms.pages' => ['title' => 'CMS Pages', 'description' => 'Marketing pages managed from the CMS.', 'active' => 'pages', 'action' => 'New Page', 'createRoute' => 'super-admin.cms.pages.create'],
        'cms.hero' => ['title' => 'Hero Sections', 'description' => 'Homepage hero content controls.', 'active' => 'hero', 'action' => 'Add Hero', 'createRoute' => 'super-admin.cms.hero.create'],
        'cms.features' => ['title' => 'Features', 'description' => 'Feature grid content for marketing pages.', 'active' => 'features', 'action' => 'Add Feature', 'createRoute' => 'super-admin.cms.features.create'],
        'cms.pricing' => ['title' => 'Pricing', 'description' => 'Plan cards shown on the marketing site.', 'active' => 'pricing', 'action' => 'Manage Plans', 'createRoute' => 'super-admin.plans'],
        'cms.testimonials' => ['title' => 'Testimonials', 'description' => 'Customer quotes on the marketing site.', 'active' => 'testimonials', 'action' => 'Add Testimonial', 'createRoute' => 'super-admin.cms.testimonials.create'],
        'cms.faq' => ['title' => 'FAQ', 'description' => 'Frequently asked questions content.', 'active' => 'faq', 'action' => 'Add FAQ', 'createRoute' => 'super-admin.cms.faq.create'],
        'cms.contact' => ['title' => 'Contact CMS', 'description' => 'Contact page content from settings.', 'active' => 'contact'],
        'cms.footer' => ['title' => 'Footer', 'description' => 'Footer links and brand copy from settings.', 'active' => 'footer'],
    ];

    public function __construct(
        private readonly OrganizationRepositoryInterface $organizations,
        private readonly PlanRepositoryInterface $plans,
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly PaymentRepositoryInterface $payments,
        private readonly DashboardAnalyticsService $analytics,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function page(string $key, Request $request): array
    {
        if (! isset($this->meta[$key])) {
            throw new NotFoundHttpException('Super Admin page not found.');
        }

        $config = $this->meta[$key];
        $filters = [
            'search' => $request->string('search')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'sort' => $request->string('sort')->toString() ?: null,
            'direction' => $request->string('direction')->toString() ?: null,
        ];

        $payload = match ($key) {
            'businesses', 'organizations', 'branches' => $this->businesses($filters),
            'business-requests' => $this->businessRequests($filters),
            'users' => $this->users($filters),
            'plans' => $this->plans(),
            'subscriptions' => $this->subscriptionsTable($filters),
            'coupons' => $this->coupons($filters),
            'discounts' => $this->discounts($filters),
            'trials' => $this->trials($filters),
            'payments' => $this->paymentsTable($filters),
            'revenue' => $this->revenue(),
            'analytics' => $this->analyticsPage(),
            'support' => $this->support($filters),
            'announcements' => $this->announcements($filters),
            'notifications' => $this->notifications(),
            'email-templates' => $this->emailTemplates(),
            'media' => $this->media(),
            'contact-messages' => $this->contactMessages($filters),
            'audit-logs' => $this->auditLogs(),
            'activity' => $this->activity(),
            'system-health' => $this->systemHealth(),
            'settings' => $this->settings(),
            'roles' => $this->roles(),
            'permissions' => $this->permissions(),
            'profile' => $this->profile(),
            'cms.pages' => $this->cmsPages(),
            'cms.hero' => $this->cmsHero(),
            'cms.features' => $this->cmsFeatures(),
            'cms.pricing' => $this->cmsPricing(),
            'cms.testimonials' => $this->cmsTestimonials(),
            'cms.faq' => $this->cmsFaqs(),
            'cms.contact' => $this->cmsContact(),
            'cms.footer' => $this->cmsFooter(),
            default => ['columns' => [], 'rows' => []],
        };

        return array_merge([
            'title' => $config['title'],
            'description' => $config['description'],
            'active' => $config['active'],
            'actionLabel' => $config['action'] ?? null,
            'createRoute' => $config['createRoute'] ?? null,
            'layout' => $config['layout'] ?? 'table',
            'showFilters' => $config['showFilters'] ?? true,
            'columns' => [],
            'rows' => [],
            'records' => [],
            'planCards' => [],
            'settingsTabs' => [],
            'paginator' => null,
            'revenueBars' => [],
            'growthBars' => [],
            'rawHtml' => false,
        ], $payload);
    }

    /**
     * @param  list<array{url: string, label: string}>  $links
     */
    private function rowActions(array $links, ?string $deleteUrl = null): string
    {
        return view('admin.partials.row-actions', [
            'links' => $links,
            'deleteUrl' => $deleteUrl,
        ])->render();
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function businesses(array $filters): array
    {
        $paginator = $this->organizations->paginateFiltered($filters);

        return [
            'records' => $paginator->getCollection()->map(fn ($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'plan' => $org->currentSubscription?->plan?->name ?? '—',
                'status' => $org->status instanceof OrganizationStatus ? $org->status->label() : ucfirst((string) $org->status),
                'owner' => $org->owner?->name ?? '—',
                'branches' => (string) ($org->branches_count ?? $org->branches()->count()),
                'created' => $org->created_at?->format('d M Y') ?? '—',
            ])->all(),
            'paginator' => $paginator,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function businessRequests(array $filters): array
    {
        $query = \App\Models\AccessRequest::query()->latest();

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('business_name', 'like', $search)
                    ->orWhere('owner_name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Business', 'Owner', 'Email', 'Plan', 'Status', 'Submitted', ''],
            'rows' => $paginator->getCollection()->map(fn (\App\Models\AccessRequest $request) => [
                $request->business_name,
                $request->owner_name,
                $request->email,
                $request->selected_plan instanceof \BackedEnum ? $request->selected_plan->value : (string) $request->selected_plan,
                $request->status instanceof \BackedEnum ? $request->status->value : (string) $request->status,
                $request->created_at?->format('d M Y') ?? '—',
                view('admin.partials.row-link', [
                    'url' => route('super-admin.business-requests.show', $request),
                    'label' => 'View',
                ])->render(),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function contactMessages(array $filters): array
    {
        $query = \App\Models\ContactMessage::query()->latest();

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('subject', 'like', $search);
            });
        }

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Name', 'Email', 'Subject', 'Received', ''],
            'rows' => $paginator->getCollection()->map(fn (\App\Models\ContactMessage $message) => [
                $message->name,
                $message->email,
                $message->subject,
                $message->created_at?->format('d M Y') ?? '—',
                view('admin.partials.row-link', [
                    'url' => route('super-admin.contact-messages.show', $message),
                    'label' => 'View',
                ])->render(),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function users(array $filters): array
    {
        $query = User::query()->with('role');

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', $search)->orWhere('email', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort = in_array($filters['sort'] ?? null, ['name', 'created_at', 'status'], true) ? $filters['sort'] : 'created_at';
        $query->orderBy($sort, ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc');

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'records' => $paginator->getCollection()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->name ?? '—',
                'status' => ucfirst($user->status),
                'created' => $user->created_at?->format('d M Y') ?? '—',
            ])->all(),
            'paginator' => $paginator,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function plans(): array
    {
        $plans = \App\Models\Plan::query()->orderBy('sort_order')->orderBy('id')->get();

        return [
            'planCards' => $plans->map(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'badge' => $plan->badge ?? $plan->name,
                'price' => $plan->formattedPrice(),
                'period' => $plan->formattedPrice() === 'Custom' ? '' : '/'.$plan->billing_interval,
                'description' => $plan->description ?? '',
                'features' => $plan->features ?? [],
                'featured' => (bool) $plan->is_featured,
                'is_active' => (bool) $plan->is_active,
            ])->all(),
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function subscriptionsTable(array $filters): array
    {
        $paginator = $this->subscriptions->paginateFiltered($filters);

        return [
            'columns' => ['Business', 'Plan', 'Cycle', 'Next invoice', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (Subscription $subscription) => [
                $subscription->organization?->name ?? '—',
                $subscription->plan?->name ?? '—',
                ucfirst($subscription->plan?->billing_interval ?? 'monthly'),
                $subscription->current_period_end?->format('d M Y') ?? '—',
                $subscription->status instanceof SubscriptionStatus
                    ? $subscription->status->label()
                    : ucfirst((string) $subscription->status),
                $this->rowActions([
                    ['url' => route('super-admin.subscriptions.show', $subscription), 'label' => 'Manage'],
                ]),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function coupons(array $filters): array
    {
        $query = Coupon::query()->latest();

        if (! empty($filters['search'])) {
            $query->where('code', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Code', 'Discount', 'Usage', 'Expires', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (Coupon $coupon) => [
                $coupon->code,
                $coupon->discountLabel(),
                $coupon->usageLabel(),
                $coupon->expires_at?->format('d M Y') ?? '—',
                ucfirst($coupon->status),
                $this->rowActions([
                    ['url' => route('super-admin.coupons.edit', $coupon), 'label' => 'Edit'],
                ], route('super-admin.coupons.destroy', $coupon)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function discounts(array $filters): array
    {
        $query = Discount::query()->with('organization')->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Business', 'Discount', 'Custom Price', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (Discount $discount) => [
                $discount->organization?->name ?? '—',
                $discount->discountLabel(),
                $discount->customPriceLabel(),
                ucfirst($discount->status),
                $this->rowActions([
                    ['url' => route('super-admin.discounts.edit', $discount), 'label' => 'Edit'],
                ], route('super-admin.discounts.destroy', $discount)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function trials(array $filters): array
    {
        $query = Subscription::query()
            ->with(['organization', 'plan'])
            ->where('status', SubscriptionStatus::Trialing->value)
            ->latest();

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Business', 'Plan', 'Started', 'Ends', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (Subscription $subscription) => [
                $subscription->organization?->name ?? '—',
                $subscription->plan?->name ?? '—',
                $subscription->trial_starts_at?->format('d M Y') ?? '—',
                $subscription->trial_ends_at?->format('d M Y') ?? '—',
                $subscription->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.subscriptions.show', $subscription), 'label' => 'Manage'],
                ]),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function paymentsTable(array $filters): array
    {
        $paginator = $this->payments->paginateFiltered($filters);

        return [
            'columns' => ['Invoice', 'Business', 'Amount', 'Method', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn ($payment) => [
                $payment->invoice?->number ?? ($payment->provider_reference ?? 'PAY-'.$payment->id),
                $payment->organization?->name ?? '—',
                $payment->formattedAmount(),
                ucfirst($payment->method ?? $payment->provider),
                $payment->status instanceof PaymentStatus ? $payment->status->label() : ucfirst((string) $payment->status),
                $this->rowActions([
                    ['url' => route('super-admin.payments.show', $payment), 'label' => 'View'],
                ]),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function revenue(): array
    {
        $months = collect(range(0, 5))->map(function (int $i): array {
            $month = now()->startOfMonth()->subMonths($i);
            $end = $month->copy()->endOfMonth();
            $sum = $this->payments->sumPaid($month, $end);
            $newOrgs = Organization::query()
                ->whereBetween('created_at', [$month, $end])
                ->count();
            $churned = Subscription::query()
                ->where('status', SubscriptionStatus::Cancelled->value)
                ->whereBetween('cancelled_at', [$month, $end])
                ->count();

            return [
                $month->format('M Y'),
                '£'.number_format($sum, 2),
                (string) $newOrgs,
                (string) $churned,
                '£'.number_format($sum, 2),
            ];
        });

        return [
            'columns' => ['Period', 'Revenue', 'New', 'Churned', 'Net'],
            'rows' => $months->all(),
            'showFilters' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function analyticsPage(): array
    {
        $total = max(1, $this->organizations->countAll());
        $active = $this->organizations->countByStatus('active');
        $trial = $this->organizations->countTrialing();
        $paid = $this->organizations->countActivePaid();

        $prevStart = now()->subMonth()->startOfMonth();
        $prevEnd = now()->subMonth()->endOfMonth();
        $prevTotal = max(1, Organization::query()->where('created_at', '<=', $prevEnd)->count());
        $prevActive = Organization::query()
            ->where('status', OrganizationStatus::Active->value)
            ->where('created_at', '<=', $prevEnd)
            ->count();
        $prevPaid = Organization::query()
            ->where('status', OrganizationStatus::Active->value)
            ->whereHas('subscriptions', fn ($q) => $q->where('status', SubscriptionStatus::Active->value)->where('created_at', '<=', $prevEnd))
            ->where('created_at', '<=', $prevEnd)
            ->count();
        $prevTrial = Organization::query()
            ->where(function ($q) use ($prevEnd): void {
                $q->where('status', OrganizationStatus::Trial->value)
                    ->orWhereHas('subscriptions', fn ($s) => $s->where('status', SubscriptionStatus::Trialing->value));
            })
            ->where('created_at', '<=', $prevEnd)
            ->count();

        $activation = ($active / $total) * 100;
        $prevActivation = ($prevActive / $prevTotal) * 100;

        return [
            'columns' => ['Metric', 'Current', 'Previous', 'Change'],
            'rows' => [
                ['Activation rate', number_format($activation, 1).'%', number_format($prevActivation, 1).'%', $this->deltaLabel($activation - $prevActivation, '%')],
                ['Trial accounts', (string) $trial, (string) $prevTrial, $this->deltaLabel($trial - $prevTrial)],
                ['Paid accounts', (string) $paid, (string) $prevPaid, $this->deltaLabel($paid - $prevPaid)],
            ],
            'revenueBars' => $this->analytics->monthlyRevenueBars(),
            'growthBars' => $this->analytics->subscriptionGrowthBars(),
            'showFilters' => false,
        ];
    }

    private function deltaLabel(float|int $delta, string $suffix = ''): string
    {
        $prefix = $delta > 0 ? '+' : '';

        return $prefix.number_format((float) $delta, $suffix === '%' ? 1 : 0).$suffix;
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function support(array $filters): array
    {
        $query = SupportTicket::query()->with('organization')->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('subject', 'like', $search)->orWhere('ticket_number', 'like', $search);
            });
        }

        $paginator = $query->paginate(15)->withQueryString();

        return [
            'columns' => ['Ticket', 'Business', 'Subject', 'Priority', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (SupportTicket $ticket) => [
                $ticket->ticket_number,
                $ticket->organization?->name ?? '—',
                $ticket->subject,
                $ticket->priority->label(),
                ucfirst($ticket->status->value),
                $this->rowActions([
                    ['url' => route('super-admin.support.show', $ticket), 'label' => 'Open'],
                ]),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return array<string, mixed>
     */
    private function announcements(array $filters): array
    {
        $paginator = Announcement::query()->latest()->paginate(15)->withQueryString();

        return [
            'columns' => ['Title', 'Audience', 'Channel', 'Scheduled', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (Announcement $item) => [
                $item->title,
                $item->audience,
                $item->channel,
                $item->scheduled_at?->format('d M Y') ?? '—',
                $item->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.announcements.edit', $item), 'label' => 'Edit'],
                ], route('super-admin.announcements.destroy', $item)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function notifications(): array
    {
        $paginator = AppNotification::query()->with('user')->latest()->paginate(15)->withQueryString();

        return [
            'columns' => ['Title', 'Type', 'Audience', 'Created', 'Read', ''],
            'rows' => $paginator->getCollection()->map(fn (AppNotification $item) => [
                $item->title,
                $item->type,
                $item->user?->name ?? '—',
                $item->created_at?->format('d M Y') ?? '—',
                $item->isRead() ? 'Read' : 'Unread',
                view('admin.partials.notification-actions', ['notification' => $item])->render(),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emailTemplates(): array
    {
        $paginator = EmailTemplate::query()->latest()->paginate(15)->withQueryString();

        return [
            'columns' => ['Template', 'Trigger', 'Locale', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (EmailTemplate $item) => [
                $item->name,
                $item->trigger ?? '—',
                $item->locale,
                $item->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.email-templates.edit', $item), 'label' => 'Edit'],
                ], route('super-admin.email-templates.destroy', $item)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function media(): array
    {
        $paginator = MediaAsset::query()->latest()->paginate(15)->withQueryString();

        return [
            'columns' => ['File', 'Folder', 'Type', 'Size', 'Uploaded', ''],
            'rows' => $paginator->getCollection()->map(fn (MediaAsset $item) => [
                $item->filename,
                $item->folder ?? ($item->collection ?? '—'),
                $item->mime_type ?? '—',
                $item->humanSize(),
                $item->created_at?->format('d M Y') ?? '—',
                $this->rowActions([], route('super-admin.media.destroy', $item)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function auditLogs(): array
    {
        $paginator = AuditLog::query()->with('user')->latest('created_at')->paginate(20)->withQueryString();

        return [
            'columns' => ['When', 'Actor', 'Action', 'Target', 'IP', 'Browser'],
            'rows' => $paginator->getCollection()->map(fn (AuditLog $log) => [
                $log->created_at?->format('d M H:i') ?? '—',
                $log->user?->name ?? 'System',
                $log->action,
                $log->target_type ? class_basename($log->target_type).'#'.$log->target_id : '—',
                $log->ip_address ?? '—',
                $log->user_agent ? \Illuminate\Support\Str::limit($log->user_agent, 40) : '—',
            ])->all(),
            'paginator' => $paginator,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function activity(): array
    {
        $paginator = ActivityLog::query()->latest('created_at')->paginate(20)->withQueryString();

        return [
            'columns' => ['Time', 'Actor', 'Event', 'Context'],
            'rows' => $paginator->getCollection()->map(fn (ActivityLog $log) => [
                $log->created_at?->diffForHumans() ?? '—',
                $log->actor_name ?? 'System',
                $log->event,
                $log->description,
            ])->all(),
            'paginator' => $paginator,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function systemHealth(): array
    {
        $checks = [];
        $now = now()->diffForHumans();

        $webStart = microtime(true);
        $checks[] = ['Web', 'Healthy', number_format((microtime(true) - $webStart) * 1000, 1).' ms', $now];

        $dbOk = true;
        $dbMs = 0.0;
        try {
            $dbStart = microtime(true);
            \Illuminate\Support\Facades\DB::select('select 1');
            $dbMs = (microtime(true) - $dbStart) * 1000;
        } catch (\Throwable) {
            $dbOk = false;
        }
        $checks[] = ['Database', $dbOk ? 'Healthy' : 'Down', $dbOk ? number_format($dbMs, 1).' ms' : '—', $now];

        $pendingJobs = 0;
        $failedJobs = 0;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('jobs')) {
                $pendingJobs = (int) \Illuminate\Support\Facades\DB::table('jobs')->count();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('failed_jobs')) {
                $failedJobs = (int) \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
            }
        } catch (\Throwable) {
            // ignore
        }
        $queueStatus = $failedJobs > 0 ? 'Warning' : ($pendingJobs > 0 ? 'Busy' : 'Idle');
        $checks[] = ['Queue', $queueStatus, $pendingJobs.' pending / '.$failedJobs.' failed', $now];

        $cacheOk = true;
        try {
            \Illuminate\Support\Facades\Cache::put('tcp_health_check', 'ok', 30);
            $cacheOk = \Illuminate\Support\Facades\Cache::get('tcp_health_check') === 'ok';
        } catch (\Throwable) {
            $cacheOk = false;
        }
        $checks[] = ['Cache', $cacheOk ? 'Healthy' : 'Down', '—', $now];

        $storageOk = is_writable(storage_path('app')) && is_writable(storage_path('logs'));
        $checks[] = ['Storage', $storageOk ? 'Writable' : 'Read-only', '—', $now];

        return [
            'columns' => ['Service', 'Status', 'Latency', 'Last check'],
            'rows' => $checks,
            'showFilters' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $grouped = Setting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');

        $tabs = [];
        foreach (['general', 'brand', 'seo', 'email', 'payments', 'localization', 'system', 'appearance', 'maintenance'] as $group) {
            $label = ucfirst($group);
            $tabs[$label] = ($grouped[$group] ?? collect())->map(fn (Setting $setting) => [
                'label' => str_replace('_', ' ', ucfirst($setting->key)),
                'value' => (string) ($setting->value ?? ''),
                'key' => $setting->key,
                'group' => $setting->group,
            ])->values()->all();
        }

        return [
            'settingsTabs' => $tabs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function roles(): array
    {
        $roles = Role::query()->withCount(['users', 'permissions'])->orderBy('name')->get();

        return [
            'columns' => ['Role', 'Users', 'Permissions', 'Protected', ''],
            'rows' => $roles->map(fn (Role $role) => [
                $role->name,
                (string) $role->users_count,
                (string) $role->permissions_count,
                $role->is_protected ? 'Yes' : 'No',
                $this->rowActions([
                    ['url' => route('super-admin.roles.edit', $role), 'label' => 'Edit'],
                ], $role->is_protected ? null : route('super-admin.roles.destroy', $role)),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function permissions(): array
    {
        $permissions = Permission::query()->withCount('roles')->orderBy('group')->orderBy('name')->get();

        return [
            'columns' => ['Permission', 'Group', 'Roles', 'Status', ''],
            'rows' => $permissions->map(fn (Permission $permission) => [
                $permission->slug,
                $permission->group,
                (string) $permission->roles_count,
                'Active',
                $this->rowActions([
                    ['url' => route('super-admin.permissions.edit', $permission), 'label' => 'Edit'],
                ], route('super-admin.permissions.destroy', $permission)),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profile(): array
    {
        $user = auth()->user();

        return [
            'columns' => ['Field', 'Value'],
            'rows' => [
                ['Name', $user?->name ?? '—'],
                ['Email', $user?->email ?? '—'],
                ['Role', $user?->role?->name ?? '—'],
                ['Status', ucfirst((string) ($user?->status ?? '—'))],
                ['Last login', $user?->last_login_at?->format('d M Y H:i') ?? '—'],
            ],
            'showFilters' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsPages(): array
    {
        $paginator = CmsPage::query()->latest()->paginate(15)->withQueryString();

        return [
            'columns' => ['Page', 'Slug', 'Updated', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (CmsPage $page) => [
                $page->title,
                '/'.$page->slug,
                $page->updated_at?->format('d M Y') ?? '—',
                $page->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.cms.pages.edit', $page), 'label' => 'Edit'],
                ], route('super-admin.cms.pages.destroy', $page)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsHero(): array
    {
        $items = CmsHeroSection::query()->orderBy('sort_order')->get();

        return [
            'columns' => ['Headline', 'Primary CTA', 'Status', ''],
            'rows' => $items->map(fn (CmsHeroSection $hero) => [
                $hero->headline,
                $hero->primary_cta_label ?? '—',
                $hero->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.cms.hero.edit', $hero), 'label' => 'Edit'],
                ], route('super-admin.cms.hero.destroy', $hero)),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsFeatures(): array
    {
        $paginator = CmsFeature::query()->orderBy('sort_order')->paginate(20)->withQueryString();

        return [
            'columns' => ['Feature', 'Plan', 'Order', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (CmsFeature $feature) => [
                $feature->title,
                $feature->plan_slug ?? '—',
                (string) $feature->sort_order,
                $feature->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.cms.features.edit', $feature), 'label' => 'Edit'],
                ], route('super-admin.cms.features.destroy', $feature)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsPricing(): array
    {
        $plans = $this->plans->orderedActive();

        return [
            'columns' => ['Plan', 'Price', 'Badge', 'Status', ''],
            'rows' => $plans->map(fn ($plan) => [
                $plan->name,
                $plan->formattedPrice().($plan->formattedPrice() === 'Custom' ? '' : '/'.$plan->billing_interval),
                $plan->badge ?? '—',
                $plan->is_active ? PublishStatus::Published->label() : 'Draft',
                $this->rowActions([
                    ['url' => route('super-admin.plans.edit', $plan), 'label' => 'Edit'],
                ]),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsTestimonials(): array
    {
        $paginator = CmsTestimonial::query()->orderBy('sort_order')->paginate(15)->withQueryString();

        return [
            'columns' => ['Name', 'Business', 'Featured', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (CmsTestimonial $item) => [
                $item->name,
                $item->business ?? '—',
                $item->is_featured ? 'Yes' : 'No',
                $item->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.cms.testimonials.edit', $item), 'label' => 'Edit'],
                ], route('super-admin.cms.testimonials.destroy', $item)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsFaqs(): array
    {
        $paginator = CmsFaq::query()->orderBy('sort_order')->paginate(20)->withQueryString();

        return [
            'columns' => ['Question', 'Order', 'Status', ''],
            'rows' => $paginator->getCollection()->map(fn (CmsFaq $item) => [
                $item->question,
                (string) $item->sort_order,
                $item->status->label(),
                $this->rowActions([
                    ['url' => route('super-admin.cms.faq.edit', $item), 'label' => 'Edit'],
                ], route('super-admin.cms.faq.destroy', $item)),
            ])->all(),
            'paginator' => $paginator,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsContact(): array
    {
        $settings = Setting::query()
            ->where(function ($query): void {
                $query->where('group', 'email')->orWhere('group', 'general');
            })
            ->get();

        return [
            'columns' => ['Block', 'Value', 'Status', ''],
            'rows' => $settings->map(fn (Setting $setting) => [
                $setting->key,
                $setting->value ?? '—',
                'Published',
                $this->rowActions([
                    ['url' => route('super-admin.settings'), 'label' => 'Edit in Settings'],
                ]),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmsFooter(): array
    {
        $settings = Setting::query()->where('group', 'brand')->get();

        return [
            'columns' => ['Column', 'Items', 'Status', ''],
            'rows' => $settings->map(fn (Setting $setting) => [
                $setting->key,
                $setting->value ?? '—',
                'Published',
                $this->rowActions([
                    ['url' => route('super-admin.settings'), 'label' => 'Edit in Settings'],
                ]),
            ])->all(),
            'showFilters' => false,
            'rawHtml' => true,
        ];
    }
}
