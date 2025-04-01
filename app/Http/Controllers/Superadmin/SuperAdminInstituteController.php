<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Institute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuperAdminInstituteController extends Controller
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

            return view('SuperAdmin.institute.Detail', compact('institutes', 'query'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading institute datail page : ' . $e->getMessage());
        }
    }



    public function instituteCreate()
    {
        try {
            return view('SuperAdmin.institute.create');
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
            return view('SuperAdmin.institute.create', compact('institute'));
        } catch (\Exception $e) {
            // If an error occurs, redirect back with an error message
            return back()->with('error', 'Error loading institute details: ' . $e->getMessage());
        }
    }

    public function Instituteupdate(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
            ]);

            $institute = Institute::findOrFail($id);
            $institute->name = $request->name;
            $institute->type = $request->type;
            $institute->save();

            return redirect()->back()->with('success', 'Institute updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating institute:' . $e->getMessage());
        }
    }

    public function instituteDelete($id)
    {
        try {
            $institute = Institute::findOrFail($id);
            $institute->delete();

            return redirect()->back()->with('success', 'Institute deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting institute: ' . $e->getMessage());
        }
    }
    //end institute handling
}
