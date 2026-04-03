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
        Schema::table('news', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        $existingSlugs = [];

        DB::table('news')
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->each(function (object $article) use (&$existingSlugs): void {
                $baseSlug = Str::slug($article->title) ?: 'news';
                $slug = $baseSlug;
                $index = 1;

                while (in_array($slug, $existingSlugs, true)) {
                    $slug = "{$baseSlug}-{$index}";
                    $index++;
                }

                $existingSlugs[] = $slug;

                DB::table('news')
                    ->where('id', $article->id)
                    ->update(['slug' => $slug]);
            });

        Schema::table('news', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
