<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        $usedSlugs = [];

        DB::table('sections')
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

        Schema::table('sections', function (Blueprint $table) {
            $table->unique(['season_id', 'ruleset_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique(['season_id', 'ruleset_id', 'slug']);
            $table->unique('slug');
        });
    }
};
