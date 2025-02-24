<?php

namespace App\Http\Controllers\Superadmin;

use Log;
use APP\Models\User;
use App\Models\Budget;
use App\Models\Remark;
use App\Models\Surety;
use App\Models\Country;
use APP\Models\Section;
use App\Models\Subject;
use App\Models\Trainer;
use App\Models\Approval;
use APP\Models\Division;
use App\Models\Document;
use App\Models\Training;
use App\Models\Costbreak;
use App\Models\Institute;
use App\Models\Participant;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

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

    public function trainingview(Request $request, $itemId = null)
    {
        $query = $request->input('query');

        // Get the training data with filtering based on the query
        $training = Training::join('divisions', 'trainings.division_id', '=', 'divisions.id')
            ->select('trainings.*', 'divisions.division_name')
            ->when($query, function ($q) use ($query) {
                $q->where('trainings.training_name', 'LIKE', "%{$query}%")
                    ->orWhere('trainings.training_code', 'LIKE', "%{$query}%")
                    ->orWhere('divisions.division_name', 'LIKE', "%{$query}%")
                    ->orWhere('trainings.category', 'LIKE', "%{$query}%")
                    ->orWhere('trainings.mode_of_delivery', 'LIKE', "%{$query}%");
            })
            ->paginate(10);

        // Pass whether training data is empty to the view
        $trainingEmpty = $training->isEmpty();

        // Check if itemId is provided and fetch Costbreak data
        $costBreak = null;
        if ($itemId) {
            $costBreak = Costbreak::where('item_id', $itemId)->first();
        }

        return view('SuperAdmin.training.Detail', compact('training', 'query', 'costBreak', 'trainingEmpty'));
    }



    //load the training create page
    public function createtrainingview()
    {
        $countries = DB::table('countries')->get();
        $institutes = Institute::all();
        $trainers = Trainer::all(); // Fetch all trainers

        return view('SuperAdmin.training.create', compact('countries', 'institutes', 'trainers'));
    }


    //store method for training data storing
    public function createtraining(Request $request)
    {
        $validated = $request->validate([
            'training_name'         => 'required|string|max:255',
            'training_code'         => 'required|string|max:10|unique:trainings,training_code',
            'mode_of_delivery'      => 'required|string|max:255',
            'training_period_from'  => 'required|date',
            'training_period_to'    => 'required|date|after_or_equal:training_period_from',
            'total_training_hours'  => 'required|integer|max:255',
            'total_program_cost'    => 'required|numeric|between:0,9999999.99',
            'course_type'           => 'required|string|max:255',
            'category'              => 'required|string|max:255',
            'training_custodian'    => 'nullable|string|max:255',
            'batch_size'            => 'nullable|integer|min:1', // Added min validation for positive batch size
            'division_id'           => 'required|exists:divisions,id',
            'section_id'            => 'nullable|exists:sections,id',
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to', // Updated to ensure the expiration date is after the training end date
            'institutes'            => 'required|array',
            'institutes.*'          => 'exists:institutes,id',
            'trainers'              => 'required|array',
            'trainers.*'            => 'exists:trainers,id',
            'remark'                => 'nullable|string|max:255', // Added max length for remark
            'subject_type'          => 'nullable|string|max:255',
            'subject_name'          => 'nullable|string|max:255',
            'dead_line'             => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Create a new training record
            $training = Training::create([
                'training_name'         => $validated['training_name'],
                'training_code'         => $validated['training_code'],
                'mode_of_delivery'      => $validated['mode_of_delivery'],
                'training_period_from'  => $validated['training_period_from'],
                'training_period_to'    => $validated['training_period_to'],
                'total_training_hours'  => $validated['total_training_hours'],
                'total_program_cost'    => $validated['total_program_cost'],
                'country'               => $validated['country'] ?? null,
                'training_structure'    => $validated['training_structure'] ?? null,
                'exp_date'              => $validated['exp_date'] ?? null,
                'batch_size'            => $validated['batch_size'] ?? null,
                'training_custodian'    => $validated['training_custodian'] ?? null,
                'course_type'           => $validated['course_type'],
                'category'              => $validated['category'],
                'dead_line'             => $validated['dead_line'],
                'division_id'           => $validated['division_id'],
                'section_id'            => $validated['section_id'] ?? 0,
            ]);

            // Attach related institutes
            $training->institutes()->sync($validated['institutes']);

            // Attach related trainers
            $training->trainers()->sync($validated['trainers']);

            // Create a subject if provided
            if (!empty($validated['subject_name'])) {
                Subject::create([
                    'subject_name' => $validated['subject_name'],
                    'subject_type' => $validated['subject_type'],
                    'training_id'  => $training->id,
                ]);
            }

            // Store remark if provided
            if (!empty($validated['remark'])) {
                $remark = Remark::create([
                    'remark'        => $validated['remark'],
                    'training_id'   => $training->id,
                    'participant_id' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('SuperAdmin.training.Detail')->with('success', 'Training created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while saving training: ' . $e->getMessage());
        }
    }


    //load training edit page
    public function trainingedit($id)
    {
        try {
            // Retrieve the training along with related data
            $training = Training::with(['institutes', 'trainers', 'subjects'])->findOrFail($id);

            // Fetch the list of institutes, trainers, subjects, and countries (add the countries data here)
            $institutes = Institute::all();
            $trainers = Trainer::all();
            $subjects = Subject::all();
            $countries = Country::all();  // Fetching the countries

            // Return the view with all necessary data
            return view('SuperAdmin.training.create', compact('training', 'institutes', 'trainers', 'subjects', 'countries'));
        } catch (\Exception $e) {
            // If an error occurs, redirect back with an error message
            return back()->with('error', 'Error loading training details: ' . $e->getMessage());
        }
    }

    //handle the update training function
    public function updatetraining(Request $request, $id)
    {
        $validated = $request->validate([
            'training_name'         => 'required|string|max:255',
            'training_code'         => 'required|string|max:10|unique:trainings,training_code',
            'mode_of_delivery'      => 'required|string|max:255',
            'training_period_from'  => 'required|date',
            'training_period_to'    => 'required|date|after_or_equal:training_period_from',
            'total_training_hours'  => 'required|integer|max:255',
            'total_program_cost'    => 'required|numeric|between:0,9999999.99',
            'course_type'           => 'required|string|max:255',
            'category'              => 'required|string|max:255',
            'training_custodian'    => 'nullable|string|max:255',
            'batch_size'            => 'nullable|integer|min:1', // Added min validation for positive batch size
            'division_id'           => 'required|exists:divisions,id',
            'section_id'            => 'nullable|exists:sections,id',
            'other_comments'        => 'nullable|string|max:255', // Added max length for comments
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to', // Updated to ensure the expiration date is after the training end date
            'institutes'            => 'required|array',
            'institutes.*'          => 'exists:institutes,id',
            'trainers'              => 'required|array',
            'trainers.*'            => 'exists:trainers,id',
            'remark'                => 'nullable|string|max:255', // Added max length for remark
            'subject_type'          => 'nullable|string|max:255',
            'subject_name'          => 'nullable|string|max:255',
            'dead_line'             => 'required|date',
        ]);
        try {
            DB::beginTransaction();

            // Find the training by ID
            $training = Training::findOrFail($id);

            // Update the training record
            $training->update([
                'training_name'         => $validated['training_name'],
                'training_code'         => $validated['training_code'],
                'mode_of_delivery'      => $validated['mode_of_delivery'],
                'training_period_from'  => $validated['training_period_from'],
                'training_period_to'    => $validated['training_period_to'],
                'total_training_hours'  => $validated['total_training_hours'],
                'total_program_cost'    => $validated['total_program_cost'],
                'country'               => $validated['country'] ?? null,
                'training_structure'    => $validated['training_structure'] ?? null,
                'exp_date'              => $validated['exp_date'] ?? null,
                'batch_size'            => $validated['batch_size'] ?? null,
                'training_custodian'    => $validated['training_custodian'] ?? null,
                'course_type'           => $validated['course_type'],
                'category'              => $validated['category'],
                'dead_line'             => $validated['dead_line'],
                'division_id'           => $validated['division_id'],
                'section_id'            => $validated['section_id'] ?? null,  // Make sure this is nullable if not provided
            ]);


            // Sync related institutes
            $training->institutes()->sync($validated['institutes']);

            // Sync related trainers
            $training->trainers()->sync($validated['trainers']);

            // Update or create a subject if provided
            if (!empty($validated['subject_name'])) {
                $training->subject()->updateOrCreate(
                    ['training_id' => $training->id],
                    [
                        'subject_name' => $validated['subject_name'],
                        'subject_type' => $validated['subject_type'],
                    ]
                );
            }

            // Update or create remark if provided
            if (!empty($validated['remark'])) {
                Remark::updateOrCreate(
                    ['training_id' => $training->id],
                    [
                        'remark'        => $validated['remark'],
                        'participant_id' => null, // Change this if participant ID is available
                    ]
                );
            }

            DB::commit();
            return redirect()->route('SuperAdmin.training.Detail')->with('success', 'Training updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while updating training: ' . $e->getMessage());
        }
    }

    //update the training status

    public function updateStatus(Request $request, $trainingId)
    {
        try {
            $training = Training::findOrFail($trainingId);

            // Update only the necessary fields
            $training->update($request->only([
                'feedback_form',
                'e_report',
                'warm_clothe_allowance',
                'presentation',
                'training_status'
            ]));

            // After saving, redirect back to the same page with session data to reflect the updated status
            return redirect()->route('SuperAdmin.training.Detail', ['id' => $trainingId])
                ->with([
                    'statusUpdated' => true, // Indicating the status was updated
                    'feedback_form' => $training->feedback_form,
                    'e_report' => $training->e_report,
                    'warm_clothe_allowance' => $training->warm_clothe_allowance,
                    'presentation' => $training->presentation,
                    'training_status' => $training->training_status
                ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving training status: ' . $e->getMessage());
        }
    }





    //add cost break down amounts

    public function storeCostBreakdown(Request $request, $trainingId)
    {
        $validated = $request->validate([
            'airfare'       => 'required|numeric|min:0',
            'subsistence'   => 'required|numeric|min:0',
            'incidental'    => 'required|numeric|min:0',
            'registration'  => 'required|numeric|min:0',
            'visa'         => 'required|numeric|min:0',
            'insurance'     => 'required|numeric|min:0',
            'warm_clothes'  => 'required|numeric|min:0',
            'total_amount'  => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Ensure training exists
            $training = Training::where('id', $trainingId)->firstOrFail();

            // Check if a cost breakdown already exists for this training_id
            if (Costbreak::where('training_id', $training->id)->exists()) {
                return redirect()->back()->with('error', 'A cost breakdown already exists for this training.');
            }

            // Create a new cost breakdown entry
            Costbreak::create([
                'training_id'   => $training->id,
                'airfare'       => $validated['airfare'],
                'subsistence'   => $validated['subsistence'],
                'incidental'    => $validated['incidental'],
                'registration'  => $validated['registration'],
                'visa'          => $validated['visa'],
                'insurance'     => $validated['insurance'],
                'warm_clothes'  => $validated['warm_clothes'],
                'total_amount'  => $validated['total_amount']
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cost breakdown added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while adding cost breakdown: ' . $e->getMessage());
        }
    }

    //load the costbreak down detail page with data
    public function viewCost($id)
    {
        try {

            // Fetch cost breakdown using training_id
            $costs = DB::table('costbreaks')->where('training_id', $id)->first();

            return view('SuperAdmin.training.costDetail', compact('costs'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading cost breakdown details page: ' . $e->getMessage());
        }
    }

    // Controller Method for Update
    public function updateCostBreakdown(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'airfare' => 'required|numeric|min:0',
                'subsistence' => 'required|numeric|min:0',
                'incidental' => 'required|numeric|min:0',
                'registration' => 'required|numeric|min:0',
                'visa' => 'required|numeric|min:0',
                'insurance' => 'required|numeric|min:0',
                'warm_clothes' => 'required|numeric|min:0',
                'training_id' => 'required|string'
            ]);


            // Find the existing Costbreak record for the given itemId
            $costBreak = Costbreak::where('id', $id)->first();

            if (!$costBreak) {
                // If the cost breakdown doesn't exist, handle the error (optional)
                return redirect()->back()->with('error', 'Cost Breakdown not found!');
            }

            $totalAmount = $validatedData['airfare'] + $validatedData['subsistence'] + $validatedData['incidental'] +
                $validatedData['registration'] + $validatedData['visa'] + $validatedData['insurance'] +
                $validatedData['warm_clothes'];


            // Update the existing Costbreak record with validated data
            $costBreak->update([
                'airfare' => $validatedData['airfare'],
                'subsistence' => $validatedData['subsistence'],
                'incidental' => $validatedData['incidental'],
                'registration' => $validatedData['registration'],
                'visa' => $validatedData['visa'],
                'insurance' => $validatedData['insurance'],
                'warm_clothes' => $validatedData['warm_clothes'],
                'total_amount' => $totalAmount,
                'training_id' => $validatedData['training_id']
            ]);

            // Redirect back with a success message
            return redirect()->route('SuperAdmin.training.costDetail', ['id' => $costBreak->training_id])
                ->with('success', 'Cost Breakdown updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error updating costbreakdown : ' . $e->getMessage());
        }
    }
    //load the costbreak down edit page with data
    public function getCostBreakdownData($id)
    {
        try {
            // Attempt to fetch the cost breakdown data
            $costs = CostBreak::find($id);

            // Check if the data exists before accessing properties
            if (!$costs) {
                return redirect()->back()->with('error', 'No cost breakdown data found for the given ID.');
            }

            return view('SuperAdmin.training.costbreak', compact('costs'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching the cost breakdown data. Please try again later: ' . $e->getMessage());
        }
    }

    //delete function for delete costbreakdown
    public function costBreakDelete($id)
    {
        try {
            DB::beginTransaction();
            $costs = Costbreak::findOrFail($id);
            $costs->delete();
            DB::commit();
            return redirect()->route('SuperAdmin.training.Detail')->with('success', 'Cost break down deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting costbreak down : ' . $e->getMessage());
        }
    }
    //store document for each training
    public function storeTrainingDocument(Request $request, $trainingId)
    {
        try {
            $validated = $request->validate([
                'name'               => 'required|string|max:255',
                'status'             => 'nullable|string|max:50',
                'date_of_submitting' => 'nullable|date',
                'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Max 2MB
            ]);
            DB::beginTransaction();

            // Ensure the training exists
            $training = Training::findOrFail($trainingId);

            // Store file in storage/app/public/documents
            $filePath = $request->file('document_file')->store('documents', 'public');

            // Create the document record
            Document::create([
                'name'               => $validated['name'],
                'status'             => $validated['status'] ?? null,
                'date_of_submitting' => $validated['date_of_submitting'] ?? null,
                'training_id'        => $training->id,
                'file_path'          => $filePath,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }


    //training delete function
    public function trainingdestroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the training by ID
            $training = Training::findOrFail($id);

            // Detach related institutes and trainers from pivot tables
            $training->institutes()->detach();
            $training->trainers()->detach();

            // Delete related subjects
            $training->subjects()->delete();

            // Delete related remarks
            Remark::where('training_id', $training->id)->delete();

            // Delete related cost breakdown entries (if applicable)
            Costbreak::where('training_id', $training->id)->delete();

            // Finally, delete the training record
            $training->delete();

            DB::commit();
            return redirect()->route('SuperAdmin.training.Detail')->with('success', 'Training deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while deleting training: ' . $e->getMessage());
        }
    }

    //end training handling functions
    //participant handling

    public function participantview($trainingId)
    {
        try {
            // Attempt to retrieve training and related data, including documents and remarks
            $training = Training::with(['remarks', 'institutes', 'documents'])  // Load remarks and documents as well
                ->find($trainingId);

            if (!$training) {
                return redirect()->back()->with('error', 'Training not found.');
            }

            // Retrieve the participants with pagination
            $participants = $training->participants()->paginate(10); // Paginate participants

            // Prepare remarks data in a format suitable for the JavaScript on the front-end
            $remarksData = $training->remarks->groupBy('training_id')->toArray(); // Group remarks by training_id

            // Return the view with the data
            return view('SuperAdmin.participant.Detail', [
                'training' => $training,
                'participants' => $participants, // Paginated participants
                'institutes' => $training->institutes,
                'documents' => $training->documents, // Pass the document details
                'remarksData' => $remarksData, // Pass the remarks to the view
            ]);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    //load the create participant blade
    public function createparticipant($trainingId)
    {
        try {
            // Attempt to find the training
            $training = Training::findOrFail($trainingId); // This will automatically throw a ModelNotFoundException if not found

            // Return the view with the training data
            return view('SuperAdmin.participant.create', compact('training'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Catch the ModelNotFoundException and return an error if the training is not found
            return redirect()->back()->with('error', 'Training not found.');
        } catch (\Exception $e) {
            // Catch any other exceptions and return a generic error message
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    //store participant
    public function participantstore(Request $request)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'epf_number'                   => 'required|string|max:50|unique:participants,epf_number',
            'designation'                  => 'required|string|max:255',
            'salary_scale'                 => 'nullable|string|max:255',
            'location'                     => 'nullable|string|max:255',
            'obligatory_period'            => 'nullable|string|max:255',
            'cost_per_head'                => 'nullable|numeric', // You can use 'decimal:10,2' if you expect decimals
            'bond_completion_date'         => 'nullable|date',
            'bond_value'                   => 'nullable|numeric', // You can use 'decimal:12,2' for decimals
            'date_of_signing'              => 'nullable|date',
            'age_as_at_commencement_date'  => 'nullable|numeric', // Assuming it's a decimal with 2 places after decimal
            'date_of_appointment'          => 'nullable|date',
            'date_of_appointment_to_the_present_post' => 'nullable|date',
            'date_of_birth'                => 'nullable|date',
            'division_id'                  => 'nullable|exists:divisions,id',
            'section_id'                   => 'nullable|exists:sections,id',
            'training_id'                  => 'required|exists:trainings,id',

            // Surety Validation (2 sureties)
            'sureties'                      => 'nullable|array|max:2',
            'sureties.*.suretyname'          => 'nullable|string|max:255',
            'sureties.*.nic'                 => 'nullable|string|max:12',
            'sureties.*.mobile'              => 'nullable|string|max:15',
            'sureties.*.address'             => 'nullable|string|max:255',
            'sureties.*.salary_scale'        => 'nullable|numeric|max:999999999', // Validate as numeric and restrict max value
            'sureties.*.suretydesignation'   => 'nullable|string|max:255',
            'sureties.*.epf_number'          => 'nullable|string|max:50',

            // Remarks Validation (Multiple remarks)
            'remarks'                     => 'nullable|array',
            'remarks.*'                   => 'nullable|string|max:500',
        ]);


        DB::beginTransaction();
        try {
            // Store Participant
            $participant = Participant::create([
                'name'                          => $request->name,
                'epf_number'                    => $request->epf_number,
                'designation'                   => $request->designation,
                'salary_scale'                  => $request->salary_scale,
                'location'                      => $request->location,
                'obligatory_period'             => $request->obligatory_period,
                'cost_per_head'                 => $request->cost_per_head,
                'bond_completion_date'          => $request->bond_completion_date,
                'bond_value'                    => $request->bond_value,
                'date_of_signing'               => $request->date_of_signing,
                'age_as_at_commencement_date'   => $request->age_as_at_commencement_date,
                'date_of_appointment'           => $request->date_of_appointment,
                'date_of_appointment_to_the_present_post' => $request->date_of_appointment_to_the_present_post,
                'date_of_birth'                 => $request->date_of_birth,
                'division_id'                   => $request->division_id,
                'section_id'                    => $request->section_id,
                'training_id'                   => $request->training_id, // Passed from the clicked training
            ]);

            // Store Sureties (up to 2)
            if ($request->sureties) {
                foreach ($request->sureties as $suretyData) {
                    Surety::create([
                        'name'           => $suretyData['suretyname'],
                        'epf_number'     => $suretyData['epf_number'],
                        'nic'            => $suretyData['nic'],
                        'mobile'         => $suretyData['mobile'],
                        'address'        => $suretyData['address'],
                        'salary_scale'   => $suretyData['salary_scale'] ?? null,
                        'designation'    => $suretyData['suretydesignation'] ?? null,
                        'participant_id' => $participant->id,
                    ]);
                }
            }

            // Store Multiple Remarks
            if ($request->has('remarks')) {
                $remarks = is_array($request->remarks) ? $request->remarks : [$request->remarks];

                foreach ($remarks as $remark) {
                    Remark::create([
                        'remark'        => $remark,
                        'training_id'   => $request->training_id,
                        'participant_id' => $participant->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('SuperAdmin.training.Detail')->with('success', 'Participant added successfully! You can now upload documents.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add participant: ' . $e->getMessage());
        }
    }
    public function viewcreatedocument($participantId)
    {
        // Find the participant to ensure they exist
        $participant = Participant::with('training')->findOrFail($participantId);

        return view('documents.create', compact('participant'));
    }
    // Store participant document
    public function storeParticipantDocument(Request $request, $participantId)
    {
        try {
            $validated = $request->validate([
                'name'               => 'required|string|max:255',
                'status'             => 'nullable|string|max:50',
                'date_of_submitting' => 'nullable|date',
                'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Max 2MB
            ]);

            DB::beginTransaction();

            // Ensure the participant exists
            $participant = Participant::findOrFail($participantId);

            // Ensure the training ID is passed correctly
            $trainingId = $request->input('training_id');  // Capture the training_id

            if (!$trainingId) {
                return redirect()->back()->with('error', 'Training ID is required.');
            }

            // Store file in storage/app/public/documents
            $filePath = $request->file('document_file')->store('documents', 'public');

            // Create the document record, including training_id
            Document::create([
                'name'               => $validated['name'],
                'status'             => $validated['status'] ?? null,
                'date_of_submitting' => $validated['date_of_submitting'] ?? null,
                'participant_id'     => $participant->id,
                'training_id'        => $trainingId,  // Add the training_id to the document
                'file_path'          => $filePath,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }

    //load the edit blade 
    public function participantedit($id)
    {
        try {
            // Find the participant and load related data
            $participant = Participant::with(['remarks', 'sureties', 'training'])->findOrFail($id);

            return view('SuperAdmin.participant.create', compact('participant'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading edit page: ' . $e->getMessage());
        }
    }
    //create participant update method
    public function updateparticipant(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'name'                         => 'required|string|max:255',
                'epf_number'                   => 'required|string|max:50',
                'designation'                  => 'required|string|max:255',
                'salary_scale'                 => 'nullable|string|max:255',
                'location'                     => 'nullable|string|max:255',
                'obligatory_period'            => 'nullable|string|max:255',
                'cost_per_head'                => 'nullable|numeric',
                'bond_completion_date'         => 'nullable|date',
                'bond_value'                   => 'nullable|numeric',
                'date_of_signing'              => 'nullable|date',
                'age_as_at_commencement_date'  => 'nullable|numeric',
                'date_of_appointment'          => 'nullable|date',
                'date_of_appointment_to_the_present_post' => 'nullable|date',
                'date_of_birth'                => 'nullable|date',
                'division_id'                  => 'nullable|exists:divisions,id',
                'section_id'                   => 'nullable|exists:sections,id',
                'training_id'                  => 'required|exists:trainings,id',

                // Surety Validation (2 sureties)
                'sureties'                      => 'nullable|array|max:2',
                'sureties.*.suretyname'          => 'nullable|string|max:255',
                'sureties.*.nic'                 => 'nullable|string|max:12',
                'sureties.*.mobile'              => 'nullable|string|max:15',
                'sureties.*.address'             => 'nullable|string|max:255',
                'sureties.*.salary_scale'        => 'nullable|numeric|max:999999999',
                'sureties.*.suretydesignation'   => 'nullable|string|max:255',
                'sureties.*.epf_number'          => 'nullable|string|max:50',

                // Remarks Validation (Multiple remarks)
                'remarks'                     => 'nullable|array',
                'remarks.*'                   => 'nullable|string|max:500',
            ]);

            // Find the participant
            $participant = Participant::findOrFail($id);

            // Update participant details (excluding remarks and sureties)
            $participant->update($request->except(['remarks', 'sureties']));

            // Update or create remarks
            if ($request->has('remarks')) {
                // Remove old remarks before adding new ones
                $participant->remarks()->delete();
                foreach ($request->remarks as $remarkText) {
                    if (!empty($remarkText)) {
                        $participant->remarks()->create([
                            'remark' => $remarkText,
                            'training_id' => $participant->training_id
                        ]);
                    }
                }
            }

            // Update or create sureties
            if ($request->has('sureties')) {
                // Loop through the sureties and check if it's a new or existing one
                foreach ($request->sureties as $index => $suretyData) {
                    if (isset($participant->sureties[$index])) {
                        // Update existing surety
                        $participant->sureties[$index]->update([
                            'name' => $suretyData['suretyname'],
                            'nic' => $suretyData['nic'],
                            'mobile' => $suretyData['mobile'],
                            'address' => $suretyData['address'],
                            'salary_scale' => $suretyData['salary_scale'],
                            'designation' => $suretyData['suretydesignation'],
                            'epf_number' => $suretyData['epf_number'],
                        ]);
                    } else {
                        // Create new surety
                        $participant->sureties()->create([
                            'name' => $suretyData['suretyname'],
                            'nic' => $suretyData['nic'],
                            'mobile' => $suretyData['mobile'],
                            'address' => $suretyData['address'],
                            'salary_scale' => $suretyData['salary_scale'],
                            'designation' => $suretyData['suretydesignation'],
                            'epf_number' => $suretyData['epf_number'],
                        ]);
                    }
                }
            }

            return redirect()->route('SuperAdmin.participant.Detail', ['id' => $participant->training_id])
                ->with('success', 'Participant updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating participant: ' . $e->getMessage());
        }
    }

    public function exportParticipantColumns()
    {
        try {
            // Attempt to export the column names as an Excel file
            return Excel::download(new ParticipantExport, 'participant_columns.xlsx');
        } catch (\Exception $e) {
            // Catch any exceptions and return a meaningful error message
            return redirect()->back()->with('error', 'An error occurred while exporting: ' . $e->getMessage());
        }
    }

    public function importParticipants(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'training_id' => 'required|exists:trainings,id' // Make sure training_id is validated
        ]);

        try {
            // Get the training_id from the request
            $trainingId = $request->training_id;

            // Pass the training_id to the import class
            Excel::import(new ParticipantImport($trainingId), $request->file('file'));

            return redirect()->back()->with('success', 'Participants imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing participants: ' . $e->getMessage());
        }
    }


    //delete participant
    public function destroyparticipant($id)
    {
        try {
            $participant = Participant::findOrFail($id);

            // Delete related remarks
            $participant->remarks()->delete();

            // Delete related sureties
            $participant->sureties()->delete();

            // Delete related documents
            $participant->documents()->delete();

            // Finally, delete the participant
            $participant->delete();

            return redirect()->route('participants.index')->with('success', 'Participant deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting participant: ' . $e->getMessage());
        }
    }


    //End Participant handling
    //budget handling
    public function budgetview(Request $request)
    {
        try {
            $query = $request->input('query');

            // Retrieve budgets with optional query filter and pagination
            $budgets = Budget::when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('type', 'LIKE', "%{$query}%")
                    ->orWhere('provide_type', 'LIKE', "%{$query}%")
                    ->orWhere('amount', 'LIKE', "%{$query}%")
                    ->orWhere('created_at', 'LIKE', "%{$query}%");
            })->paginate(10); // Ensure paginate() is used

            // Return the view with budgets and query
            return view('SuperAdmin.budget.Detail', compact('budgets', 'query'));
        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    //budget create page
    public function createbudgetview()
    {
        try {
            return view('SuperAdmin.budget.Create');
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading budget create page: ' . $e->getMessage());
        }
    }

    public function budgetstore(Request $request)
    {
        try {
            $request->validate([
                'type'              => 'string|required',
                'provide_type'      => 'required|string',
                'amount'            => 'numeric |max:999999999'
            ]);

            Budget::create([
                'type' => $request->type,
                'amount' => $request->amount,
                'provide_type' => $request->provide_type,
                'division_id' => 1
            ]);

            return redirect()->route('SuperAdmin.budget.Detail')->with('success', 'Budget created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error storing budget detail: ' . $e->getMessage());
        }
    }
    //end budget handling functions


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

    public function approval()
    {
        $approvals = Approval::where('status', 'pending')->get();
        return view('SuperAdmin.approval.Detail', compact('approvals'));
    }

    public function approve(Approval $approval)
    {
        try {
            // Ensure the approval is still pending
            if ($approval->status !== 'pending') {
                return redirect()->back()->with('warning', 'This request has already been processed or is not pending.');
            }

            // Find the model by its training code and model type
            $model = $approval->model_type::where('training_code', $approval->model_id)->first();

            // If model not found, return error
            if (!$model) {
                return redirect()->back()->with('error', 'Training record not found.');
            }

            // Process action (edit)
            if ($approval->action === 'edit') {
                // Decode the new data and update the model
                $newData = json_decode($approval->new_data, true);
                if ($newData) {
                    $model->update($newData);
                    return redirect()->back()->with('success', 'Training record updated successfully.');
                }
                return redirect()->back()->with('error', 'Invalid new data for update.');
            }

            // Process action (delete)
            if ($approval->action === 'delete') {
                // Begin a transaction to ensure consistency
                DB::beginTransaction();

                // Detach related models (institutes and trainers)
                $model->institutes()->detach();
                $model->trainers()->detach();

                // Delete related records (subjects, remarks, and cost breakdowns)
                $model->subjects()->delete();
                Remark::where('training_id', $model->id)->delete();
                Costbreak::where('training_id', $model->id)->delete();

                // Finally, delete the training record
                $model->delete();

                // Commit the transaction
                DB::commit();
                return redirect()->back()->with('success', 'Training record deleted successfully.');
            }

            // If action is not recognized
            return redirect()->back()->with('error', 'Invalid action for approval.');
        } catch (\Exception $e) {
            // Rollback any changes in case of error
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while approving the request: ' . $e->getMessage());
        } finally {
            // Mark approval as 'approved' regardless of action outcome
            if ($approval->status === 'pending') {
                $approval->update(['status' => 'approved']);
            }

            // Remove approval record from the table
            $approval->delete();
        }
    }



    public function reject(Approval $approval)
    {
        // Find the model by its training code and model type
        $model = $approval->model_type::where('training_code', $approval->model_id)->first();

        // If model not found, return error
        if (!$model) {
            return redirect()->back()->with('error', 'Model not found.');
        }

        // If action is 'delete', delete the model
        if ($approval->action === 'delete') {
            $model->delete();
        }

        // Mark approval as 'rejected'
        $approval->update(['status' => 'rejected']);

        // Remove approval record from the table
        $approval->delete();

        // Return rejection message
        return redirect()->back()->with('warning', 'Request rejected and model deleted.');
    }
}
