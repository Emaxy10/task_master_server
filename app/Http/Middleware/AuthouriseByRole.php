<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthouriseByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = User::find(Auth::id());


         if (!$user) {
            return response()->json(['error' => 'Not Unauthenticated.'], 401);
        }
        if (!$user->hasRole($roles)) {
            return response()->json(['error' => 'User does not have role']);
        }

         // Attach the role to the request
        $request->attributes->set('role', $roles);


        return $next($request);
    }
}
