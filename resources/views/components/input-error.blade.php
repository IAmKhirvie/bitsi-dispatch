@props(['field', 'bag' => 'default'])

@error($field, $bag)
    <p {{ $attributes->merge(['class' => 'text-sm text-destructive']) }}>{{ $message }}</p>
@enderror
