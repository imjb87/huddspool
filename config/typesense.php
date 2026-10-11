<?php

return [
    'host' => env('TYPESENSE_HOST'),
    'api_key' => env('TYPESENSE_API_KEY'),
    'search_api_key' => env('TYPESENSE_SEARCH_API_KEY'),
    'collection_prefix' => env('TYPESENSE_COLLECTION_PREFIX', 'huddspool'),
    'search_timeout' => (int) env('TYPESENSE_SEARCH_TIMEOUT', 2),
    'index_timeout' => (int) env('TYPESENSE_INDEX_TIMEOUT', 15),
];
