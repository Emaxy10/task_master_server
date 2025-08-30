<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use illuminate\Support\Facades\Auth;

class UserSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $sub): Response
    {
        $user = User::find(Auth::id());
        if (!$user->hasSubscription($sub)) {
            return response()->json(['error' => 'User does not have Subscription']);
        }

        return $next($request);
    }
}
