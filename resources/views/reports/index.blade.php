@extends('layouts.app')

@section('title', 'Reports - BITSI Dispatch')

@section('content')
    <div class="app-page flex h-full flex-1 flex-col gap-4 p-4">
        <div class="app-toolbar flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Reports</h1>
                <p class="text-sm text-muted-foreground">Dispatch reports and trip analytics</p>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-4 text-card-foreground shadow">
            <div class="app-toolbar flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold">Dispatch Excel Template</h2>
                    <p class="text-sm text-muted-foreground">
                        @if ($dispatchTemplate)
                            Active template: {{ $dispatchTemplate->file_name }}
                        @else
                            Upload an XLSX file to use as the layout for future dispatch report exports.
                        @endif
                    </p>
                </div>
                <div class="app-filterbar flex flex-wrap items-center gap-2">
                    <form action="{{ route('reports.templates.store') }}" method="POST" enctype="multipart/form-data" class="app-filterbar flex flex-wrap items-center gap-2">
                        @csrf
                        <input type="hidden" name="report_type" value="dispatch">
                        <input
                            type="file"
                            name="template"
                            accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            required
                            class="block h-9 w-64 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium"
                        >
                        <button type="submit" class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
                            Import XLSX
                        </button>
                    </form>

                    @if ($dispatchTemplate)
                        <form action="{{ route('reports.templates.destroy', $dispatchTemplate) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-4 text-sm font-medium shadow-sm hover:bg-accent">
                                Use Default
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @error('template')
                <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
            @enderror
        </div>

        @livewire('report.report-summary-table')
    </div>
@endsection
