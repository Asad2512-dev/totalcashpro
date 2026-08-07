@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records for the selected filters.',
])

<div
    class="admin-card overflow-hidden"
    x-data="reportTable(@js($columns), @js($rows))"
>
    <div class="flex flex-col gap-3 border-b border-gray-100 p-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <input
                type="search"
                x-model="search"
                placeholder="Search table…"
                class="admin-input w-full pl-9"
            />
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <x-admin.icon name="search" class="h-4 w-4" />
            </span>
        </div>
        <p class="text-sm text-gray-500" x-text="`${filteredRows.length} of ${rows.length} rows`"></p>
    </div>

    <template x-if="filteredRows.length === 0">
        <div class="p-8">
            <x-admin.empty-state title="No matching rows" :description="$empty" />
        </div>
    </template>

    <template x-if="filteredRows.length > 0">
        <div class="report-table-wrap max-h-[32rem] overflow-auto">
            <table class="admin-table min-w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-white shadow-sm dark:bg-gray-900">
                    <tr>
                        <template x-for="(column, index) in columns" :key="column">
                            <th class="cursor-pointer whitespace-nowrap px-4 py-3" @click="sortBy(index)">
                                <span x-text="column"></span>
                                <span x-show="sortColumn === index" x-text="sortDirection === 'asc' ? ' ↑' : ' ↓'" class="text-primary-600"></span>
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, rowIndex) in paginatedRows" :key="rowIndex">
                        <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                            <template x-for="(cell, cellIndex) in row" :key="cellIndex">
                                <td class="whitespace-nowrap px-4 py-3.5 text-gray-700 dark:text-gray-200" x-text="cell"></td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3 dark:border-gray-800" x-show="filteredRows.length > pageSize">
        <button type="button" class="text-sm font-semibold text-primary-700 disabled:opacity-40" @click="prevPage()" :disabled="page === 1">Previous</button>
        <span class="text-sm text-gray-500" x-text="`Page ${page} of ${totalPages}`"></span>
        <button type="button" class="text-sm font-semibold text-primary-700 disabled:opacity-40" @click="nextPage()" :disabled="page >= totalPages">Next</button>
    </div>
</div>
