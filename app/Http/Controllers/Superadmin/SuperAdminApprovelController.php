<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Trainer;
use App\Models\Approval;
use App\Models\Training;
use App\Models\Costbreak;
use App\Models\Institute;
use App\Models\Participant;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SuperAdminApprovelController extends Controller
{
    
    // Approvel Handling
    public function approvelview()
    {
        return view('SuperAdmin.approvel.Detail');
    }
    public function approval()
    {
        $approvals = Approval::where('status', 'pending')->get();
        return view('SuperAdmin.approval.Detail', compact('approvals'));
    }

    public function approve(Approval $approval)
    {
        try {
            DB::beginTransaction();

            // Ensure the approval request is still pending
            if ($approval->status !== 'pending') {
                return redirect()->back()->with('warning', 'This request has already been processed or is not pending.');
            }

            // Find the model based on its type
            $model = $approval->model_type::findOrFail($approval->model_id);
            // Decode the new data
            $newData = json_decode($approval->new_data, true);
            // Process action (edit/update)
            if ($approval->action === 'edit' || $approval->action === 'update') {
                if ($approval->model_type === Training::class) {
                    // Update the training model
                    $model->update($newData);

                    // Sync related models (institutes & trainers)
                    $model->institutes()->sync($newData['institutes']);
                    $model->trainers()->sync($newData['trainers']);
                    if (!isset($newData['remark']) || empty($newData['remark'])) {
                        $newData['remarks'] = []; // Prevent passing null
                    } else {
                        $newData['remarks'] = [
                            ['remark' => $newData['remark']]
                        ];
                    }
                    $model->remarks()->createMany($newData['remarks']);

                    $message = "The training record (ID: {$model->id}) for '{$model->name}' has been successfully updated. ";
                    $message .= "Institutes and trainers have been updated, and remarks have been synchronized.";
                } elseif ($approval->model_type === Participant::class) {
                    // Update the participant record
                    $model->update($newData);

                    // Handle sureties: Check if sureties are set and properly structured
                    if (!isset($newData['sureties']) || empty($newData['sureties'])) {
                        $newData['sureties'] = []; // Prevent passing null if sureties are empty
                    } else {
                        // Ensure each surety is correctly formatted
                        $newData['sureties'] = array_map(function ($surety) {
                            return [
                                'name'              => $surety['name'],
                                'nic'               => $surety['nic'],
                                'mobile'            => $surety['mobile'],
                                'address'           => $surety['address'],
                                'salary_scale'      => $surety['salary_scale'],
                                'designation'       => $surety['suretydesignation'],
                                'epf_number'        => $surety['epf_number'],
                            ];
                        }, $newData['sureties']);
                    }

                    // Loop through the sureties to update or create them based on participant_id
                    foreach ($newData['sureties'] as $suretyData) {
                        // Find if the surety already exists for this participant (using participant_id)
                        $existingSurety = $model->sureties()->where('participant_id', $model->id)
                            ->where('nic', $suretyData['nic'])  // Or any unique identifier like NIC
                            ->first();

                        if ($existingSurety) {
                            // Update the existing surety
                            $existingSurety->update($suretyData);
                        } else {
                            // Create a new surety if not found
                            $model->sureties()->create(array_merge($suretyData, ['participant_id' => $model->id]));
                        }
                    }

                    // Handle remarks: Ensure remarks are set correctly
                    if (!isset($newData['remarks']) || empty($newData['remarks'])) {
                        $newData['remarks'] = []; // Prevent passing null if remarks are empty
                    } else {
                        // Format remarks as an array of arrays for createMany
                        $newData['remarks'] = array_map(function ($remark) {
                            return ['remark' => $remark]; // Ensure this is the correct column name
                        }, $newData['remarks']);
                    }

                    // Sync remarks
                    $model->remarks()->createMany($newData['remarks']);

                    // Success message
                    $message = "The participant record (ID: {$model->id}) for '{$model->name}' has been successfully updated. ";
                    $message .= "Sureties and remarks have been updated, and remarks have been synchronized.";
                } elseif ($approval->model_type === Institute::class) {
                    // Update the institute record
                    $model->update($newData);
                    $message = "The institute record (ID: {$model->id}) for '{$model->name}' has been successfully updated.";
                } elseif ($approval->model_type === Trainer::class) {
                    // Update the trainer record
                    $model->update($newData);
                    $message = "The trainer record (ID: {$model->id}) for '{$model->name}' has been successfully updated.";
                } else {
                    // Update other models (Costbreak, etc.)
                    $model->update($newData);
                    $message = class_basename($approval->model_type) . " record (ID: {$model->id}) for '{$model->name}' has been updated successfully.";
                }

                // Create a notification
                Notification::create([
                    'user_id'  => $approval->user_id,
                    'message'  => $message,
                    'status'   => 'pending'
                ]);

                DB::commit();
                return redirect()->back()->with('success', $message);
            }


            // Process action (delete)
            if ($approval->action === 'delete') {

                $modelName = class_basename($approval->model_type); // Get the model's name

                // Check if model type is Participant and delete related records
                if ($approval->model_type === Participant::class) {
                    // Delete related records
                    $model->remarks()->delete();
                    $model->sureties()->delete();
                    $model->documents()->delete();
                }

                if ($approval->model_type === Training::class) {
                    $model->remarks()->delete();
                    $model->costBrakedowns()->delete();
                    $model->subjects()->delete();
                    $model->participants()->delete();
                    $model->documents()->delete();
                }

                if ($approval->model_type === Costbreak::class) {
                    $model->delete();
                }
                if ($approval->model_type === Institute::class) {
                    $model->trainers()->delete();
                }

                if ($approval->model_type === Trainer::class) {
                    $model->delete();
                }

                // Delete the main model (Participant or Costbreak)
                $model->delete();

                // Create a detailed message about the deletion
                $message = "{$modelName} record (ID: {$model->id}) has been deleted successfully.";

                // Create a notification for the user
                Notification::create([
                    'user_id'  => $approval->user_id,
                    'message'  => $message,
                    'status'   => 'pending'
                ]);

                // Commit the transaction
                DB::commit();

                // Return success message
                return redirect()->back()->with('success', $message);
            }



            return redirect()->back()->with('error', 'Invalid action for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while approving the request: ' . $e->getMessage());
        } finally {
            // Mark approval as 'approved' and clear `new_data`
            if ($approval->status === 'pending') {
                $approval->update([
                    'status'   => 'approved',
                    'new_data' => null
                ]);
            }
        }
    }



    public function reject(Approval $approval)
    {
        try {
            DB::beginTransaction();

            // Ensure the approval request is still pending
            if ($approval->status !== 'pending') {
                return redirect()->back()->with('success', 'This request has already been processed or is not pending.');
            }

            // Check if model_type exists and is a valid class
            if (!class_exists($approval->model_type)) {
                return redirect()->back()->with('error', 'Invalid model type.');
            }

            // Find the model based on its type
            $model = $approval->model_type::find($approval->model_id);

            // If model not found, return error
            if (!$model) {
                return redirect()->back()->with('error', 'Model not found.');
            }

            // Mark approval as 'rejected'
            $approval->update(['status' => 'rejected']);
            // Detailed rejection message with ID and model name
            $message = "The {$approval->model_type} (ID: {$model->id}) approval request for {$model->name} has been rejected.";
            // Create a rejection notification
            Notification::create([
                'user_id' => $approval->user_id, // assuming the current user is rejecting the request
                'message' => $message,
                'status' => 'pending'
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Rejected approval');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
