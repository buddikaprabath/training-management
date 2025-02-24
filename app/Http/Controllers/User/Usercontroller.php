<?php

namespace App\Http\Controllers\User;

use App\Models\Remark;
use App\Models\Surety;
use App\Models\Country;
use App\Models\Subject;
use App\Models\Trainer;
use App\Models\Approval;
use App\Models\Document;
use App\Models\Training;
use App\Models\Costbreak;
use App\Models\Institute;
use App\Models\Participant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Usercontroller extends Controller
{
    public function viewDashboard()
    {
        return view('User.page.dashboard');
    }
    public function trainingview(Request $request, $itemId = null)
    {
        try {
            $query = $request->input('query');

            // Get logged-in user's division and section
            $user = Auth::user();
            $userDivision = $user->division_id;
            $userSection = $user->section_id;

            // Get training data with filtering based on user access
            $training = Training::join('divisions', 'trainings.division_id', '=', 'divisions.id')
                ->select('trainings.*', 'divisions.division_name')
                ->where('trainings.division_id', $userDivision) // Filter by user's division
                ->when($userSection, function ($q) use ($userSection) {
                    $q->where('trainings.section_id', $userSection); // Filter by user's section (if applicable)
                })
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($subQuery) use ($query) {
                        $subQuery->where('trainings.training_name', 'LIKE', "%{$query}%")
                            ->orWhere('trainings.training_code', 'LIKE', "%{$query}%")
                            ->orWhere('divisions.division_name', 'LIKE', "%{$query}%")
                            ->orWhere('trainings.category', 'LIKE', "%{$query}%")
                            ->orWhere('trainings.mode_of_delivery', 'LIKE', "%{$query}%");
                    });
                })
                ->paginate(10);

            // Instead, pass an empty training flag to the view
            $trainingEmpty = $training->isEmpty();

            // Fetch Costbreak data if itemId is provided
            $costBreak = $itemId ? Costbreak::where('item_id', $itemId)->first() : null;

            return view('User.training.Detail', compact('training', 'query', 'costBreak', 'trainingEmpty'));
        } catch (\Exception $e) {
            // Catch any exceptions and show an error message
            return redirect()->back()->with('error', 'An error occurred while fetching training details. Please try again later.');
        }
    }


    //load the training create page
    public function createtrainingview()
    {
        try {
            $countries = DB::table('countries')->get();
            $institutes = Institute::all();
            $trainers = Trainer::all(); // Fetch all trainers

            return view('User.training.create', compact('countries', 'institutes', 'trainers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again later.');
        }
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
            'batch_size'            => 'nullable|integer|min:1',
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to',
            'institutes'            => 'required|array',
            'institutes.*'          => 'exists:institutes,id',
            'trainers'              => 'required|array',
            'trainers.*'            => 'exists:trainers,id',
            'remark'                => 'nullable|string|max:255',
            'subject_type'          => 'nullable|string|max:255',
            'subject_name'          => 'nullable|string|max:255',
            'dead_line'             => 'required|date',
        ]);
        // Get logged-in user's division and section
        $user = Auth::user();
        $userDivision = $user->division_id;
        $userSection = $user->section_id;

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
                'division_id'           => $userDivision,
                'section_id'            => $userSection ?? 0,
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
                Remark::create([
                    'remark'        => $validated['remark'],
                    'training_id'   => $training->id,
                    'participant_id' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('User.training.Detail')->with('success', 'Training created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error occurred while saving training. Please try again later:' . $e->getMessage());
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
            return view('User.training.create', compact('training', 'institutes', 'trainers', 'subjects', 'countries'));
        } catch (\Exception $e) {
            // If an error occurs, redirect back with an error message
            return back()->with('error', 'Error loading training details: ' . $e->getMessage());
        }
    }
    //handle the update training function
    public function updatetraining(Request $request, $id)
    {
        // Validate the input fields
        $validated = $request->validate([
            'training_name'         => 'required|string|max:255',
            'training_code'         => 'required|string|max:10|unique:trainings,training_code,' . $id,
            'mode_of_delivery'      => 'required|string|max:255',
            'training_period_from'  => 'required|date',
            'training_period_to'    => 'required|date|after_or_equal:training_period_from',
            'total_training_hours'  => 'required|integer|max:255',
            'total_program_cost'    => 'required|numeric|between:0,9999999.99',
            'course_type'           => 'required|string|max:255',
            'category'              => 'required|string|max:255',
            'training_custodian'    => 'nullable|string|max:255',
            'batch_size'            => 'nullable|integer|min:1',
            'other_comments'        => 'nullable|string|max:255',
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to',
            'institutes'            => 'required|array',
            'institutes.*'          => 'exists:institutes,id',
            'trainers'              => 'required|array',
            'trainers.*'            => 'exists:trainers,id',
            'remark'                => 'nullable|string|max:255',
            'subject_type'          => 'nullable|string|max:255',
            'subject_name'          => 'nullable|string|max:255',
            'dead_line'             => 'required|date',
        ]);

        // Get logged-in user's division and section
        $user = Auth::user();
        $userDivision = $user->division_id;
        $userSection = $user->section_id;

        try {
            DB::beginTransaction();

            // Find the training by ID
            $training = Training::findOrFail($id);

            // Create an approval request instead of updating the training directly
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Training::class,
                'model_id'   => (string) $training->training_code,
                'action'     => 'edit',
                'new_data'   => json_encode($validated),
                'status'     => 'pending',
                'division_id' => $userDivision,  // Pass the division_id
                'section_id' => $userSection,
            ]);

            DB::commit();
            return redirect()->route('User.training.Detail')->with('success', 'Your update request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while sending approval request: ' . $e->getMessage());
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
            return redirect()->route('User.training.Detail', ['id' => $trainingId])
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

            return view('User.training.costDetail', compact('costs'));
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
            return redirect()->route('User.training.costDetail', ['id' => $costBreak->training_id])
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

            return view('User.training.costbreak', compact('costs'));
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
            return redirect()->route('User.training.Detail')->with('success', 'Cost break down deleted successfully!');
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

    // Training delete function
    public function trainingdestroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the training by ID
            $training = Training::findOrFail($id);

            // Check if an approval request already exists for this deletion
            $existingApproval = Approval::where('model_type', Training::class)
                ->where('model_id', (string) $training->training_code)
                ->where('action', 'delete')
                ->where('status', 'pending')
                ->first();

            // Prevent duplicate approval requests
            if ($existingApproval) {
                return redirect()->route('User.training.Detail')->with('info', 'A deletion request is already pending for this training.');
            }

            // Create an approval request for deletion
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Training::class,
                'model_id'   => (string) $training->training_code,
                'action'     => 'delete',
                'new_data'   => null,  // No new data as we are deleting the record
                'status'     => 'pending',
                'division_id' => Auth::user()->division_id,  // Pass the division_id
            ]);

            DB::commit();
            return redirect()->route('User.training.Detail')->with('success', 'Your deletion request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('User.training.Detail')->with('error', 'Error occurred while sending deletion request: ' . $e->getMessage());
        }
    }



    //end training handling

    //participant handling

    //load the participant view blade
    public function participantview($trainingId)
    {
        try {
            // Attempt to retrieve training and related data, including documents
            $training = Training::with(['remarks', 'institutes', 'documents'])  // Load documents as well
                ->find($trainingId);

            if (!$training) {
                return redirect()->back()->with('error', 'Training not found.');
            }

            // Retrieve the participants with pagination
            $participants = $training->participants()->paginate(10); // Paginate participants

            // Return the view with the data
            return view('User.participant.Detail', [
                'training' => $training,
                'participants' => $participants, // Paginated participants
                'institutes' => $training->institutes,
                'documents' => $training->documents, // Pass the document details
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
            return view('User.participant.create', compact('training'));
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

        // Get logged-in user's division and section
        $user = Auth::user();
        $userDivision = $user->division_id;
        $userSection = $user->section_id;
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
                'division_id'                   => $userDivision,
                'section_id'                    => $userSection,
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

            return redirect()->route('User.training.Detail')->with('success', 'Participant added successfully! You can now upload documents.');
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

            return view('User.participant.create', compact('participant'));
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
            // Get logged-in user's division and section
            $user = Auth::user();
            $userDivision = $user->division_id;
            $userSection = $user->section_id;
            // Update participant details (excluding remarks and sureties)
            $participant->update([
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
                'division_id'                   => $userDivision,
                'section_id'                    => $userSection,
                'training_id'                   => $request->training_id, // Passed from the clicked training
            ]);

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

            return redirect()->route('User.participant.Detail', ['id' => $participant->training_id])
                ->with('success', 'Participant updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating participant: ' . $e->getMessage());
        }
    }
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function exportParticipantColumns()
    {
        try {
            // Attempt to export the column names as an Excel file using the injected Excel instance
            return $this->excel->download(new ParticipantExport, 'participant_columns.xlsx');
        } catch (\Exception $e) {
            // Catch any exceptions and return a meaningful error message
            return redirect()->back()->with('error', 'An error occurred while exporting: ' . $e->getMessage());
        }
    }

    public function importParticipants(Request $request)
    {
        // Validate the file and training_id fields
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'training_id' => 'required|exists:trainings,id' // Ensure the training_id exists in the trainings table
        ]);

        try {
            // Get the training ID from the request
            $trainingId = $request->training_id;

            // Use the injected Excel instance to import the file and pass the training_id to the import class
            $this->excel->import(new ParticipantImport($trainingId), $request->file('file'));

            // Return success message if import is successful
            return redirect()->back()->with('success', 'Participants imported successfully!');
        } catch (\Exception $e) {
            // Catch any exceptions and return error message
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

            return redirect()->route('User.participants.index')->with('success', 'Participant deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting participant: ' . $e->getMessage());
        }
    }


    //End Participant handling
    //training handling
    public function trainingsummaryView()
    {
        return view('User.report.training');
    }
}
