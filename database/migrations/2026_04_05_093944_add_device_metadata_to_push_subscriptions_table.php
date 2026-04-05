<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->string('device_label')->nullable()->after('content_encoding');
            $table->string('browser')->nullable()->after('device_label');
            $table->string('platform')->nullable()->after('browser');
            $table->text('user_agent')->nullable()->after('platform');
        });
    }

    public function down(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'device_label',
                'browser',
                'platform',
                'user_agent',
            ]);
        });
    }
};
