<?php

use App\\Models\\Section;
use App\\Models\\Team;
use App\\Models\\User;
use App\\Models\\Venue;

return [
    'driver' => env('SCOUT_DRIVER', 'typesense'),
    'prefix' => env('SCOUT_PREFIX', 'huddspool_'),
    'queue' => env('SCOUT_QUEUE', true),
    'after_commit' => true,
    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],
    'soft_delete' => false,
    'identify' => env('SCOUT_IDENTIFY', false),
    'typesense' => [
        'client-settings' => [
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'localhost'),
                    'port' => env('TYPESENSE_PORT', 8108),
                    'path' => env('TYPESENSE_PATH', ''),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'nearest_node' => null,
            'connection_timeout_seconds' => 2,
            'healthcheck_interval_seconds' => 30,
            'num_retries' => 3,
            'retry_interval_seconds' => 1,
        ],
        'model-settings' => [
            User::class => [
                'collection-schema' => [
                    'fields' => [
                        ['name' => 'name', 'type' => 'string'],
                        ['name' => 'team_name', 'type' => 'string', 'optional' => true],
                        ['name' => 'team_shortname', 'type' => 'string', 'optional' => true],
                        ['name' => 'section_names', 'type' => 'string[]', 'optional' => true],
                        ['name' => 'created_at', 'type' => 'int64'],
                    ],
                    'default_sorting_field' => 'created_at',
                ],
                'search-parameters' => [
                    'query_by' => 'name,team_name,team_shortname,section_names',
                    'prefix' => 'true,true,false,false',
                ],
            ],
            Team::class => [
                'collection-schema' => [
                    'fields' => [
                        ['name' => 'name', 'type' => 'string'],
                        ['name' => 'shortname', 'type' => 'string', 'optional' => true],
                        ['name' => 'venue_name', 'type' => 'string', 'optional' => true],
                        ['name' => 'section_names', 'type' => 'string[]', 'optional' => true],
                        ['name' => 'created_at', 'type' => 'int64'],
                    ],
                    'default_sorting_field' => 'created_at',
                ],
                'search-parameters' => [
                    'query_by' => 'name,shortname,venue_name,section_names',
                    'prefix' => 'true,true,false,false',
                ],
            ],
            Venue::class => [
                'collection-schema' => [
                    'fields' => [
                        ['name' => 'name', 'type' => 'string'],
                        ['name' => 'address', 'type' => 'string', 'optional' => true],
                        ['name' => 'created_at', 'type' => 'int64'],
                    ],
                    'default_sorting_field' => 'created_at',
                ],
                'search-parameters' => [
                    'query_by' => 'name,address',
                    'prefix' => 'true,false',
                ],
            ],
            Section::class => [
                'collection-schema' => [
                    'fields' => [
                        ['name' => 'name', 'type' => 'string'],
                        ['name' => 'ruleset_name', 'type' => 'string', 'optional' => true],
                        ['name' => 'season_name', 'type' => 'string', 'optional' => true],
                        ['name' => 'team_names', 'type' => 'string[]', 'optional' => true],
                        ['name' => 'search_terms', 'type' => 'string', 'optional' => true],
                        ['name' => 'created_at', 'type' => 'int64'],
                    ],
                    'default_sorting_field' => 'created_at',
                ],
                'search-parameters' => [
                    'query_by' => 'name,ruleset_name,season_name,team_names,search_terms',
                    'prefix' => 'true,false,false,false,false',
                ],
            ],
        ],
    ],
];
