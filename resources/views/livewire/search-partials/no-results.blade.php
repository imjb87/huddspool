<x-ui-empty-state
    layout="search"
    title="No results found"
    description="We couldn’t find anything with that term. Please try again."
    wire:loading.remove
    wire:target="searchTerm"
    data-search-no-results
/>
