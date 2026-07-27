<?php

use App\Notifications\FixtureResultOutstandingNotification;
use App\Notifications\InviteNotification;
use App\Notifications\KnockoutMatchReadyNotification;
use App\Notifications\KnockoutMatchReminderNotification;
use App\Notifications\KnockoutResultOutstandingNotification;
use App\Notifications\LeagueNightTonightNotification;
use App\Notifications\LeagueResultSubmittedNotification;
use App\Notifications\MatchNightStartedNotification;
use App\Notifications\NewsPublishedNotification;
use App\Notifications\TuesdayResultCatchupNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type')->unique();
            $table->string('name');
            $table->string('description');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('notification_settings')->insert([
            $this->setting(LeagueNightTonightNotification::class, 'League night tonight', 'On the day of a league fixture.', $now),
            $this->setting(MatchNightStartedNotification::class, 'Match night started', 'When a league match is ready to begin.', $now),
            $this->setting(FixtureResultOutstandingNotification::class, 'Outstanding league result', 'When a completed league fixture still needs a result.', $now),
            $this->setting(TuesdayResultCatchupNotification::class, 'Tuesday result catch-up', 'A Sunday reminder for an outstanding Tuesday result.', $now),
            $this->setting(KnockoutMatchReminderNotification::class, 'Knockout match reminder', 'Before an upcoming knockout match.', $now),
            $this->setting(KnockoutResultOutstandingNotification::class, 'Outstanding knockout result', 'When a completed knockout match still needs a result.', $now),
            $this->setting(KnockoutMatchReadyNotification::class, 'Knockout match ready', 'When the teams in a knockout match are confirmed.', $now),
            $this->setting(LeagueResultSubmittedNotification::class, 'League result submitted', 'When a league result is submitted for confirmation.', $now),
            $this->setting(NewsPublishedNotification::class, 'News published', 'When a news article is published.', $now),
            $this->setting(InviteNotification::class, 'Account invitation', 'When an administrator invites someone to create an account.', $now),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }

    /**
     * @return array{notification_type: string, name: string, description: string, enabled: bool, created_at: mixed, updated_at: mixed}
     */
    private function setting(string $notificationType, string $name, string $description, mixed $timestamp): array
    {
        return [
            'notification_type' => $notificationType,
            'name' => $name,
            'description' => $description,
            'enabled' => true,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
};
