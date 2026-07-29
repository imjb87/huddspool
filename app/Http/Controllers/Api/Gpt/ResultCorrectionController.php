<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\CorrectResultRequest;
use App\Models\Result;
use App\Services\Gpt\CorrectResult;
use Illuminate\Http\JsonResponse;

class ResultCorrectionController extends Controller
{
    public function __invoke(CorrectResultRequest $request, Result $result, CorrectResult $correctResult): JsonResponse
    {
        $audit = $correctResult->handle(
            administrator: $request->user(),
            result: $result,
            frames: $request->array('frames'),
            expectedDraftVersion: $request->integer('expected_draft_version'),
            reason: $request->string('reason')->toString(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => 'The result was corrected.',
            'change' => ['before' => $audit->before, 'after' => $audit->after],
            'audit_id' => $audit->id,
        ]);
    }
}
