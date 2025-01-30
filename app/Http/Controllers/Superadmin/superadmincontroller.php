<?php

namespace App\Http\Controllers\Superadmin;

use Log;
use APP\Models\User;
use APP\Models\Section;
use APP\Models\Division;
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

    public function userview()
    {
        $users = \App\Models\User::with(['division', 'section'])->paginate(10); // Paginate 10 users per page

        return view('SuperAdmin.page.UserDetails', compact('users'));
    }

    public function createUserView()
    {
        return view('SuperAdmin.page.createUser');
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
            return redirect()->route('SuperAdmin.page.createUser')->with('error', 'User already exist!');
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
        return redirect()->route('SuperAdmin.page.UserDetails')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Find the user by ID
        return view('SuperAdmin.page.createUser', compact('user')); // Pass the user data to the edit view
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
        return redirect()->route('SuperAdmin.page.UserDetails')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        // Redirect back with success message
        return redirect()->route('SuperAdmin.page.UserDetails')->with('success', 'User deleted successfully!');
    }

    //training handling

    public function trainingview()
    {
        return view('SuperAdmin.training.Detail');
    }


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
