<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            $redirect = redirect()->to($user->homeUrl());

            if ($user->isStaff()) {
                $redirect->with('error', 'You do not have permission to access that page.');
            }

            return $redirect;
        }

        return $next($request);
    }
}
