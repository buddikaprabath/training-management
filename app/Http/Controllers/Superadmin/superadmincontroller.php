<?php

namespace App\Http\Controllers\Superadmin;

use Log;
use APP\Models\User;
use App\Models\Country;
use APP\Models\Section;
use APP\Models\Division;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class superadmincontroller extends Controller
{
    public function index()
    {
        return view('SuperAdmin.page.dashboard');
    }

    public function userview(Request $request)
    {
        $query = $request->input('query');

        // If search query exists, filter users
        if ($query) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('username', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->paginate(10);
        } else {
            $users = User::paginate(10); // Load all users if no search
        }

        return view('SuperAdmin.Users.Details', compact('users', 'query'));
    }

    public function createUserView()
    {
        return view('SuperAdmin.Users.Create');
    }

    public function create(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|max:255',
            'division_id' => 'required|integer',
            'section_id' => 'nullable|integer',  // section_id is optional, but must be an integer if provided
            'password' => 'required|string|min:8|confirmed', // Ensure password is confirmed
        ]);

        // Check if user already exists
        $existingUser = User::where('username', $validatedData['username'])
            ->orWhere('email', $validatedData['email'])
            ->first();

        if ($existingUser) {
            return redirect()->route('SuperAdmin.Users.Create')->with('error', 'User already exist!');
        }

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'division_id' => $validatedData['division_id'],
            'section_id' => $validatedData['section_id'] ?? 0, // Default to 0 if no section is provided
            'password' => Hash::make($validatedData['password']),
        ]);

        // Redirect to the specified route with success message
        return redirect()->route('SuperAdmin.Users.Details')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Find the user by ID
        return view('SuperAdmin.Users.Create', compact('user')); // Pass the user data to the edit view
    }

    public function update(Request $request, $id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,  // Exclude current user's username from uniqueness check
            'email' => 'required|email|max:255|unique:users,email,' . $id,  // Exclude current user's email from uniqueness check
            'role' => 'required|string|max:255',
            'division_id' => 'required|integer',
            'section_id' => 'nullable|integer',  // section_id is optional, but must be an integer if provided
            'password' => 'nullable|string|min:8|confirmed', // Ensure password is confirmed, but is optional
        ]);


        // Update the user
        $user->update([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'division_id' => $validatedData['division_id'],
            'section_id' => $validatedData['section_id'] ?? 0, // Default to 0 if no section is provided
            // Only update password if it's provided
            'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $user->password,
        ]);

        // Redirect to the specified route with success message
        return redirect()->route('SuperAdmin.Users.Details')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        // Redirect back with success message
        return redirect()->route('SuperAdmin.Users.Details')->with('success', 'User deleted successfully!');
    }
    //end user handling functions

    //training handling

    //training details view page
    public function trainingview(Request $request)
    {
        $query = $request->input('query');

        $training = Training::join('divisions', 'trainings.division_id', '=', 'divisions.id')
            ->select('trainings.*', 'divisions.division_name') // Selecting required fields
            ->when($query, function ($q) use ($query) {
                $q->where('trainings.training_name', 'LIKE', "%{$query}%")
                    ->orWhere('trainings.training_code', 'LIKE', "%{$query}%")
                    ->orWhere('divisions.division_name', 'LIKE', "%{$query}%"); // Search by division name
            })
            ->paginate(10);

        return view('SuperAdmin.training.Detail', compact('training', 'query'));
    }

    //training create page
    public function createtrainingview()
    {
        $countries = DB::table('countries')->get();

        return view('SuperAdmin.training.create', compact('countries'));
    }
    //end training handling functions
    //participant handling

    public function participantview()
    {
        return view('SuperAdmin.participant.Detail');
    }

    //budget handling
    public function budgetview()
    {
        return view('SuperAdmin.budget.Detail');
    }


    //Institute handling
    public function instituteview()
    {
        return view('SuperAdmin.institute.Detail');
    }

    //Trainer handling
    public function trainerview()
    {
        return view('SuperAdmin.trainer.Detail');
    }

    // Approvel Handling
    public function approvelview()
    {
        return view('SuperAdmin.approvel.Detail');
    }

    //reports handling
    public function trainingsummaryView()
    {
        return view('SuperAdmin.report.trainingSummary');
    }
}
