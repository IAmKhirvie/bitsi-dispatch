<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\DispatchDay;
use Illuminate\Support\Facades\DB;

class SummaryService
{
    private const DESTINATION_MATCHERS = [
        'naga' => ['naga'],
        'legazpi' => ['legazpi'],
        'sorsogon' => ['sorsogon'],
        'virac' => ['virac'],
        'masbate' => ['masbate'],
        'tabaco' => ['tabaco'],
        'visayas' => ['tacloban', 'catbalogan', 'samar'],
    ];

    public function generateForDay(DispatchDay $day): DailySummary
    {
        return DB::transaction(function () use ($day) {
            $entries = $day->entries()
                ->where('status', '!=', 'cancelled')
                ->get();

            $summary = DailySummary::updateOrCreate(
                ['dispatch_day_id' => $day->id],
                ['total_trips' => $entries->count()]
            );

            // Build category counts
            $counts = [
                'sb' => $entries->where('direction', 'SB')->count(),
                'nb' => $entries->where('direction', 'NB')->count(),
                'cargo' => $entries->filter(fn ($e) => str_contains(strtolower($e->remarks ?? ''), 'cargo'))->count(),
            ];

            foreach (self::DESTINATION_MATCHERS as $category => $keywords) {
                $counts[$category] = $entries->filter(fn ($e) =>
                    collect($keywords)->contains(fn ($kw) => str_contains(strtolower($e->route ?? ''), $kw))
                )->count();
            }

            // Upsert all items
            foreach ($counts as $category => $tripCount) {
                $summary->items()->updateOrCreate(
                    ['category' => $category],
                    ['trip_count' => $tripCount]
                );
            }

            return $summary->load('items');
        });
    }
}
