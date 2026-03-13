<?php

use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;
use Spatie\ResponseCache\Hasher\DefaultHasher;
use Spatie\ResponseCache\Replacers\CsrfTokenReplacer;
use Spatie\ResponseCache\Serializers\JsonSerializer;

return [
    /*
     * Determine if the response cache middleware should be enabled.
     */
    'enabled' => env('RESPONSE_CACHE_ENABLED', true),

    'cache' => [
        /*
         * Here you may define the cache store that should be used
         * to store requests. This can be the name of any store
         * that is configured in your app's cache.php config.
         */
        'store' => env('RESPONSE_CACHE_DRIVER', 'file'),

        /*
         * The default number of seconds responses will be cached
         * when using the default CacheProfile settings.
         */
        'lifetime_in_seconds' => (int) env('RESPONSE_CACHE_LIFETIME', 60 * 60 * 24 * 7),

        /*
         * If your cache driver supports tags, you may specify a tag
         * name here. All responses will be tagged. When clearing
         * the response cache only tagged items will be flushed.
         */
        'tag' => env('RESPONSE_CACHE_TAG', ''),
    ],

    'bypass' => [
        /*
         * The header name that will force a bypass of the cache.
         */
        'header_name' => env('CACHE_BYPASS_HEADER_NAME'),

        /*
         * The header value that will force a cache bypass.
         */
        'header_value' => env('CACHE_BYPASS_HEADER_VALUE'),
    ],

    'debug' => [
        /*
         * Determines if debug headers are added to cached responses.
         */
        'enabled' => env('APP_DEBUG', false),

        /*
         * The name of the HTTP header containing the cache timestamp.
         */
        'cache_time_header_name' => env('RESPONSE_CACHE_HEADER_NAME', 'X-Cache-Time'),

        /*
         * The name of the header for the cache status.
         */
        'cache_status_header_name' => 'X-Cache-Status',

        /*
         * The header name for the cache age in seconds.
         */
        'cache_age_header_name' => env('RESPONSE_CACHE_AGE_HEADER_NAME', 'X-Cache-Age'),

        /*
         * The header name used for the response cache key.
         */
        'cache_key_header_name' => 'X-Cache-Key',
    ],

    /*
     * These query parameters will be ignored when generating the cache key.
     */
    'ignored_query_parameters' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
    ],

    /*
     * The given class determines if a request should be cached.
     */
    'cache_profile' => CacheAllSuccessfulGetRequests::class,

    /*
     * This class is responsible for generating a hash for a request.
     */
    'hasher' => DefaultHasher::class,

    /*
     * This class is responsible for serializing responses.
     */
    'serializer' => JsonSerializer::class,

    /*
     * Here you may define the replacers that will replace dynamic content
     * from the response.
     */
    'replacers' => [
        CsrfTokenReplacer::class,
    ],
];
