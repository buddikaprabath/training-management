<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Trainer;
use App\Models\Institute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuperAdminTrainerController extends Controller
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

            return view('SuperAdmin.trainer.Detail', [
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
            return view('SuperAdmin.trainer.Create', compact('institute'));
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

            return redirect()->route('SuperAdmin.institute.Detail')->with('success', 'Trainer Created Successfuly!');
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
            return view('SuperAdmin.trainer.Create', compact('trainer', 'institutes'));
        } catch (\Exception $e) {
            return back()->with('error', 'error loading trainer edit page: ' . $e->getMessage());
        }
    }
    //update the existing trainer details
    public function trainerUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'name'         => 'string|max:255|required',
                'email'        => 'string|email|required',
                'mobile'       => 'required',
                'institute_id' => 'integer|required'
            ]);

            $trainer = Trainer::findOrFail($id);
            $trainer->name = $request->name;
            $trainer->email = $request->email;
            $trainer->mobile = $request->mobile;
            $trainer->institute_id = $request->institute_id;
            $trainer->save();

            return redirect()->route('SuperAdmin.institute.Detail')->with('success', 'Trainer details updated successfully!');
        } catch (\Exception $e) {
            // Returning back with an error message
            return back()->with('error', 'Error updating trainer details: ' . $e->getMessage());
        }
    }
    //delete the trainer details
    public function trainerDelete($id)
    {
        try {
            // Find the trainer by ID
            $trainer = Trainer::findOrFail($id);

            // Delete the trainer record
            $trainer->delete();

            // Redirect back to the same page with a success message
            return redirect()->back()->with('success', 'Trainer details successfully deleted!');
        } catch (\Exception $e) {
            // Redirect back with an error message
            return redirect()->back()->with('error', 'Error deleting trainer details: ' . $e->getMessage());
        }
    }
}
