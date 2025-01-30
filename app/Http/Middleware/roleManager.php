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
    public function handle(Request $request, Closure $next, $role, $division, $section): Response
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
                switch ($division) {
                    case 'IT':
                        if ($authUserRole == 'user' && $authUserDivision == '3') {
                            return $next($request);
                        }
                        break;
                    case 'Finance':
                        if ($authUserRole == 'user' && $authUserDivision == '4') {
                            return $next($request);
                        }
                        break;
                    case 'SCM':
                        if ($authUserRole == 'user' && $authUserDivision == '5') {
                            return $next($request);
                        }
                        break;
                    case 'Marketing':
                        if ($authUserRole == 'user' && $authUserDivision == '6') {
                            return $next($request);
                        }
                        break;
                    case 'Security':
                        if ($authUserRole == 'user' && $authUserDivision == '7') {
                            return $next($request);
                        }
                        break;
                    case 'CATC':
                        switch ($section) {
                            case 'wing-1':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '1') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-2':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '2') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-3':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '3') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-4':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '4') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-5':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '5') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-6':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '6') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-7':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '7') {
                                    return $next($request);
                                }
                                break;
                            case 'wing-8':
                                if ($authUserRole == 'user' && $authUserDivision == '2' && $authUserSection == '8') {
                                    return $next($request);
                                }
                                break;
                        }
                        break;
                        return redirect()->route('login');
                }
                break;
                return redirect()->route('login');
        }

        return redirect()->route('login');
    }
}
