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
            //dashboard routes
            Route::prefix('page')->name('page.')->group(function () {
                Route::get('dashboard', 'index')->name('dashboard');
            });
            //users details routes
            Route::prefix('Users')->name('Users.')->group(function () {

                Route::get('Details', 'userview')->name('Details'); //load the user details page
                Route::get('Create', 'createUserView')->name('Create'); //load the create user page

                // Store user (for create)
                Route::post('store', 'create')->name('user.store');

                // Edit user (for edit form)
                Route::get('editUser/{id}', 'edit')->name('edit');

                // Update user (for edit)
                Route::put('updateUser/{id}', 'update')->name('user.update');
                // Delete user
                Route::delete('deleteUser/{id}', 'destroy')->name('user.delete');

                // Search Route for User Details
                Route::get('searchUsers', 'userview')->name('user.search'); // Search users by name, username, or email
            });

            //training routes
            Route::prefix('training')->name('training.')->group(function () {
                Route::get('Detail', 'trainingview')->name('Detail'); //load training details view
                Route::get('create', 'createtrainingview')->name('create'); //load the training create page
                Route::post('store', 'createtraining')->name('store'); // Store user (for create)
                Route::get('{id}/edit', 'trainingedit')->name('edit');
                Route::put('{id}/update', 'updatetraining')->name('update'); // Update training details
                Route::post('cost-breakdown/store/{trainingId}', 'storeCostBreakdown')->name('cost-breakdown.store');
                Route::post('{id}/update-tasks', 'updateTasks')->name('updateTasks');
            });

            //participant routes
            Route::prefix('participant')->name('participant.')->group(function () {
                Route::get('{id}/Detail', 'participantview')->name('Detail'); //load participant details view
                Route::get('{id}/create', 'createparticipant')->name('create'); //load participant create view
            });
            //budget routes
            Route::prefix('budget')->name('budget.')->group(function () {
                Route::get('Detail', 'budgetview')->name('Detail'); //load budget details view
                Route::get('Create', 'createBudgetView')->name('Create'); //load the create budget page
                Route::post('store', 'create')->name('store'); // Store user (for create)
            });

            //institute routes
            Route::prefix('institute')->name('institute.')->group(function () {
                Route::get('Detail', 'instituteview')->name('Detail');
                Route::get('create', 'createinstituteview')->name('create'); //load the institute create page
                Route::post('store', 'createinstitute')->name('store'); // Store user (for create)
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
