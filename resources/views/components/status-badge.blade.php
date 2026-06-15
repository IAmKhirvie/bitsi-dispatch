@props([
    'status' => '',
    'label' => null,
    'size' => 'sm',
])

@php
    $normalized = strtolower(str_replace('-', '_', (string) $status));

    $palette = [
        'scheduled' => ['bg' => 'hsl(214 95% 93%)', 'fg' => 'hsl(217 76% 38%)', 'dot' => 'hsl(217 91% 60%)'],
        'departed'  => ['bg' => 'hsl(48 96% 89%)',  'fg' => 'hsl(28 78% 35%)',  'dot' => 'hsl(38 92% 50%)'],
        'on_route'  => ['bg' => 'hsl(204 94% 94%)', 'fg' => 'hsl(201 79% 32%)', 'dot' => 'hsl(199 89% 48%)'],
        'delayed'   => ['bg' => 'hsl(33 100% 92%)', 'fg' => 'hsl(20 78% 35%)',  'dot' => 'hsl(25 95% 53%)'],
        'arrived'   => ['bg' => 'hsl(138 76% 92%)', 'fg' => 'hsl(142 65% 26%)', 'dot' => 'hsl(142 71% 45%)'],
        'cancelled' => ['bg' => 'hsl(0 93% 94%)',   'fg' => 'hsl(0 65% 38%)',   'dot' => 'hsl(0 84% 60%)'],
        'breakdown' => ['bg' => 'hsl(48 96% 89%)',  'fg' => 'hsl(28 78% 35%)',  'dot' => 'hsl(38 92% 50%)'],
        'active'    => ['bg' => 'hsl(138 76% 92%)', 'fg' => 'hsl(142 65% 26%)', 'dot' => 'hsl(142 71% 45%)'],
        'inactive'  => ['bg' => 'hsl(0 93% 94%)',   'fg' => 'hsl(0 65% 38%)',   'dot' => 'hsl(0 84% 60%)'],
        'available' => ['bg' => 'hsl(138 76% 92%)', 'fg' => 'hsl(142 65% 26%)', 'dot' => 'hsl(142 71% 45%)'],
        'dispatched'=> ['bg' => 'hsl(214 95% 93%)', 'fg' => 'hsl(217 76% 38%)', 'dot' => 'hsl(217 91% 60%)'],
        'on_leave'  => ['bg' => 'hsl(33 100% 92%)', 'fg' => 'hsl(20 78% 35%)',  'dot' => 'hsl(25 95% 53%)'],
    ];

    $colors = $palette[$normalized] ?? ['bg' => 'hsl(0 0% 96%)', 'fg' => 'hsl(0 0% 30%)', 'dot' => 'hsl(0 0% 45%)'];
    $display = $label ?? str_replace('_', ' ', $status);
    $sizeClass = $size === 'md' ? 'px-2.5 py-1 text-xs' : 'px-2 py-0.5 text-[11px]';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full font-medium capitalize {{ $sizeClass }}"
      style="background-color: {{ $colors['bg'] }}; color: {{ $colors['fg'] }};">
    <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $colors['dot'] }};"></span>
    {{ $display }}
</span>
