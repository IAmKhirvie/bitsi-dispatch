@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-y-1.5 text-center sm:text-left ' . $class]) }}>
    {{ $slot }}
</div>
