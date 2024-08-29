<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermission($permission)) {
            $userpermission = $user->permissions->first();
            return redirect($userpermission->url)->with('error',"You don\'t have access");
        }

        return $next($request);
    }
}
