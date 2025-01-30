<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $authUserRole = Auth::user()->role;
        $authUserDivision = Auth::user()->division_id;
        $authUserSection = Auth::user()->section_id;

        if ($authUserRole == 'superadmin' && $authUserDivision == '1') {
            return redirect()->intended(route('SuperAdmin.page.dashboard', absolute: false));
        } elseif ($authUserRole == 'hradmin' && $authUserDivision == '1') {
            return redirect()->intended(route('Admin.HRAdmin.index', absolute: false));
        } elseif ($authUserRole == 'catcadmin' && $authUserDivision == '2') {
            return redirect()->intended(route('Admin.CATCAdmin.index', absolute: false));
        } elseif ($authUserRole == 'user') {
            if ($authUserDivision == '3') {
                return redirect()->intended(route('User.ITUser.index', absolute: false));
            } elseif ($authUserDivision == '4') {
                return redirect()->intended(route('User.FinanceUser.index', absolute: false));
            } elseif ($authUserDivision == '5') {
                return redirect()->intended(route('User.SCMUser.index', absolute: false));
            } elseif ($authUserDivision == '6') {
                return redirect()->intended(route('User.MarketingUser.index', absolute: false));
            } elseif ($authUserDivision == '2') {
                if ($authUserSection == '1') {
                    return redirect()->intended(route('User.CATCUser.wing_1.index', absolute: false));
                } elseif ($authUserSection == '2') {
                    return redirect()->intended(route('User.CATCUser.wing_2.index', absolute: false));
                } elseif ($authUserSection == '3') {
                    return redirect()->intended(route('User.CATCUser.wing_3.index', absolute: false));
                } elseif ($authUserSection == '4') {
                    return redirect()->intended(route('User.CATCUser.wing_4.index', absolute: false));
                } elseif ($authUserSection == '5') {
                    return redirect()->intended(route('User.CATCUser.wing_5', absolute: false));
                } elseif ($authUserSection == '6') {
                    return redirect()->intended(route('User.CATCUser.wing_6.index', absolute: false));
                } elseif ($authUserSection == '7') {
                    return redirect()->intended(route('User.CATCUser.wing_7.index', absolute: false));
                } elseif ($authUserSection == '8') {
                    return redirect()->intended(route('User.CATCUser.wing_8.index', absolute: false));
                } else {
                    return redirect()->intended(route('login', absolute: false));
                }
            } else {
                return redirect()->intended(route('login', absolute: false));
            }
        } else {
            return redirect()->intended(route('login', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
