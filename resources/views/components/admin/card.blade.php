@props(['padding' => true])

<section {{ $attributes->class('admin-card overflow-hidden') }}>
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</section>
