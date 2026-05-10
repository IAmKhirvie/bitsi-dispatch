@props(['field' => null, 'for' => null, 'bag' => 'default'])

@php $errorField = $field ?? $for; @endphp

@if ($errorField)
    @error($errorField, $bag)
        <p {{ $attributes->merge(['class' => 'text-sm text-destructive']) }}>{{ $message }}</p>
    @enderror
@endif
