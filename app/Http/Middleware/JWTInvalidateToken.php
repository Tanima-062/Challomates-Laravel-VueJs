<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class JWTInvalidateToken
{
    /**
     * Invalidate a token based on conditions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('employee')->user();
        if ($user && $user->status !== User::STATUS_ACTIVE) {
            auth('employee')->invalidate(true);
            auth('employee')->logout();

            return response()->json([
                'message'   =>'Unauthorized.'
            ], 401);

        }
        return $next($request);
    }
}
