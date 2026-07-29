<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGptAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            return new JsonResponse([
                'message' => 'This Huddspool account is not authorised for GPT administration.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
