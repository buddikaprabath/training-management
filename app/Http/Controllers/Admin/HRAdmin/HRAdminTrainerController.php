<?php

namespace App\Http\Controllers\Admin\HRAdmin;

use App\Models\Trainer;
use App\Models\Approval;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HRAdminTrainerController extends Controller
{
     
    // Trainer handling
    public function trainerview(Request $request, $id)
    {
        try {
            // Get the query parameter
            $query = $request->input('query');

            // Fetch the institute by ID
            $institute = Institute::findOrFail($id);

            // Apply the query to filter by trainer's name, email, or mobile
            $trainers = $institute->trainers()
                ->when($query, function ($q) use ($query) {
                    return $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('email', 'LIKE', '%' . $query . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $query . '%');
                })
                ->paginate(10);

            return view('Admin.HRAdmin.trainer.Detail', [
                'institute' => $institute,
                'trainers' => $trainers,
                'query' => $query,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Trainer not found.');
        }
    }



    //load the trainer create page
    public function trainerCreate($id)
    {
        try {
            // Fetch the institute data based on the $id
            $institute = Institute::findOrFail($id);

            // Pass the institute data to the view for creating a new trainer
            return view('Admin.HRAdmin.trainer.Create', compact('institute'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Trainer create page: ' . $e->getMessage());
        }
    }

    //trainer details store function
    public function trainerStore(Request $request)
    {
        try {
            $validatedata = $request->validate([
                'name'      => 'string|max:255|required',
                'email'     => 'string|email|required',
                'mobile'    => ['required', 'regex:/^(?:\+94|0)[7][0-9]{8}$/'],
                'institute_id' => 'integer'
            ]);

            Trainer::create([
                'name' => $validatedata['name'],
                'email' => $validatedata['email'],
                'mobile' => $validatedata['mobile'],
                'institute_id' => $validatedata['institute_id']
            ]);

            return redirect()->route('Admin.HRAdmin.institute.Detail')->with('success', 'Trainer Created Successfuly!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error storing Trainer Details: ', $e->getMessage());
        }
    }

    //load the trainer details edit page
    public function trainerEdit($id)
    {
        try {
            // Fetch the trainer along with its associated institute
            $trainer = Trainer::with(['institute'])->findOrFail($id);

            // Fetch the list of all institutes (optional, if you need to show a list of options in a dropdown)
            $institutes = Institute::all();

            // Pass both the trainer, institutes, and the institute of the trainer to the view
            return view('Admin.HRAdmin.trainer.Create', compact('trainer', 'institutes'));
        } catch (\Exception $e) {
            return back()->with('error', 'error loading trainer edit page: ' . $e->getMessage());
        }
    }
    //update the existing trainer details
    public function trainerUpdate(Request $request, $id)
    {
        try {
            $updatedData = $request->validate([
                'name'         => 'string|max:255|required',
                'email'        => 'string|email|required',
                'mobile'       => 'required',
                'institute_id' => 'integer|required'
            ]);

            $trainer = Trainer::findOrFail($id);
            // Check if an approval request already exists
            $existingApproval = Approval::where('model_type', Trainer::class)
                ->where('model_id', (string) $trainer->id)
                ->where('action', 'update')
                ->where('status', 'pending')
                ->first();

            if ($existingApproval) {
                return redirect()->route('Admin.HRAdmin.trainer.Detail', ['id' => $trainer->institute_id])
                    ->with('error', 'An update request for this trainer is already pending approval.');
            }

            // Create an approval request for the update
            Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Trainer::class,
                'model_id'   => (string) $trainer->id,
                'action'     => 'update',
                'new_data'   => json_encode($updatedData), // Store the updated data, including sureties and remarks
                'status'     => 'pending',
            ]);

            DB::commit();
            return redirect()->route('Admin.HRAdmin.trainer.Detail', ['id' => $trainer->institute_id])
                ->with('success', 'Your update request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error occurred while sending update request: ' . $e->getMessage());
        }
    }
    //delete the trainer details
    public function trainerDelete($id)
    {
        try {
            // Find the trainer by ID
            $trainer = Trainer::findOrFail($id);

            // Check if an approval request already exists for this deletion
            $existingApproval = Approval::where('model_type', Trainer::class)
                ->where('model_id', (string) $trainer->id)
                ->where('action', 'delete')
                ->where('status', 'pending')
                ->first();

            // Prevent duplicate approval requests
            if ($existingApproval) {
                return redirect()->back()->with('info', 'A deletion request is already pending for this trainer.');
            }

            // Create an approval request for deletion
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Trainer::class,
                'model_id'   => (string) $trainer->id,
                'action'     => 'delete',
                'new_data'   => null,  // No new data as we are deleting the record
                'status'     => 'pending',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Your deletion request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while sending deletion request: ' . $e->getMessage());
        }
    }
}
