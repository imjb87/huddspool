<?php

use App\Models\Venue;
use App\Support\Geocoding\NominatimGeocoder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('telephone');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Venue::query()
            ->where(function ($query) {
                $query->whereNull('latitude')
                    ->orWhereNull('longitude');
            })
            ->orderBy('id')
            ->chunkById(25, function ($venues) {
                /** @var \App\Models\Venue $venue */
                foreach ($venues as $venue) {
                    $coordinates = NominatimGeocoder::geocode($venue->address);

                    if (! $coordinates) {
                        continue;
                    }

                    $venue->forceFill([
                        'latitude' => $coordinates['lat'],
                        'longitude' => $coordinates['lng'],
                    ])->saveQuietly();

                    // Respect API usage policies by spacing out requests slightly.
                    usleep(250000); // 0.25s
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }

};
