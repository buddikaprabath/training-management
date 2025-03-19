<?php

use App\Models\Training;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\Usercontroller;
use App\Http\Controllers\Admin\HRAdmincontroller;
use App\Http\Controllers\Admin\CATCAdmincontroller;
use App\Http\Controllers\Superadmin\superadmincontroller;

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
                Route::delete('deleteTraining/{id}', 'trainingdestroy')->name('Training.delete'); // Delete user
                Route::post('documents/store', 'storeTrainingDocument')->name('documents.store');
                Route::get('costDetail/{id}', 'viewCost')->name('costDetail');
                Route::get('costbreak/{id}', 'getCostBreakdownData')->name('costbreak');
                Route::delete('cost-breakdown/delete/{id}', 'costBreakDelete')->name('cost-breakdown.delete');
                Route::put('{id}/cost-breakdown/update', 'updateCostBreakdown')->name('cost-breakdown.update');
                Route::put('update-status/{trainingId}', 'updateStatus')->name('update-status');
                Route::post('subject/store/{trainingId}', 'storeSubject')->name('subject.store');
            });

            //participant routes
            Route::prefix('participant')->name('participant.')->group(function () {
                Route::get('{id}/Detail', 'participantview')->name('Detail'); //load participant details view
                Route::get('{id}/create', 'createparticipant')->name('create'); //load participant create view
                Route::post('store', 'participantstore')->name('store');
                Route::get('{id}/edit', 'participantedit')->name('edit');
                Route::put('update/{id}', 'updateparticipant')->name('update');
                Route::get('export-participant-columns', 'exportParticipantColumns')->name('export-participant-columns');
                Route::post('import-participants', 'importParticipants')->name('import-participants');
                Route::post('documents/store/{id}', 'storeParticipantDocument')->name('documents.store');
                Route::delete('delete/{id}', 'destroyparticipant')->name('delete');
                Route::post('grade/store', 'gradeStore')->name('grade.store');
            });
            //budget routes
            Route::prefix('budget')->name('budget.')->group(function () {
                Route::get('Detail', 'budgetview')->name('Detail'); //load budget details view
                Route::get('Create', 'createBudgetView')->name('Create'); //load the create budget page
                Route::post('store', 'budgetstore')->name('store'); // Store user (for create)
            });

            //institute routes
            Route::prefix('institute')->name('institute.')->group(function () {
                Route::get('Detail', 'instituteview')->name('Detail');
                Route::get('create', 'instituteCreate')->name('create');
                Route::post('/store', 'Institutestore')->name('store');
                Route::get('{id}/edit', 'instituteedit')->name('edit');
                Route::put('/update/{id}', 'Instituteupdate')->name('update');
                Route::delete('{id}/delete', 'instituteDelete')->name('delete');
                Route::get('search', 'Institutesearch')->name('search');
            });

            //trainers routes
            Route::prefix('trainer')->name('trainer.')->group(function () {
                Route::get('{id}/Detail', 'trainerview')->name('Detail'); //load the trainer details view
                Route::get('{id}/Create', 'trainerCreate')->name('Create');
                Route::get('search', 'trainerSearch')->name('search');
                Route::post('store', 'trainerStore')->name('store');
                Route::get('{id}/edit', 'trainerEdit')->name('edit');
                Route::put('/update/{id}', 'trainerUpdate')->name('update');
                Route::delete('{id}/delete', 'trainerDelete')->name('delete');
            });

            //approvel routes
            Route::prefix('approval')->name('approval.')->group(function () {
                Route::get('Detail', 'approval')->name('Detail');
                Route::post('{approval}/approve', 'approve')->name('approve');
                Route::post('{approval}/reject', 'reject')->name('reject');
            });

            //reports routes
            Route::prefix('report')->name('report.')->group(function () {
                Route::get('trainingSummary', 'trainingsummaryView')->name('trainingSummary'); // load the training summary view
                Route::get('epfSummary', 'epfsummaryView')->name('EPFSummary');
                Route::get('bondSummary', 'bondsummaryView')->name('BONDSummary');
                Route::get('budgetSummery', 'budgetSummeryView')->name('BudgetSummery');
            });
        });
    });
});


//hr admin routes
Route::middleware(['auth', 'verified', 'roleManager:hradmin, 1, 0'])->group(function () {
    Route::controller(HRAdmincontroller::class)->group(function () {
        Route::prefix('Admin')->name('Admin.')->group(function () {
            Route::prefix('HRAdmin')->name('HRAdmin.')->group(function () {
                //dashboard route
                Route::prefix('page')->name('page.')->group(function () {
                    Route::get('dashboard', 'viewDashboard')->name('dashboard');
                });
                //training Routes
                Route::prefix('training')->name('training.')->group(function () {
                    Route::get('Detail', 'trainingview')->name('Detail'); //load training details view
                    Route::get('create', 'createtrainingview')->name('create'); //load the training create page
                    Route::post('store', 'createtraining')->name('store'); // Store user (for create)
                    Route::get('{id}/edit', 'trainingedit')->name('edit');
                    Route::put('{id}/update', 'updatetraining')->name('update'); // Update training details
                    Route::post('cost-breakdown/store/{trainingId}', 'storeCostBreakdown')->name('cost-breakdown.store');
                    Route::delete('deleteTraining/{id}', 'trainingdestroy')->name('Training.delete'); // Delete user
                    Route::post('documents/store/{id}', 'storeTrainingDocument')->name('documents.store');
                    Route::get('costDetail/{id}', 'viewCost')->name('costDetail');
                    Route::get('costbreak/{id}', 'getCostBreakdownData')->name('costbreak');
                    Route::delete('cost-breakdown/delete/{id}', 'costBreakDelete')->name('cost-breakdown.delete');
                    Route::put('{id}/cost-breakdown/update', 'updateCostBreakdown')->name('cost-breakdown.update');
                    Route::put('update-status/{trainingId}', 'updateStatus')->name('update-status');
                });
                //participant routes
                Route::prefix('participant')->name('participant.')->group(function () {
                    Route::get('{id}/Detail', 'participantview')->name('Detail'); //load participant details view
                    Route::get('{id}/create', 'createparticipant')->name('create'); //load participant create view
                    Route::post('store', 'participantstore')->name('store');
                    Route::get('{id}/edit', 'participantedit')->name('edit');
                    Route::put('update/{id}', 'updateparticipant')->name('update');
                    Route::get('export-participant-columns', 'exportParticipantColumns')->name('export-participant-columns');
                    Route::post('import-participants', 'importParticipants')->name('import-participants');
                    Route::post('documents/store/{id}', 'storeParticipantDocument')->name('documents.store');
                    Route::delete('delete/{id}', 'destroyparticipant')->name('delete');
                });
                //budget routes
                Route::prefix('budget')->name('budget.')->group(function () {
                    Route::get('Detail', 'budgetview')->name('Detail'); //load budget details view
                    Route::get('Create', 'createBudgetView')->name('Create'); //load the create budget page
                    Route::post('store', 'budgetstore')->name('store'); // Store user (for create)
                });
                //institute routes
                Route::prefix('institute')->name('institute.')->group(function () {
                    Route::get('Detail', 'instituteview')->name('Detail');
                    Route::get('create', 'instituteCreate')->name('create');
                    Route::post('/store', 'Institutestore')->name('store');
                    Route::get('{id}/edit', 'instituteedit')->name('edit');
                    Route::put('/update/{id}', 'Instituteupdate')->name('update');
                    Route::delete('{id}/delete', 'instituteDelete')->name('delete');
                    Route::get('search', 'Institutesearch')->name('search');
                });

                //trainers routes
                Route::prefix('trainer')->name('trainer.')->group(function () {
                    Route::get('{id}/Detail', 'trainerview')->name('Detail'); //load the trainer details view
                    Route::get('{id}/Create', 'trainerCreate')->name('Create');
                    Route::get('search', 'trainerSearch')->name('search');
                    Route::post('store', 'trainerStore')->name('store');
                    Route::get('{id}/edit', 'trainerEdit')->name('edit');
                    Route::put('/update/{id}', 'trainerUpdate')->name('update');
                    Route::delete('{id}/delete', 'trainerDelete')->name('delete');
                });
                //reports routes
                Route::prefix('report')->name('report.')->group(function () {
                    Route::get('training', 'trainingsummaryView')->name('training'); // load the training summary view
                });
                //notifications routes
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::get('Detail', 'getNotifications')->name('Detail');
                    Route::put('update/{id}', 'statusupdate')->name('update');
                });
            });
        });
    });
});

//catc admin routes
Route::middleware(['auth', 'verified', 'roleManager:catcadmin, 2, 0'])->group(function () {
    Route::controller(CATCAdmincontroller::class)->group(function () {
        Route::prefix('Admin')->name('Admin.')->group(function () {
            Route::prefix('CATCAdmin')->name('CATCAdmin.')->group(function () {
                //CATC dachboard
                Route::prefix('page')->name('page.')->group(function () {
                    Route::get('dashboard', 'viewDashboard')->name('dashboard');
                });
                //CATC training Routes
                Route::prefix('training')->name('training.')->group(function () {
                    Route::get('Detail', 'trainingview')->name('Detail'); //load training details view
                    Route::get('create', 'createtrainingview')->name('create'); //load the training create page
                    Route::post('store', 'createtraining')->name('store'); // Store user (for create)
                    Route::get('{id}/edit', 'trainingedit')->name('edit');
                    Route::put('{id}/update', 'updatetraining')->name('update'); // Update training details
                    Route::post('cost-breakdown/store/{trainingId}', 'storeCostBreakdown')->name('cost-breakdown.store');
                    Route::delete('deleteTraining/{id}', 'trainingdestroy')->name('Training.delete'); // Delete user
                    Route::post('documents/store/{id}', 'storeTrainingDocument')->name('documents.store');
                    Route::get('costDetail/{id}', 'viewCost')->name('costDetail');
                    Route::get('costbreak/{id}', 'getCostBreakdownData')->name('costbreak');
                    Route::delete('cost-breakdown/delete/{id}', 'costBreakDelete')->name('cost-breakdown.delete');
                    Route::put('{id}/cost-breakdown/update', 'updateCostBreakdown')->name('cost-breakdown.update');
                    Route::put('update-status/{trainingId}', 'updateStatus')->name('update-status');
                });
                //CATC participant routes
                Route::prefix('participant')->name('participant.')->group(function () {
                    Route::get('{id}/Detail', 'participantview')->name('Detail'); //load participant details view
                    Route::get('{id}/create', 'createparticipant')->name('create'); //load participant create view
                    Route::post('store', 'participantstore')->name('store');
                    Route::get('{id}/edit', 'participantedit')->name('edit');
                    Route::put('update/{id}', 'updateparticipant')->name('update');
                    Route::get('export-participant-columns', 'exportParticipantColumns')->name('export-participant-columns');
                    Route::post('import-participants', 'importParticipants')->name('import-participants');
                    Route::post('documents/store/{id}', 'storeParticipantDocument')->name('documents.store');
                    Route::delete('delete/{id}', 'destroyCatcparticipant')->name('delete');
                });
                //CATC reports routes
                Route::prefix('report')->name('report.')->group(function () {
                    Route::get('training', 'trainingsummaryView')->name('training'); // load the training summary view
                });
                //notifications routes
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::get('Detail', 'getNotifications')->name('Detail');
                    Route::put('update/{id}', 'statusupdate')->name('update');
                });
            });
        });
    });
});
//user routes
Route::middleware(['auth', 'verified', 'roleManager:user'])->group(function () {
    Route::controller(Usercontroller::class)->group(function () {
        Route::prefix('User')->name('User.')->group(function () {
            Route::prefix('page')->name('page.')->group(function () {
                Route::get('dashboard', 'viewDashboard')->name('dashboard');
            });
            //training Routes
            Route::prefix('training')->name('training.')->group(function () {
                Route::get('Detail', 'trainingview')->name('Detail'); //load training details view
                Route::get('create', 'createtrainingview')->name('create'); //load the training create page
                Route::post('store', 'createtraining')->name('store'); // Store user (for create)
                Route::get('{id}/edit', 'trainingedit')->name('edit');
                Route::put('{id}/update', 'updatetraining')->name('update'); // Update training details
                Route::post('cost-breakdown/store/{trainingId}', 'storeCostBreakdown')->name('cost-breakdown.store');
                Route::delete('deleteTraining/{id}', 'trainingdestroy')->name('Training.delete'); // Delete user
                Route::post('documents/store/{id}', 'storeTrainingDocument')->name('documents.store');
                Route::get('costDetail/{id}', 'viewCost')->name('costDetail');
                Route::get('costbreak/{id}', 'getCostBreakdownData')->name('costbreak');
                Route::delete('cost-breakdown/delete/{id}', 'costBreakDelete')->name('cost-breakdown.delete');
                Route::put('{id}/cost-breakdown/update', 'updateCostBreakdown')->name('cost-breakdown.update');
                Route::put('update-status/{trainingId}', 'updateStatus')->name('update-status');
            });
            //participant routes
            Route::prefix('participant')->name('participant.')->group(function () {
                Route::get('{id}/Detail', 'participantview')->name('Detail'); //load participant details view
                Route::get('{id}/create', 'createparticipant')->name('create'); //load participant create view
                Route::post('store', 'participantstore')->name('store');
                Route::get('{id}/edit', 'participantedit')->name('edit');
                Route::put('update/{id}', 'updateparticipant')->name('update');
                Route::get('export-participant-columns', 'exportParticipantColumns')->name('export-participant-columns');
                Route::post('import-participants', 'importParticipants')->name('import-participants');
                Route::post('documents/store/{id}', 'storeParticipantDocument')->name('documents.store');
                Route::delete('delete/{id}', 'destroyparticipant')->name('delete');
            });
            //reports routes
            Route::prefix('report')->name('report.')->group(function () {
                Route::get('training', 'trainingsummaryView')->name('training'); // load the training summary view
            });
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('Detail', 'getNotifications')->name('Detail');
                Route::put('update/{id}', 'statusupdate')->name('update');
            });
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
