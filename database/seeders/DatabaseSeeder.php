<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use App\Support\SiteAuthorization;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(HuddspoolSeeder::class);
        $this->call(PageSeeder::class);

        $admin = User::factory()->create([
            'name' => 'John Bell',
            'email' => 'john@thebiggerboat.co.uk',
            'is_admin' => true,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        SiteAuthorization::assignRole($admin, RoleName::Admin);
    }
}
