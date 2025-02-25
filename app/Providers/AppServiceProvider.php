<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define all the navbar views for different roles
        $navbars = [
            'User.components.navbar',
            'Admin.HRAdmin.component.navbar',
            'Admin.CATCAdmin.component.navbar',
            'SuperAdmin.component.navbar',
        ];

        View::composer($navbars, function ($view) {
            if (Auth::check()) { // Ensure the user is authenticated
                $notifications = Notification::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->get();
            } else {
                $notifications = collect([]); // Return an empty collection if not logged in
            }

            $view->with('notifications', $notifications);
        });
        Schema::defaultStringLength(191);
    }
}
