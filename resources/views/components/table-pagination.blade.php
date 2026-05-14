@props([
    'paginator',
    'options' => [5, 10, 15, 20, 30, 40, 50, 100],
])

<div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs">
    <div class="flex items-center gap-2">
        <label class="text-muted-foreground">Rows per page:</label>
        <select
            wire:model.live="perPage"
            class="rounded border border-input bg-transparent px-2 py-1 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
            @foreach ($options as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
        <span class="text-muted-foreground">
            Showing {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
        </span>
    </div>

    @if ($paginator->hasPages())
        <div>
            {{ $paginator->onEachSide(1)->links() }}
        </div>
    @endif
</div>
