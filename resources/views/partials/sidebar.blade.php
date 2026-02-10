{{-- Mobile Sidebar Overlay --}}
<div
    x-show="mobileOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-on:click="mobileOpen = false"
    class="fixed inset-0 z-40 bg-black/80 md:hidden"
    x-cloak
></div>

{{-- Mobile Sidebar --}}
<aside
    x-show="mobileOpen"
    x-transition:enter="transition ease-in-out duration-300 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-[18rem] bg-sidebar p-0 text-sidebar-foreground md:hidden"
    data-sidebar="sidebar"
    data-mobile="true"
    x-cloak
>
    <div class="flex h-full w-full flex-col">
        @include('partials._sidebar-content')
    </div>
</aside>

{{-- Desktop Sidebar --}}
<div
    class="group peer hidden md:block"
    x-bind:data-state="sidebarOpen ? 'expanded' : 'collapsed'"
    x-bind:data-collapsible="sidebarOpen ? '' : 'icon'"
    data-variant="inset"
    data-side="left"
>
    {{-- Sidebar gap spacer --}}
    <div
        class="relative h-svh bg-transparent transition-[width] duration-200 ease-linear group-data-[collapsible=offcanvas]:w-0 group-data-[collapsible=icon]:w-[calc(var(--sidebar-width-icon)_+_theme(spacing.4))]"
        x-bind:style="sidebarOpen ? 'width: var(--sidebar-width)' : ''"
    ></div>

    {{-- Sidebar fixed panel --}}
    <div
        class="fixed inset-y-0 left-0 z-10 hidden h-svh transition-[left,right,width] duration-200 ease-linear md:flex p-2 group-data-[collapsible=icon]:w-[calc(var(--sidebar-width-icon)_+_theme(spacing.4)_+2px)]"
        x-bind:style="sidebarOpen ? 'width: var(--sidebar-width)' : ''"
    >
        <div
            data-sidebar="sidebar"
            class="flex h-full w-full flex-col bg-sidebar group-data-[variant=floating]:rounded-lg group-data-[variant=floating]:border group-data-[variant=floating]:border-sidebar-border group-data-[variant=floating]:shadow"
        >
            @include('partials._sidebar-content')
        </div>
    </div>
</div>
