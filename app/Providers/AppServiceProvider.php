<?php

namespace App\Providers;

use App\Models\Notification;
use App\Services\EmpApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(EmpApiService::class, function ($app) {
            return new EmpApiService();
        });
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
                $viewName = $view->getName(); // Get the view name being rendered

                // Check if the view is for SuperAdmin
                if ($viewName === 'SuperAdmin.components.navbar') {
                    // Fetch notifications for SuperAdmin based on user_role
                    $notifications = Notification::where('user_role', 'superadmin')
                        ->where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->take(4)
                        ->get();

                    // Count total pending notifications for SuperAdmin
                    $totalPending = Notification::where('user_role', 'superadmin')
                        ->where('status', 'pending')
                        ->count();
                } else {
                    // For other roles, fetch notifications based on user_id
                    $notifications = Notification::where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->take(4)
                        ->get();

                    // Count total pending notifications for the current user
                    $totalPending = Notification::where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->count();
                }
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
