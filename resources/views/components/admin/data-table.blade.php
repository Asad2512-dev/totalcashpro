@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records yet.',
])

<x-admin.table :columns="$columns" :rows="$rows" :empty="$empty" />
