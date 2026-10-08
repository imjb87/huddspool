<x-ui-empty-state
    layout="search"
    title="No results found"
    description="No players, teams, or venues matched that search. Try a different name."
    wire:loading.remove
    wire:target="searchTerm"
    data-search-no-results
/>
