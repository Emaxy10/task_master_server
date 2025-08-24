<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthouriseByPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {

        $roleName = $request->attributes->get('role');
        $role = Role::where('name', $roleName)->first();

        if(!$role || !$role->hasPermission($permission)){
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        return $next($request);
    }
}
