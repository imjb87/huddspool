<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('invitations');

        if (Schema::hasColumn('users', 'invitation_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['invitation_token']);
                $table->dropColumn('invitation_token');
            });
        }

        DB::table('notification_settings')
            ->where('notification_type', 'App\\Notifications\\InviteNotification')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The invitation flow has been replaced by Laravel's password broker.
    }
};
