<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Seed CMS pages required for local development.
     */
    public function run(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'handbook'],
            [
                'title' => 'Handbook',
                'content' => <<<'HTML'
<p>This is the local handbook placeholder page.</p>
<p>Replace this content in the admin area once the real handbook copy is available.</p>
HTML,
            ],
        );
    }
}
