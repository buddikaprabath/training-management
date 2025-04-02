<?php

namespace App\Http\Controllers\Admin\HRAdmin;

use App\Models\Approval;
use App\Models\Institute;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HRAdminInstituteController extends Controller
{
    //Institute handling
    public function instituteview(Request $request)
    {
        try {
            $query = $request->input('query');

            // Filter based on search query or show all records
            $institutes = Institute::when($query, function ($q) use ($query) {
                return $q->where('name', 'like', '%' . $query . '%');
            })->paginate(10);

            return view('Admin.HRAdmin.institute.Detail', compact('institutes', 'query'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading institute datail page : ' . $e->getMessage());
        }
    }



    public function instituteCreate()
    {
        try {
            return view('Admin.HRAdmin.institute.create');
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading institute create page: ' . $e->getMessage());
        }
    }


    public function Institutestore(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
            ]);

            $institute = new Institute();
            $institute->name = $request->name;
            $institute->type = $request->type;
            $institute->save();

            return redirect()->back()->with('success', 'Institute created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error storing institute: ' . $e->getMessage());
        }
    }
    public function instituteedit($id)
    {
        try {
            // Retrieve the institute by ID
            $institute = Institute::findOrFail($id);

            // Return the view with the correct variable
            return view('Admin.HRAdmin.institute.create', compact('institute'));
        } catch (\Exception $e) {
            // If an error occurs, redirect back with an error message
            return back()->with('error', 'Error loading institute details: ' . $e->getMessage());
        }
    }

    public function Instituteupdate(Request $request, $id)
    {
        try {
            $updatedData = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
            ]);

            $institute = Institute::findOrFail($id);
            // Check if an approval request already exists
            $existingApproval = Approval::where('model_type', Institute::class)
                ->where('model_id', (string) $institute->id)
                ->where('action', 'update')
                ->where('status', 'pending')
                ->first();

            if ($existingApproval) {
                return redirect()->route('Admin.HRAdmin.institute.Detail')
                    ->with('error', 'An update request for this institute is already pending approval.');
            }

            // Create an approval request for the update
            Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Institute::class,
                'model_id'   => (string) $institute->id,
                'action'     => 'update',
                'new_data'   => json_encode($updatedData), // Store the updated data, including sureties and remarks
                'status'     => 'pending',
            ]);

            $message = "Approval Request Submitted : A new approval request has been submitted for editing a Institute record.Please review and take the necessary action.";

            $user_role = 'superadmin';

            // Create a notification
            Notification::create([
                'message'  => $message,
                'status'   => 'pending',
                'user_role' => $user_role,
                'model_id'   => (string) $institute->id,
            ]);
            DB::commit();
            return redirect()->route('Admin.HRAdmin.institute.Detail')
                ->with('success', 'Your update request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error occurred while sending update request: ' . $e->getMessage());
        }
    }

    public function instituteDelete($id)
    {
        try {
            $institute = Institute::findOrFail($id);
            // Check if an approval request already exists for this deletion
            $existingApproval = Approval::where('model_type', Institute::class)
                ->where('model_id', (string) $institute->id)
                ->where('action', 'delete')
                ->where('status', 'pending')
                ->first();
            // Prevent duplicate approval requests
            if ($existingApproval) {
                return redirect()->back()->with('info', 'A deletion request is already pending for this Institute.');
            }

            // Create an approval request for deletion
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Institute::class,
                'model_id'   => (string) $institute->id,
                'action'     => 'delete',
                'new_data'   => null,  // No new data as we are deleting the record
                'status'     => 'pending',
            ]);

            $message = "Approval Request Submitted : A new approval request has been submitted for deleting a Institute record.Please review and take the necessary action.";

            $user_role = 'superadmin';

            // Create a notification
            Notification::create([
                'message'  => $message,
                'status'   => 'pending',
                'user_role' => $user_role,
                'model_id'   => (string) $institute->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Your deletion request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while sending deletion request: ' . $e->getMessage());
        }
    }


    //end institute handling
}
