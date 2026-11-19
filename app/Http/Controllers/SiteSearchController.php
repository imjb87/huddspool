<?php

namespace App\Http\Controllers;

use App\Domain\Search\BuildSearchResults;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\JsonResponse;

class SiteSearchController extends Controller
{
    public function __invoke(SearchRequest $request, BuildSearchResults $buildSearchResults): JsonResponse
    {
        return response()->json([
            'groups' => $buildSearchResults->forApi((string) $request->validated('q', '')),
        ]);
    }
}
