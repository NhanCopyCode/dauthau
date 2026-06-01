<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Roles are passed as individual arguments, e.g.:
     *   - `CheckRole:admin`
     *   - `CheckRole:admin,operator`
     *
     * If the authenticated user does not have any of the specified
     * roles the request is aborted with a 403 response.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized access.');
        }

        if (! $user->hasAnyRole($roles)) {
            abort(403, 'Insufficient permissions.');
        }

        return $next($request);
    }
}
