<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_summary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_summary_id')->constrained()->cascadeOnDelete();
            $table->string('category', 30);
            $table->unsignedInteger('trip_count')->default(0);
            $table->timestamps();

            $table->unique(['daily_summary_id', 'category']);
            $table->index('category');
        });

        // Migrate existing data from denormalized columns
        $categories = [
            'sb', 'nb', 'naga', 'legazpi', 'sorsogon',
            'virac', 'masbate', 'tabaco', 'visayas', 'cargo',
        ];

        $summaries = DB::table('daily_summaries')->get();

        foreach ($summaries as $summary) {
            foreach ($categories as $category) {
                $column = $category . '_trips';

                if (isset($summary->$column)) {
                    DB::table('daily_summary_items')->insert([
                        'daily_summary_id' => $summary->id,
                        'category' => $category,
                        'trip_count' => $summary->$column ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Drop the old denormalized columns
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'sb_trips',
                'nb_trips',
                'naga_trips',
                'legazpi_trips',
                'sorsogon_trips',
                'virac_trips',
                'masbate_trips',
                'tabaco_trips',
                'visayas_trips',
                'cargo_trips',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->unsignedInteger('sb_trips')->default(0);
            $table->unsignedInteger('nb_trips')->default(0);
            $table->unsignedInteger('naga_trips')->default(0);
            $table->unsignedInteger('legazpi_trips')->default(0);
            $table->unsignedInteger('sorsogon_trips')->default(0);
            $table->unsignedInteger('virac_trips')->default(0);
            $table->unsignedInteger('masbate_trips')->default(0);
            $table->unsignedInteger('tabaco_trips')->default(0);
            $table->unsignedInteger('visayas_trips')->default(0);
            $table->unsignedInteger('cargo_trips')->default(0);
        });

        // Restore data from items back to columns
        $items = DB::table('daily_summary_items')->get()->groupBy('daily_summary_id');

        foreach ($items as $summaryId => $rows) {
            $update = [];
            foreach ($rows as $row) {
                $update[$row->category . '_trips'] = $row->trip_count;
            }
            DB::table('daily_summaries')->where('id', $summaryId)->update($update);
        }

        Schema::dropIfExists('daily_summary_items');
    }
};
