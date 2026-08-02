<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Branch;
use Illuminate\Support\Str;

final class BranchService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Branch
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        $branch = Branch::query()->create($data);
        $this->logAdminAction('branch.created', 'Branch created: '.$branch->name, $branch);

        return $branch;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Branch $branch, array $data): Branch
    {
        $old = $branch->toArray();
        if (isset($data['slug']) || isset($data['name'])) {
            $data['slug'] = Str::slug($data['slug'] ?? $data['name'] ?? $branch->name);
        }
        $branch->update($data);
        $this->logAdminAction('branch.updated', 'Branch updated: '.$branch->name, $branch, $old, $branch->fresh()?->toArray());

        return $branch->refresh();
    }

    public function delete(Branch $branch): void
    {
        $snapshot = $branch->toArray();
        $name = $branch->name;
        $branch->delete();
        $this->logAdminAction('branch.deleted', 'Branch deleted: '.$name, null, $snapshot);
    }
}
