@props(['columns' => 4, 'rows' => 1])

<div {{ $attributes->class('admin-stat-grid') }} aria-hidden="true">
    @for ($i = 0; $i < $columns; $i++)
        <div class="admin-card space-y-3 p-5">
            <div class="admin-skeleton h-4 w-24"></div>
            <div class="admin-skeleton h-8 w-32"></div>
            <div class="admin-skeleton h-5 w-20"></div>
        </div>
    @endfor
</div>
