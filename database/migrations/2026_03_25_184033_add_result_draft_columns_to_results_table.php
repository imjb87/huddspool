<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedInteger('draft_version')->default(0)->after('submitted_at');
            $table->foreignId('draft_updated_by')->nullable()->after('draft_version')->constrained('users')->nullOnDelete();
            $table->json('draft_state')->nullable()->after('draft_updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draft_updated_by');
            $table->dropColumn(['draft_version', 'draft_state']);
        });
    }
};
