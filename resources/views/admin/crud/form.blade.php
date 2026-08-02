@props([
    'title',
    'description' => null,
    'active',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save',
    'cancelRoute',
    'fields' => [],
    'model' => null,
])

<x-layouts.admin :title="$title" :active="$active">
    <x-admin.breadcrumb :items="[ucfirst(str_replace('-', ' ', $active)), $title]" />

    <x-admin.toolbar :title="$title" :description="$description">
        <x-admin.button variant="secondary" size="sm" :href="$cancelRoute">Back</x-admin.button>
    </x-admin.toolbar>

    <x-admin.card>
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $field)
                    @php
                        $name = $field['name'];
                        $type = $field['type'] ?? 'text';
                        $label = $field['label'] ?? ucfirst(str_replace('_', ' ', $name));
                        $value = old($name, $field['value'] ?? data_get($model, $name));
                        $span = ($field['full'] ?? false) ? 'sm:col-span-2' : '';
                    @endphp

                    <div class="{{ $span }}">
                        @if ($type === 'textarea')
                            <x-admin.textarea :label="$label" :name="$name" :rows="$field['rows'] ?? 4">{{ $value }}</x-admin.textarea>
                        @elseif ($type === 'select')
                            <x-admin.select :label="$label" :name="$name">
                                @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </x-admin.select>
                        @elseif ($type === 'checkbox')
                            <x-admin.checkbox :label="$label" :name="$name" :checked="(bool) $value" />
                        @elseif ($type === 'file')
                            <label class="block space-y-1.5">
                                <span class="admin-label">{{ $label }}</span>
                                <input type="file" name="{{ $name }}" class="admin-input">
                            </label>
                        @elseif ($type === 'switch')
                            <x-admin.switch :label="$label" :name="$name" :checked="(bool) $value" />
                        @else
                            <x-admin.input :label="$label" :type="$type" :name="$name" :value="$value" />
                        @endif
                        @error($name)
                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <x-admin.button type="submit">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="$cancelRoute">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-layouts.admin>
