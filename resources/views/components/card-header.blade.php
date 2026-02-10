@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-y-1.5 p-6 ' . $class]) }}>
    {{ $slot }}
</div>
