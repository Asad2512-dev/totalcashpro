@props(['rows' => 5, 'columns' => 4])

<div {{ $attributes->class('admin-card overflow-hidden p-4') }} aria-hidden="true">
    <div class="space-y-3">
        <div class="admin-skeleton h-4 w-full max-w-md"></div>
        @for ($r = 0; $r < $rows; $r++)
            <div class="grid gap-3" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr))">
                @for ($c = 0; $c < $columns; $c++)
                    <div class="admin-skeleton h-10 w-full"></div>
                @endfor
            </div>
        @endfor
    </div>
</div>
