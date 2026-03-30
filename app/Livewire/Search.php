<?php

namespace App\Livewire;

use App\Support\SiteSearch\BuildSearchResults;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Search extends Component
{
    public bool $isOpen = false;

    public mixed $searchTerm = '';

    #[On('openSearch')]
    public function openSearch(): void
    {
        $this->isOpen = true;
        $this->searchTerm = '';
    }

    public function closeSearch(): void
    {
        $this->isOpen = false;
        $this->searchTerm = '';
    }

    public function render(): View
    {
        $normalizedSearchTerm = $this->normalizedSearchTerm();

        return view('livewire.search', [
            'resultGroups' => $this->resultGroups,
            'searchTermLength' => strlen($normalizedSearchTerm),
        ]);
    }

    /**
     * @return array<string, array{heading: string, badge: string, route: string, results: Collection}>
     */
    #[Computed]
    public function resultGroups(): array
    {
        return app(BuildSearchResults::class)->build($this->normalizedSearchTerm());
    }

    private function normalizedSearchTerm(): string
    {
        if (! is_string($this->searchTerm)) {
            return '';
        }

        return trim($this->searchTerm);
    }
}
