<?php

namespace Tests\Feature;

use App\Filament\Resources\ExpulsionResource;
use App\Filament\Resources\FixtureResource;
use App\Filament\Resources\KnockoutResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\NewsResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\RulesetResource;
use App\Filament\Resources\SeasonEntryResource;
use App\Filament\Resources\SeasonResource;
use App\Filament\Resources\SectionResource;
use App\Filament\Resources\SupportTicketResource;
use App\Filament\Resources\TeamResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VenueResource;
use App\Models\User;
use EslamRedaDiv\FilamentCopilot\Discovery\ResourceInspector;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentCopilotResourceDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panel_discovers_copilot_enabled_resources(): void
    {
        User::factory()->create([
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel('admin');

        $resourceClasses = collect(app(ResourceInspector::class)->discoverResources('admin'))
            ->pluck('resource');

        $this->assertTrue($resourceClasses->contains(UserResource::class));
        $this->assertTrue($resourceClasses->contains(NewsResource::class));
        $this->assertTrue($resourceClasses->contains(SeasonResource::class));
        $this->assertTrue($resourceClasses->contains(SectionResource::class));
        $this->assertTrue($resourceClasses->contains(TeamResource::class));
        $this->assertTrue($resourceClasses->contains(VenueResource::class));
        $this->assertTrue($resourceClasses->contains(KnockoutResource::class));
        $this->assertTrue($resourceClasses->contains(RulesetResource::class));
        $this->assertTrue($resourceClasses->contains(PageResource::class));
        $this->assertTrue($resourceClasses->contains(SeasonEntryResource::class));
        $this->assertTrue($resourceClasses->contains(FixtureResource::class));
        $this->assertTrue($resourceClasses->contains(ExpulsionResource::class));
        $this->assertTrue($resourceClasses->contains(SupportTicketResource::class));
        $this->assertTrue($resourceClasses->contains(MediaResource::class));
    }
}
