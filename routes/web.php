<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\superadmincontroller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// super admin routes
Route::middleware(['auth', 'verified', 'roleManager:superadmin, 1, 0'])->group(function () {
    Route::controller(superadmincontroller::class)->group(function () {
        Route::prefix('SuperAdmin')->name('SuperAdmin.')->group(function () {
            //users details routes
            Route::prefix('page')->name('page.')->group(function () {
                Route::get('dashboard', 'index')->name('dashboard');

                Route::get('UserDetails', 'userview')->name('UserDetails'); //load the user details page
                Route::get('createUser', 'createUserView')->name('createUser'); //load the create user page 

                // Store user (for create)
                Route::post('store', 'create')->name('user.store');

                // Edit user (for edit form)
                Route::get('editUser/{id}', 'edit')->name('user.edit');

                // Update user (for edit)
                Route::put('updateUser/{id}', 'update')->name('user.update');
                // Delete user
                Route::delete('deleteUser/{id}', 'destroy')->name('user.delete');
            });

            //training routes
            Route::prefix('training')->name('training.')->group(function () {
                Route::get('Detail', 'trainingview')->name('Detail'); //load training details view
            });

            //participant routes
            Route::prefix('participant')->name('participant')->group(function () {
                Route::get('Detail', 'participantview')->name('Detail');
            });

            //budget routes
            Route::prefix('budget')->name('budget.')->group(function () {
                Route::get('Detail', 'budgetview')->name('Detail'); //load budget details view
            });

            //institute routes
            Route::prefix('institute')->name('institute.')->group(function () {
                Route::get('Detail', 'instituteview')->name('Detail');
            });

            //trainers routes
            Route::prefix('trainer')->name('trainer.')->group(function () {
                Route::get('Detail', 'trainerview')->name('Detail'); //load the trainer details view
            });

            //approvel routes
            Route::prefix('approvel')->name('approvel.')->group(function () {
                Route::get('Detail', 'approvelview')->name('Detail'); //load the apprvel view page
            });

            //reports routes
            Route::prefix('report')->name('report.')->group(function () {
                Route::get('trainingSummary', 'trainingsummaryView')->name('trainingSummary'); // load the training summary view
            });
        });
    });
});


//hr admin routes
Route::get('Admin/HRAdmin/index', function () {
    return view('Admin.HRAdmin.index');
})->middleware(['auth', 'verified', 'roleManager:hradmin, 1, 0'])->name('Admin.HRAdmin.index');

//catc admin routes
Route::get('Admin/CATCAdmin/index', function () {
    return view('Admin.CATCAdmin.index');
})->middleware(['auth', 'verified'])->name('Admin.CATCAdmin.index');

//user routes
Route::get('User/ITUser/index', function () {
    return view('User.ITUser.index');
})->middleware(['auth', 'verified'])->name('User.ITUser.index');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
