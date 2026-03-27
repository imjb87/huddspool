<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('season_entries', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('notes');
            $table->string('payment_status')->nullable()->after('payment_provider');
            $table->string('stripe_checkout_session_id')->nullable()->after('payment_status');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
            $table->timestamp('payment_completed_at')->nullable()->after('stripe_payment_intent_id');
            $table->string('payment_currency', 3)->nullable()->after('payment_completed_at');
            $table->decimal('payment_amount', 8, 2)->nullable()->after('payment_currency');
            $table->json('payment_metadata')->nullable()->after('payment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('season_entries', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'payment_status',
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'payment_completed_at',
                'payment_currency',
                'payment_amount',
                'payment_metadata',
            ]);
        });
    }
};
