<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Production MySQL: `is_overridden` was NOT NULL with no default,
        // so any insert omitting it failed with SQLSTATE 1364.
        // Give the column itself a default so it is safe even outside Eloquent.
        DB::statement("ALTER TABLE `results` MODIFY `is_overridden` TINYINT(1) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `results` MODIFY `is_overridden` TINYINT(1) NOT NULL");
    }
};
