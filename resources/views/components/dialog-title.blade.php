@props(['class' => ''])

<h2 {{ $attributes->merge(['class' => 'text-lg font-semibold leading-none tracking-tight ' . $class]) }}>
    {{ $slot }}
</h2>
