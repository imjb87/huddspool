<?php

namespace App\Console\Commands;

use App\Support\SitemapBuilder;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle(SitemapBuilder $sitemapBuilder): int
    {
        $path = $sitemapBuilder->writeTo(public_path('sitemap.xml'));

        $this->info(sprintf('Sitemap generated: %s', $path));

        return self::SUCCESS;
    }
}
