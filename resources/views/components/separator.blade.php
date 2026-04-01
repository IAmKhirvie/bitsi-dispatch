@props([
    'orientation' => 'horizontal',
])

@php
    $classes = $orientation === 'horizontal'
        ? 'shrink-0 bg-border h-[1px] w-full'
        : 'shrink-0 bg-border h-full w-[1px]';
@endphp

<div role="separator" {{ $attributes->merge(['class' => $classes]) }}></div>
