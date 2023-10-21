<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role_id): Response
    {
        // dd('user yg login' . auth()->user()->role_id . ' | hakakses yg boleh:' . $role_id);
        if (auth()->user()->role_id == $role_id) {
            return $next($request);
        }
        return redirect()->back()->with('deleted', 'Anda tidak bisa mengakses halaman yang dituju');
    }
}
