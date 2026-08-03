@props(['padding' => true])

<section {{ $attributes->class('admin-card overflow-hidden transition hover:shadow-[0_8px_30px_-20px_rgb(17_24_39_/_0.12)]') }}>
    <div @class(['p-4 sm:p-5' => $padding])>
        {{ $slot }}
    </div>
</section>
