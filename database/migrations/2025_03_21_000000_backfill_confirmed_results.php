<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('results')
            ->where('is_confirmed', false)
            ->whereIn('id', function ($query) {
                $query->select('result_id')
                    ->from('frames')
                    ->groupBy('result_id')
                    ->havingRaw('COUNT(*) >= 10');
            })
            ->update(['is_confirmed' => true]);
    }

    public function down(): void
    {
        // no-op
    }
};

