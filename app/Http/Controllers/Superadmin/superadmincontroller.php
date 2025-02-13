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
use APP\Models\Division;
use App\Models\Document;
use App\Models\Training;
use App\Models\Costbreak;
use App\Models\Institute;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Exports\ParticipantExport;
use Illuminate\Support\Facades\DB;
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

    // training detail view page 
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

        // Check if itemId is provided and fetch Costbreak data
        $costBreak = null;
        if ($itemId) {
            $costBreak = Costbreak::where('item_id', $itemId)->first();
        }

        return view('SuperAdmin.training.Detail', compact('training', 'query', 'costBreak'));
    }


    public function createtrainingview()
    {
        $countries = DB::table('countries')->get();
        $institutes = Institute::all();
        $trainers = Trainer::all(); // Fetch all trainers

        return view('SuperAdmin.training.create', compact('countries', 'institutes', 'trainers'));
    }



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

    //add cost break down amounts

    public function storeCostBreakdown(Request $request, $trainingId)
    {
        $validated = $request->validate([
            'airfare'    => 'required|numeric|min:0',
            'subsistence'    => 'required|numeric|min:0',
            'incidental'    => 'required|numeric|min:0',
            'registration'    => 'required|numeric|min:0',
            'visa'    => 'required|numeric|min:0',
            'insurance'    => 'required|numeric|min:0',
            'warm_clothes'    => 'required|numeric|min:0',
            'total_amount'    => 'required|numeric|min:0',
        ]);
        try {
            DB::beginTransaction();

            // Ensure training exists
            $training = Training::where('id', $trainingId)->firstOrFail();

            // Create a new cost breakdown entry
            Costbreak::create([
                'training_id'       => $training->id, // Use 'id' here
                'airfare'           => $validated['airfare'],
                'subsistence'       => $validated['subsistence'],
                'incidental'        => $validated['incidental'],
                'registration'      => $validated['registration'],
                'visa'              => $validated['visa'],
                'insurance'         => $validated['insurance'],
                'warm_clothes'      => $validated['warm_clothes'],
                'total_amount'      => $validated['total_amount']
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cost breakdown added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while adding cost breakdown: ' . $e->getMessage());
        }
    }

    // Controller Method for Update
    public function updateCostBreakdown(Request $request, $itemId)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'airfare' => 'required|numeric|min:0',
            'subsistence' => 'required|numeric|min:0',
            'incidental' => 'required|numeric|min:0',
            'registration' => 'required|numeric|min:0',
            'visa' => 'required|numeric|min:0',
            'insurance' => 'required|numeric|min:0',
            'warm_clothes' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Find the existing Costbreak record for the given itemId
        $costBreak = Costbreak::where('item_id', $itemId)->first();

        if (!$costBreak) {
            // If the cost breakdown doesn't exist, handle the error (optional)
            return redirect()->back()->with('error', 'Cost Breakdown not found!');
        }

        // Update the existing Costbreak record with validated data
        $costBreak->update([
            'airfare' => $validatedData['airfare'],
            'subsistence' => $validatedData['subsistence'],
            'incidental' => $validatedData['incidental'],
            'registration' => $validatedData['registration'],
            'visa' => $validatedData['visa'],
            'insurance' => $validatedData['insurance'],
            'warm_clothes' => $validatedData['warm_clothes'],
            'total_amount' => $validatedData['total_amount'],
        ]);

        // Redirect back with a success message
        return redirect()->route('SuperAdmin.training.Detail', ['itemId' => $itemId])
            ->with('success', 'Cost Breakdown updated successfully!');
    }
    public function getCostBreakdownData($id)
    {
        $costBreakdown = Costbreak::find($id);
        return response()->json($costBreakdown);
    }


    //store document for each training
    public function storeTrainingDocument(Request $request, $trainingId)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'status'             => 'nullable|string|max:50',
            'date_of_submitting' => 'nullable|date',
            'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Max 2MB
        ]);

        try {
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

    //load the participant view blade
    public function participantview($trainingId)
    {
        $training = Training::with(['remarks', 'institutes', 'participants']) // Load participants
            ->find($trainingId);

        if (!$training) {
            return redirect()->back()->with('error', 'Training not found.');
        }

        return view('SuperAdmin.participant.Detail', [
            'training' => $training,
            'participants' => $training->participants, // Ensure participants are passed
            'institutes' => $training->institutes,
        ]);
    }

    //load the create participant blade
    public function createparticipant($trainingId)
    {
        // Find the training to ensure it exists
        $training = Training::findOrFail($trainingId);

        return view('SuperAdmin.participant.create', compact('training'));
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

    //store participant document
    public function storeParticipantDocument(Request $request, $participantId)
    {
        $request->validate([
            'documents'         => 'required|array',
            'documents.*'       => 'file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('documents');
                Document::create([
                    'name'          => $document->getClientOriginalName(),
                    'file_path'     => $path,
                    'training_id'   => Participant::findOrFail($participantId)->training_id,
                    'participant_id' => $participantId,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Documents uploaded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to upload documents: ' . $e->getMessage());
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
        // Export the column names as an Excel file
        return Excel::download(new ParticipantExport, 'participant_columns.xlsx');
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
        $query = $request->input('query');

        // If search query exists, filter users
        if ($query) {
            $budget = Budget::where('name', 'LIKE', "%{$query}%")
                ->orWhere('username', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->paginate(10);
        } else {
            $budget = Budget::paginate(10); // Load all users if no search
        }

        return view('SuperAdmin.budget.Detail', compact('budget', 'query'));
    }
    //budget create page
    public function createbudgetview()
    {
        return view('SuperAdmin.budget.Create');
    }
    //end budget handling functions


    //Institute handling
    public function instituteview(Request $request)
    {
        $query = $request->input('query');

        // Filter based on search query or show all records
        $institutes = Institute::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', '%' . $query . '%');
        })->paginate(10);

        return view('SuperAdmin.institute.Detail', compact('institutes', 'query'));
    }



    public function instituteCreate()
    {
        return view('SuperAdmin.institute.create');
    }


    public function Institutestore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $institute = new Institute();
        $institute->name = $request->name;
        $institute->type = $request->type;
        $institute->save();

        return redirect()->back()->with('success', 'Institute created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $institute = Institute::findOrFail($id);
        $institute->name = $request->name;
        $institute->type = $request->type;
        $institute->save();

        return redirect()->back()->with('success', 'Institute updated successfully.');
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
    //Trainer handling
    public function trainerview($id)
    {

        $trainer = Trainer::findOrFail($id);

        return view('SuperAdmin.trainer.Detail', compact('trainer'));
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
