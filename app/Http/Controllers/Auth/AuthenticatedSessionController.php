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
            return redirect()->intended(route('Admin.HRAdmin.page.dashboard', absolute: false));
        } elseif ($authUserRole == 'catcadmin' && $authUserDivision == '2') {
            return redirect()->intended(route('Admin.CATCAdmin.page.dashboard', absolute: false));
        } elseif ($authUserRole == 'user') {
            return redirect()->intended(route('User.page.dashboard', absolute: false));
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
