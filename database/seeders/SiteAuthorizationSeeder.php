<?php

namespace Database\Seeders;

use App\Domain\Authorization\SiteAuthorization;
use Illuminate\Database\Seeder;

class SiteAuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();
    }
}
