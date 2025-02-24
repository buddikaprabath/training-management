<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class roleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $division = null, $section = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $authUserRole = Auth::user()->role;
        $authUserDivision = Auth::user()->division_id;
        $authUserSection = Auth::user()->section_id;

        switch ($role) {
            case 'superadmin':
                if ($authUserRole == 'superadmin' && $authUserDivision == '1') {
                    return $next($request);
                }
                break;
            case 'hradmin':
                if ($authUserRole == 'hradmin' && $authUserDivision == '1') {
                    return $next($request);
                }
                break;
            case 'catcadmin':
                if ($authUserRole == 'catcadmin' && $authUserDivision == '2') {
                    return $next($request);
                }
                break;
            case 'user':
                if ($authUserRole == 'user') {
                    return $next($request);
                }
                break;
                return redirect()->route('login');
        }

        return redirect()->route('login');
    }
}
