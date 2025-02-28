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
            'Admin.HRAdmin.components.navbar',
            'Admin.CATCAdmin.components.navbar',
            'SuperAdmin.components.navbar',
        ];

        View::composer($navbars, function ($view) {
            if (Auth::check()) { // Ensure the user is authenticated
                // Fetch the latest 4 notifications
                $notifications = Notification::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();

                // Count total pending notifications
                $totalPending = Notification::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->count();
            } else {
                $notifications = collect([]); // Return an empty collection if not logged in
                $totalPending = 0; // No pending notifications if not logged in
            }

            // Pass the notifications and totalPending count to the view
            $view->with('notifications', $notifications)
                ->with('totalPending', $totalPending);
        });

        Schema::defaultStringLength(191);
    }
}
