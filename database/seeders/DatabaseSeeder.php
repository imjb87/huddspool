<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SiteAuthorizationSeeder::class,
            NotificationSettingsSeeder::class,
            HuddspoolSeeder::class,
            PageSeeder::class,
        ]);

        User::factory()->admin()->create([
            'name' => 'John Bell',
            'email' => 'john@thebiggerboat.co.uk',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

    }
}
