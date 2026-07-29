<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Services\Gpt\AdminResourceReader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function capabilities(AdminResourceReader $reader): JsonResponse
    {
        return response()->json([
            'resources' => $reader->capabilities(),
        ]);
    }

    public function index(Request $request, string $resource, AdminResourceReader $reader): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        return response()->json($reader->list(
            resource: $resource,
            search: $validated['search'] ?? null,
            limit: (int) ($validated['limit'] ?? 20),
        ));
    }

    public function show(string $resource, int $record, AdminResourceReader $reader): JsonResponse
    {
        return response()->json([
            'resource' => $resource,
            'record' => $reader->find($resource, $record),
        ]);
    }
}
