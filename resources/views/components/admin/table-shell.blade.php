@props([
    'stack' => true,
    'sticky' => false,
])

<div {{ $attributes->class('admin-card overflow-hidden') }}>
    <div @class([
        'admin-table-wrap',
        'admin-table-wrap--stack' => $stack,
    ])>
        <table @class([
            'admin-table min-w-full text-left text-sm',
            'admin-table--stack' => $stack,
            'admin-table-head-sticky' => $sticky,
        ])>
            {{ $slot }}
        </table>
    </div>
</div>
