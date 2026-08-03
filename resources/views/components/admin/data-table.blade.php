@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records yet — UI placeholder for Phase 2.',
])

<x-admin.table :columns="$columns" :rows="$rows" :empty="$empty" />
