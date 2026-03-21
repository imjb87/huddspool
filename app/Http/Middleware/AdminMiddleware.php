<?php

namespace App\Http\Middleware;

use App\Enums\PermissionName;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->can(PermissionName::AccessAdminPanel->value)) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
