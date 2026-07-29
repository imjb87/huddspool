<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectedAdminController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'administrator' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
            ],
            'message' => 'Connected to Huddspool with administrator access.',
        ]);
    }
}
