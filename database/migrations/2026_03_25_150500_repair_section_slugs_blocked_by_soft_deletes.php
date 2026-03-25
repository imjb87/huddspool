<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('sections')
            ->whereNotNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function (object $section): void {
                DB::table('sections')
                    ->where('id', $section->id)
                    ->update([
                        'slug' => sprintf('%s-archived-%d', Str::slug($section->name) ?: 'section', $section->id),
                    ]);
            });

        DB::table('sections')
            ->whereNull('deleted_at')
            ->orderBy('season_id')
            ->orderBy('ruleset_id')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $section): void {
                DB::table('sections')
                    ->where('id', $section->id)
                    ->update([
                        'slug' => sprintf('section-temp-%d', $section->id),
                    ]);
            });

        $usedSlugs = [];

        DB::table('sections')
            ->whereNull('deleted_at')
            ->orderBy('season_id')
            ->orderBy('ruleset_id')
            ->orderBy('id')
            ->get(['id', 'name', 'season_id', 'ruleset_id'])
            ->each(function (object $section) use (&$usedSlugs): void {
                $scopeKey = "{$section->season_id}:{$section->ruleset_id}";
                $baseSlug = Str::slug($section->name) ?: 'section';
                $slug = $baseSlug;
                $suffix = 1;

                while (in_array("{$scopeKey}:{$slug}", $usedSlugs, true)) {
                    $slug = "{$baseSlug}-{$suffix}";
                    $suffix++;
                }

                DB::table('sections')
                    ->where('id', $section->id)
                    ->update(['slug' => $slug]);

                $usedSlugs[] = "{$scopeKey}:{$slug}";
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
