@props([
    'role' => '',
    'label' => null,
])

@php
    $normalized = strtolower((string) $role);

    $palette = [
        'admin'              => ['bg' => 'hsl(0 93% 94%)',   'fg' => 'hsl(0 65% 38%)'],
        'operations_manager' => ['bg' => 'hsl(214 95% 93%)', 'fg' => 'hsl(217 76% 38%)'],
        'ops'                => ['bg' => 'hsl(214 95% 93%)', 'fg' => 'hsl(217 76% 38%)'],
        'dispatcher'         => ['bg' => 'hsl(138 76% 92%)', 'fg' => 'hsl(142 65% 26%)'],
        'driver'             => ['bg' => 'hsl(48 96% 89%)',  'fg' => 'hsl(28 78% 35%)'],
    ];

    $colors = $palette[$normalized] ?? ['bg' => 'hsl(0 0% 96%)', 'fg' => 'hsl(0 0% 30%)'];
    $display = $label ?? ucwords(str_replace('_', ' ', $role));
@endphp

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize"
      style="background-color: {{ $colors['bg'] }}; color: {{ $colors['fg'] }};">
    {{ $display }}
</span>
