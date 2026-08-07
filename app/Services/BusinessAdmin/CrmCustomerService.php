<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\CrmCustomer;
use App\Models\CrmCustomerNote;
use App\Models\CrmCustomerVisit;
use App\Models\User;
use Illuminate\Support\Collection;

final class CrmCustomerService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    /**
     * @return Collection<int, CrmCustomer>
     */
    public function list(User $admin, ?string $search = null, ?string $marketing = null): Collection
    {
        $branchId = $this->branchContext->currentBranchId($admin);

        return CrmCustomer::query()
            ->where('organization_id', $admin->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when($marketing === 'email', fn ($q) => $q->whereJsonContains('marketing_preferences->email', true))
            ->when($marketing === 'sms', fn ($q) => $q->whereJsonContains('marketing_preferences->sms', true))
            ->orderBy('name')
            ->limit(200)
            ->get();
    }

    public function findForAdmin(User $admin, CrmCustomer $customer): CrmCustomer
    {
        $this->assertSameOrg($admin, $customer);

        return $customer->load(['branch']);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function timeline(User $admin, CrmCustomer $customer): Collection
    {
        $this->assertSameOrg($admin, $customer);

        $notes = CrmCustomerNote::query()
            ->with('author:id,name')
            ->where('crm_customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CrmCustomerNote $note) => [
                'type' => 'note',
                'at' => $note->created_at,
                'title' => 'Note added',
                'body' => $note->body,
                'meta' => $note->author?->name,
            ]);

        $visits = CrmCustomerVisit::query()
            ->where('crm_customer_id', $customer->id)
            ->orderByDesc('visited_at')
            ->get()
            ->map(fn (CrmCustomerVisit $visit) => [
                'type' => 'visit',
                'at' => $visit->visited_at,
                'title' => 'Visit recorded',
                'body' => $visit->notes,
                'meta' => $visit->spend_amount !== null ? '£'.number_format((float) $visit->spend_amount, 2) : null,
            ]);

        return $notes->concat($visits)->sortByDesc('at')->values();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $admin, array $data): CrmCustomer
    {
        return CrmCustomer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $this->branchContext->currentBranchId($admin) ?? $data['branch_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'marketing_preferences' => $data['marketing_preferences'] ?? ['email' => false, 'sms' => false],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $admin, CrmCustomer $customer, array $data): CrmCustomer
    {
        $this->assertSameOrg($admin, $customer);

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'marketing_preferences' => $data['marketing_preferences'] ?? $customer->marketing_preferences,
            'notes' => $data['notes'] ?? null,
        ]);

        return $customer->refresh();
    }

    public function delete(User $admin, CrmCustomer $customer): void
    {
        $this->assertSameOrg($admin, $customer);
        $customer->delete();
    }

    public function addNote(User $admin, CrmCustomer $customer, string $body): CrmCustomerNote
    {
        $this->assertSameOrg($admin, $customer);

        return CrmCustomerNote::query()->create([
            'crm_customer_id' => $customer->id,
            'organization_id' => $admin->organization_id,
            'body' => $body,
            'created_by' => $admin->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addVisit(User $admin, CrmCustomer $customer, array $data): CrmCustomerVisit
    {
        $this->assertSameOrg($admin, $customer);

        return CrmCustomerVisit::query()->create([
            'crm_customer_id' => $customer->id,
            'organization_id' => $admin->organization_id,
            'branch_id' => $this->branchContext->currentBranchId($admin),
            'visited_at' => $data['visited_at'] ?? now(),
            'spend_amount' => $data['spend_amount'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    private function assertSameOrg(User $admin, CrmCustomer $customer): void
    {
        if ((int) $customer->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }
}
