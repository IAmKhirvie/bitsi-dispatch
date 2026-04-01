<div class="flex h-full flex-1 flex-col gap-4 p-4">
    <div class="flex items-center gap-4">
        <div class="p-2 rounded-lg bg-primary/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold">Attendance Settings</h1>
            <p class="text-sm text-muted-foreground">Configure driver attendance thresholds and alerts</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('status'))
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mx-auto w-full max-w-2xl space-y-6">
        {{-- Late Threshold --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 pb-2">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Late Threshold</h3>
                <p class="mt-1 text-sm text-muted-foreground">How many minutes after scheduled time is a driver considered late?</p>
            </div>
            <div class="p-6 pt-4">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="late_threshold" class="text-sm font-medium leading-none">Late Threshold (minutes)</label>
                        <input
                            id="late_threshold"
                            type="number"
                            wire:model.live.debounce.1000ms="lateThreshold"
                            min="1"
                            max="120"
                            class="flex h-9 w-full max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                        <p class="text-xs text-muted-foreground">
                            Drivers checking in after {{ $lateThreshold }} minutes past scheduled time will be marked as late.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pre-departure Alert --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 pb-2">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Pre-departure Alert</h3>
                <p class="mt-1 text-sm text-muted-foreground">When should the dispatcher be alerted about upcoming trips with no check-in?</p>
            </div>
            <div class="p-6 pt-4">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="pre_departure" class="text-sm font-medium leading-none">Alert Before Departure (minutes)</label>
                        <input
                            id="pre_departure"
                            type="number"
                            wire:model.live.debounce.1000ms="preDepartureAlert"
                            min="1"
                            max="120"
                            class="flex h-9 w-full max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                        <p class="text-xs text-muted-foreground">
                            Alert dispatcher {{ $preDepartureAlert }} minutes before scheduled departure if driver hasn't checked in.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Auto-absent Timeout --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 pb-2">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Auto-absent Timeout</h3>
                <p class="mt-1 text-sm text-muted-foreground">After how many minutes should a no-show driver be marked as absent?</p>
            </div>
            <div class="p-6 pt-4">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="auto_absent" class="text-sm font-medium leading-none">Auto-absent Timeout (minutes)</label>
                        <input
                            id="auto_absent"
                            type="number"
                            wire:model.live.debounce.1000ms="autoAbsentTimeout"
                            min="1"
                            max="240"
                            class="flex h-9 w-full max-w-xs rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        />
                        <p class="text-xs text-muted-foreground">
                            Drivers who haven't checked in {{ $autoAbsentTimeout }} minutes after scheduled time will be auto-marked as absent.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Require Check-in --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 pb-2">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Check-in Requirement</h3>
                <p class="mt-1 text-sm text-muted-foreground">Should check-in be mandatory for all assigned drivers?</p>
            </div>
            <div class="p-6 pt-4">
                <div class="flex items-center justify-between">
                    <div class="space-y-0.5">
                        <label for="require_check_in" class="text-base font-medium leading-none">Require Check-in</label>
                        <p class="text-sm text-muted-foreground">
                            When enabled, all assigned drivers must check in before their trip
                        </p>
                    </div>
                    <button
                        wire:click="$toggle('requireCheckIn')"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 {{ $requireCheckIn ? 'bg-primary' : 'bg-input' }}"
                        role="switch"
                        aria-checked="{{ $requireCheckIn ? 'true' : 'false' }}"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-background shadow-lg ring-0 transition duration-200 ease-in-out {{ $requireCheckIn ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div class="flex justify-end">
            <a href="{{ route('admin.attendance.index') }}"
               class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                Back to Attendance
            </a>
        </div>
    </div>
</div>
