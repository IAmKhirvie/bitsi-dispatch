@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col-reverse sm:flex-row sm:justify-end sm:gap-x-2 ' . $class]) }}>
    {{ $slot }}
</div>
