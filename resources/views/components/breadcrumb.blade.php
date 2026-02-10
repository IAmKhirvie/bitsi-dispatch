@props(['items' => []])

@if(count($items) > 0)
    <nav aria-label="breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 break-words text-sm text-muted-foreground sm:gap-2.5">
            @foreach($items as $item)
                <li class="inline-flex items-center gap-1.5">
                    @if($loop->last)
                        <span role="link" aria-disabled="true" aria-current="page" class="font-normal text-foreground">
                            {{ $item['title'] }}
                        </span>
                    @else
                        <a href="{{ $item['href'] }}" class="transition-colors hover:text-foreground">
                            {{ $item['title'] }}
                        </a>
                    @endif
                </li>
                @if(!$loop->last)
                    <li role="presentation" aria-hidden="true" class="[&>svg]:h-3.5 [&>svg]:w-3.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
