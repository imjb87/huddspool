<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            'gpt_action_audits',
            'oauth_device_codes',
            'oauth_refresh_tokens',
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'personal_access_tokens',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These tables belonged to the retired GPT integration and are not restored.
    }
};
