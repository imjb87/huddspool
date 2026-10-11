<?php

namespace App\Support\SiteSearch;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TypesenseClient
{
    public function configured(): bool
    {
        return filled(config('typesense.host')) && filled(config('typesense.api_key'));
    }

    public function searchConfigured(): bool
    {
        return filled(config('typesense.host'))
            && (filled(config('typesense.search_api_key')) || filled(config('typesense.api_key')));
    }

    public function request(bool $search = false, ?int $timeout = null): PendingRequest
    {
        $apiKey = $search
            ? (config('typesense.search_api_key') ?: config('typesense.api_key'))
            : config('typesense.api_key');

        return Http::acceptJson()
            ->withHeaders(['X-TYPESENSE-API-KEY' => (string) $apiKey])
            ->timeout($timeout ?? (int) config(
                $search ? 'typesense.search_timeout' : 'typesense.index_timeout',
                $search ? 2 : 15,
            ));
    }

    public function url(string $path): string
    {
        return rtrim((string) config('typesense.host'), '/').'/'.ltrim($path, '/');
    }

    public function collection(string $type): string
    {
        return trim((string) config('typesense.collection_prefix', 'huddspool'), '_').'_'.strtolower($type);
    }
}
