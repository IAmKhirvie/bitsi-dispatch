<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\DispatchDay;

class SummaryService
{
    public function generateForDay(DispatchDay $day): DailySummary
    {
        $entries = $day->entries()
            ->where('status', '!=', 'cancelled')
            ->get();

        $data = [
            'dispatch_day_id' => $day->id,
            'total_trips' => $entries->count(),
            'sb_trips' => $entries->where('direction', 'SB')->count(),
            'nb_trips' => $entries->where('direction', 'NB')->count(),
            'naga_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'naga'))->count(),
            'legazpi_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'legazpi'))->count(),
            'sorsogon_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'sorsogon'))->count(),
            'virac_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'virac'))->count(),
            'masbate_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'masbate'))->count(),
            'tabaco_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->route ?? ''), 'tabaco'))->count(),
            'visayas_trips' => $entries->filter(fn ($e) =>
                str_contains(strtolower($e->route ?? ''), 'tacloban') ||
                str_contains(strtolower($e->route ?? ''), 'catbalogan') ||
                str_contains(strtolower($e->route ?? ''), 'samar')
            )->count(),
            'cargo_trips' => $entries->filter(fn ($e) => str_contains(strtolower($e->remarks ?? ''), 'cargo'))->count(),
        ];

        return DailySummary::updateOrCreate(
            ['dispatch_day_id' => $day->id],
            $data
        );
    }
}
