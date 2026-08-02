<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Organization;
use App\Services\SuperAdmin\BranchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class BranchController extends Controller
{
    public function __construct(private readonly BranchService $service) {}

    public function create(Request $request): View
    {
        $organizationId = $request->integer('organization_id') ?: null;
        $organization = $organizationId
            ? Organization::query()->find($organizationId)
            : null;

        return view('admin.crud.form', [
            'title' => $organization ? 'Add Branch · '.$organization->name : 'Add Branch',
            'description' => $organization
                ? 'New location for this business.'
                : 'Choose the business this branch belongs to.',
            'active' => 'businesses',
            'action' => route('super-admin.branches.store'),
            'cancelRoute' => $organization
                ? route('super-admin.organizations.show', $organization)
                : route('super-admin.businesses'),
            'fields' => $this->fields(null, $organizationId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:open,closed,paused'],
            'staff_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $branch = $this->service->create($data);

        return redirect()
            ->route('super-admin.organizations.show', $branch->organization_id)
            ->with('status', 'Branch created for this business.');
    }

    public function edit(Branch $branch): View
    {
        $branch->load('organization');

        return view('admin.crud.form', [
            'title' => 'Edit Branch',
            'description' => $branch->organization?->name ?? 'Business branch',
            'active' => 'businesses',
            'action' => route('super-admin.branches.update', $branch),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.organizations.show', $branch->organization_id),
            'model' => $branch,
            'fields' => $this->fields($branch, $branch->organization_id),
        ]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:open,closed,paused'],
            'staff_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->service->update($branch, $data);

        return redirect()
            ->route('super-admin.organizations.show', $branch->organization_id)
            ->with('status', 'Branch updated.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $organizationId = $branch->organization_id;
        $this->service->delete($branch);

        return redirect()
            ->route('super-admin.organizations.show', $organizationId)
            ->with('status', 'Branch deleted.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Branch $branch = null, ?int $organizationId = null): array
    {
        $selectedOrg = $organizationId ?? $branch?->organization_id;
        $orgs = Organization::query()->orderBy('name')->pluck('name', 'id')->all();

        return [
            [
                'name' => 'organization_id',
                'type' => 'select',
                'label' => 'Business',
                'value' => $selectedOrg,
                'options' => $orgs,
            ],
            ['name' => 'name', 'label' => 'Branch name', 'value' => $branch?->name],
            ['name' => 'slug', 'value' => $branch?->slug],
            ['name' => 'city', 'value' => $branch?->city],
            ['name' => 'address', 'value' => $branch?->address, 'full' => true],
            ['name' => 'staff_count', 'type' => 'number', 'label' => 'Staff count', 'value' => $branch?->staff_count ?? 0],
            ['name' => 'status', 'type' => 'select', 'value' => $branch?->status ?? 'open', 'options' => ['open' => 'Open', 'closed' => 'Closed', 'paused' => 'Paused']],
        ];
    }
}
