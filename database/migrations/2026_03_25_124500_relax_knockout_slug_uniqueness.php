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
        Schema::table('knockouts', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        $usedSlugs = [];

        DB::table('knockouts')
            ->orderBy('season_id')
            ->orderBy('id')
            ->get(['id', 'name', 'season_id'])
            ->each(function (object $knockout) use (&$usedSlugs): void {
                $scopeKey = (string) $knockout->season_id;
                $baseSlug = Str::slug($knockout->name) ?: 'knockout';
                $slug = $baseSlug;
                $suffix = 1;

                while (in_array("{$scopeKey}:{$slug}", $usedSlugs, true)) {
                    $slug = "{$baseSlug}-{$suffix}";
                    $suffix++;
                }

                DB::table('knockouts')
                    ->where('id', $knockout->id)
                    ->update(['slug' => $slug]);

                $usedSlugs[] = "{$scopeKey}:{$slug}";
            });

        Schema::table('knockouts', function (Blueprint $table) {
            $table->unique(['season_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockouts', function (Blueprint $table) {
            $table->dropUnique(['season_id', 'slug']);
            $table->unique('slug');
        });
    }
};
