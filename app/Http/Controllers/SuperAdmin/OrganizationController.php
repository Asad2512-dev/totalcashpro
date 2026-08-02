<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\OrganizationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Services\SuperAdmin\OrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

final class OrganizationController extends Controller
{
    public function __construct(private readonly OrganizationService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Add Business',
            'description' => 'Creates the business plus one owner account, then emails login credentials.',
            'active' => 'businesses',
            'action' => route('super-admin.organizations.store'),
            'cancelRoute' => route('super-admin.businesses'),
            'fields' => $this->createFields(),
        ]);
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $result = $this->service->createWithOwner($request->validated());

        return redirect()
            ->route('super-admin.organizations.show', $result['organization'])
            ->with('status', 'Business created. Owner login credentials were emailed.')
            ->with('owner_credentials', [
                'name' => $result['owner']->name,
                'email' => $result['owner']->email,
                'password' => $result['password'],
            ]);
    }

    public function show(Organization $organization): View
    {
        $organization->load([
            'owner.role',
            'currentSubscription.plan',
            'branches' => fn ($q) => $q->orderBy('name'),
            'users' => fn ($q) => $q->with(['role', 'branch'])->orderBy('name'),
        ]);

        $subscription = $organization->currentSubscription;

        return view('admin.businesses.show', [
            'title' => $organization->name,
            'description' => 'Full business profile, staff and branches.',
            'organization' => $organization,
            'fields' => [
                ['label' => 'Business name', 'value' => $organization->name],
                ['label' => 'Slug', 'value' => $organization->slug],
                ['label' => 'Business email', 'value' => $organization->email ?? '—'],
                ['label' => 'Phone', 'value' => $organization->phone ?? '—'],
                ['label' => 'Country', 'value' => $organization->country],
                ['label' => 'Currency', 'value' => $organization->currency],
                ['label' => 'Timezone', 'value' => $organization->timezone],
                ['label' => 'Tax number', 'value' => $organization->tax_number ?? '—'],
                ['label' => 'Status', 'value' => $organization->status?->label() ?? $organization->status],
                ['label' => 'Owner', 'value' => $organization->owner?->name ?? '—'],
                ['label' => 'Owner email / login', 'value' => $organization->owner?->email ?? '—'],
                ['label' => 'Owner role', 'value' => $organization->owner?->role?->name ?? '—'],
                ['label' => 'Plan', 'value' => $subscription?->plan?->name ?? '—'],
                ['label' => 'Subscription status', 'value' => $subscription?->status?->label() ?? '—'],
                ['label' => 'Trial ends', 'value' => $organization->trial_ends_at?->format('d M Y') ?? ($subscription?->trial_ends_at?->format('d M Y') ?? '—')],
                ['label' => 'Period ends', 'value' => $subscription?->current_period_end?->format('d M Y') ?? '—'],
                ['label' => 'Branches', 'value' => (string) $organization->branches->count()],
                ['label' => 'Staff', 'value' => (string) $organization->users->count()],
                ['label' => 'Created', 'value' => $organization->created_at?->format('d M Y H:i') ?? '—'],
                ['label' => 'Updated', 'value' => $organization->updated_at?->format('d M Y H:i') ?? '—'],
            ],
            'actions' => view('admin.partials.organization-actions', compact('organization'))->render(),
            'credentials' => session('owner_credentials'),
        ]);
    }

    public function sendCredentials(Organization $organization): RedirectResponse
    {
        $result = $this->service->sendOwnerCredentials($organization);

        return back()
            ->with('status', 'Fresh login credentials emailed to '.$result['owner']->email.'.')
            ->with('owner_credentials', [
                'name' => $result['owner']->name,
                'email' => $result['owner']->email,
                'password' => $result['password'],
            ]);
    }

    public function edit(Organization $organization): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Business',
            'description' => $organization->name,
            'active' => 'businesses',
            'action' => route('super-admin.organizations.update', $organization),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.organizations.show', $organization),
            'model' => $organization,
            'fields' => $this->fields($organization),
        ]);
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'size:2'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'owner_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:pending,active,trial,suspended,cancelled'],
        ]);

        $this->service->update($organization, $data);

        return redirect()
            ->route('super-admin.organizations.show', $organization)
            ->with('status', 'Business updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->service->delete($organization);

        return redirect()
            ->route('super-admin.businesses')
            ->with('status', 'Business deleted.');
    }

    public function suspend(Organization $organization): RedirectResponse
    {
        $this->service->setStatus($organization, OrganizationStatus::Suspended);

        return back()->with('status', 'Business suspended.');
    }

    public function activate(Organization $organization): RedirectResponse
    {
        $this->service->setStatus($organization, OrganizationStatus::Active);

        return back()->with('status', 'Business activated.');
    }

    public function loginAs(Organization $organization): RedirectResponse
    {
        $this->authorize('view', $organization);

        $owner = $organization->owner
            ?? User::query()
                ->where('organization_id', $organization->id)
                ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                ->orderBy('id')
                ->first();

        if ($owner === null) {
            return back()->withErrors(['organization' => 'This business has no admin owner to sign in as.']);
        }

        $superAdminId = auth()->id();
        auth()->login($owner);
        session()->put('impersonator_id', $superAdminId);
        session()->regenerate();

        return redirect()
            ->route('business-admin.dashboard')
            ->with('status', 'Signed in as '.$owner->name.' ('.$organization->name.').');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:organizations,id'],
            'bulk_action' => ['required', 'in:suspend,delete'],
        ]);

        $count = match ($data['bulk_action']) {
            'suspend' => $this->service->bulkSuspend($data['ids']),
            'delete' => $this->service->bulkDelete($data['ids']),
        };

        return back()->with('status', "Bulk {$data['bulk_action']} applied to {$count} business(es).");
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function createFields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Business name'],
            ['name' => 'slug', 'label' => 'Slug (optional)'],
            ['name' => 'owner_name', 'label' => 'Owner name', 'full' => true],
            ['name' => 'owner_email', 'type' => 'email', 'label' => 'Owner email (login)', 'full' => true],
            ['name' => 'email', 'type' => 'email', 'label' => 'Business email (optional)'],
            ['name' => 'phone'],
            ['name' => 'country', 'value' => 'GB'],
            ['name' => 'currency', 'value' => 'GBP'],
            ['name' => 'timezone', 'value' => 'Europe/London'],
            ['name' => 'tax_number', 'label' => 'Tax number'],
            [
                'name' => 'status',
                'type' => 'select',
                'value' => 'trial',
                'options' => [
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'trial' => 'Trial',
                    'suspended' => 'Suspended',
                    'cancelled' => 'Cancelled',
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Organization $organization = null): array
    {
        $owners = User::query()->orderBy('name')->pluck('name', 'id')->all();

        return [
            ['name' => 'name', 'label' => 'Business name', 'value' => $organization?->name],
            ['name' => 'slug', 'label' => 'Slug', 'value' => $organization?->slug],
            ['name' => 'email', 'type' => 'email', 'value' => $organization?->email],
            ['name' => 'phone', 'value' => $organization?->phone],
            ['name' => 'country', 'value' => $organization?->country ?? 'GB'],
            ['name' => 'currency', 'value' => $organization?->currency ?? 'GBP'],
            ['name' => 'timezone', 'value' => $organization?->timezone ?? 'Europe/London'],
            ['name' => 'tax_number', 'label' => 'Tax number', 'value' => $organization?->tax_number],
            [
                'name' => 'owner_user_id',
                'type' => 'select',
                'label' => 'Owner (one account)',
                'value' => $organization?->owner_user_id,
                'options' => ['' => '— None —'] + $owners,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'value' => $organization?->status?->value ?? 'pending',
                'options' => [
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'trial' => 'Trial',
                    'suspended' => 'Suspended',
                    'cancelled' => 'Cancelled',
                ],
            ],
        ];
    }
}