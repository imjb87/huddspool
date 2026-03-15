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
            $table->string('slug')->nullable()->after('name');
        });

        $usedSlugs = [];

        DB::table('sections')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function (object $section) use (&$usedSlugs): void {
                $baseSlug = Str::slug($section->name) ?: 'section';
                $slug = $baseSlug;
                $suffix = 1;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = "{$baseSlug}-{$suffix}";
                    $suffix++;
                }

                DB::table('sections')
                    ->where('id', $section->id)
                    ->update(['slug' => $slug]);

                $usedSlugs[] = $slug;
            });

        Schema::table('sections', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
