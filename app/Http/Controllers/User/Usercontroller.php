<?php

namespace App\Http\Controllers\User;

use App\Models\Grade;
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
use App\Models\Notification;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Illuminate\Validation\Rule;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Usercontroller extends Controller
{
    public function getNotifications()
    {
        $userId = Auth::id();

        // Fetch the latest 10 notifications, paginated, where status is either 'pending' or read within the last week
        $notifications = Notification::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($query) {
                        $query->where('status', 'read')
                            ->where('read_at', '>=', now()->subWeek()); // Filter read notifications in the last week
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Paginate with 10 items per page

        // Count total pending notifications
        $totalPending = Notification::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        return view('User.notifications.Detail', compact('notifications', 'totalPending'));
    }

    public function statusupdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'in:pending,read', // Ensure the status is one of the allowed values
        ]);

        try {
            $notification = Notification::findOrFail($id);

            // Update the 'status' field with the value from the request
            $notification->update([
                'status' => $request->input('status'),
                'read_at' => now(),
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading this message: ' . $e->getMessage());
        }
    }




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
            $training_codes = DB::table('training_codes')->get();

            return view('User.training.create', compact('countries', 'institutes', 'trainers', 'training_codes'));
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
            'duration'              => 'required|string|max:255',
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
                'duration'              => $validated['duration'],
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
            $training_codes = DB::table('training_codes')->get();

            // Return the view with all necessary data
            return view('User.training.create', compact('training', 'institutes', 'trainers', 'subjects', 'countries', 'training_codes'));
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
            'duration'              => 'required|string|max:255',
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
                'model_id'   => (string) $training->id,
                'action'     => 'edit',
                'new_data'   => json_encode($validated),
                'status'     => 'pending',
                'division_id' => $userDivision,  // Pass the division_id
                'section_id' => $userSection,
            ]);

            $message = "Approval Request Submitted : A new approval request has been submitted for editing a training record.                   Please review and take the necessary action.";

            $user_role = 'superadmin';

            // Create a notification
            Notification::create([
                'message'  => $message,
                'status'   => 'pending',
                'user_role' => $user_role,
                'model_id'   => (string) $training->id,
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

            // Add totalAmount to the validated data for approval
            $validatedData['total_amount'] = $totalAmount;

            // Check if there is already an approval request pending for this update
            $existingApproval = Approval::where('model_type', Costbreak::class)
                ->where('model_id', (string) $costBreak->id) // Cast the id to string explicitly
                ->where('action', 'update')
                ->where('status', 'pending')
                ->first();

            // Prevent duplicate approval requests
            if ($existingApproval) {
                return redirect()->back()->with('info', 'An update request is already pending approval for this cost breakdown.');
            }

            // Create an approval request for the update action
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Costbreak::class,
                'model_id'   => (string) $costBreak->id,
                'action'     => 'update',
                'new_data'   => json_encode($validatedData), // Save the updated data as new_data
                'status'     => 'pending',
                'division_id' => Auth::user()->division_id,  // Pass the division_id
            ]);

            $message = "Approval Request Submitted:  
                        A new approval request has been submitted for updating a cost breakdown record.Please review and take the necessary action.";

            $user_role = 'superadmin';
            // Create a notification
            Notification::create([
                'message'  => $message,
                'status'   => 'pending',
                'user_role' => $user_role,
                'model_id'   => (string) $costBreak->id,
            ]);

            // Redirect back with a success message
            return redirect()->route('User.training.costDetail', ['id' => $costBreak->training_id])
                ->with('success', 'Your update request has been sent for approval!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating cost breakdown: ' . $e->getMessage());
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

            // Find the Costbreak record
            $costs = Costbreak::findOrFail($id);

            // Get the authenticated user ID
            $userId = Auth::user()->id;

            // Create an approval record for the deletion request
            $approval = Approval::create([
                'model_type' => Costbreak::class,
                'model_id' => (string) $costs->id,  // Cast to string as ID is auto-increment
                'action' => 'delete',                // Specify the action to be 'delete'
                'status' => 'pending',               // Set status as 'pending'
                'new_data' => null,                  // No new data for deletion
                'user_id' => $userId,                // Add the user ID for tracking who requested the deletion
            ]);

            DB::commit();

            // Return success message to inform the user that approval is pending
            return redirect()->back()->with('success', 'Costbreak deletion request has been submitted for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error submitting deletion request: ' . $e->getMessage());
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

    //store subject according to the training
    public function storeSubject(Request $request)
    {
        $trainingId = $request->training_id;
        // Get all subject inputs from the request (both default and dynamically added)
        $subjects = [];

        // Check for the default subject field (named "subject_name" without a number)
        if ($request->has('subject_name') && !empty($request->subject_name)) {
            $subjects[] = $request->subject_name;
        }

        // Get all dynamically added subject fields (named subject_name_1, subject_name_2, etc.)
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'subject_name_') === 0 && !empty($value)) {
                $subjects[] = $value;
            }
        }

        // Validate that at least one subject name is provided
        if (empty($subjects)) {
            return redirect()->back()->with('error', 'Please add at least one subject.');
        }

        // Check the current subject count for this training
        $currentSubjectCount = Subject::where('training_id', $trainingId)->count();
        $totalSubjectsAfterAdd = $currentSubjectCount + count($subjects);

        // Ensure the training won't exceed 15 subjects
        if ($totalSubjectsAfterAdd > 15) {
            return redirect()->back()->with('error', 'A training can have a maximum of 15 subjects. You currently have ' . $currentSubjectCount . ' subjects and are trying to add ' . count($subjects) . ' more.');
        }

        // Begin transaction to ensure all subjects are saved together
        DB::beginTransaction();

        try {
            $duplicateSubjects = [];
            $addedSubjects = 0;

            // Process each subject
            foreach ($subjects as $subjectName) {
                // Check if this subject already exists for this training
                $existingSubject = Subject::where('training_id', $trainingId)
                    ->where('subject_name', $subjectName)
                    ->first();

                if ($existingSubject) {
                    // Add to duplicate list
                    $duplicateSubjects[] = $subjectName;
                    continue;
                }
                // Create a new subject record
                $subject = new Subject();
                $subject->training_id = $trainingId;
                $subject->subject_name = $subjectName;
                $subject->save();
                $addedSubjects++;
            }

            DB::commit();

            // Prepare response message
            if ($addedSubjects > 0) {
                $message = $addedSubjects . ' subject(s) added successfully.';

                if (!empty($duplicateSubjects)) {
                    $message .= ' The following subjects were not added because they already exist: ' . implode(', ', $duplicateSubjects);
                    return redirect()->back()->with('error', $message);
                }

                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'No subjects were added. All subjects already exist: ' . implode(', ', $duplicateSubjects));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add subjects. ' . $e->getMessage());
        }
    }
    // Training delete function
    public function trainingdestroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the training by ID
            $training = Training::findOrFail($id);

            // Ensure the training_code is in the correct format
            $trainingCode = $training->id;

            // Check if an approval request already exists for this deletion
            $existingApproval = Approval::where('model_type', Training::class)
                ->where('model_id', $trainingCode)  // Use the formatted training_code
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
                'model_id'   => $trainingCode,  // Store formatted training_code
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

            // Retrieve participants with pagination and their remarks
            $participants = $training->participants()->with('remarks')->paginate(10);

            // get the subject relationship
            $Subjects = $training->subjects;

            // Return the view with the data
            return view('User.participant.Detail', [
                'training' => $training,
                'participants' => $participants, // Paginated participants
                'institutes' => $training->institutes,
                'documents' => $training->documents, // Pass the document details
                'subjects' => $Subjects,
            ]);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatecompletionStatus(Request $request)
    {
        try {
            $participantId = $request->participant_id;
            $request->validate([
                'completion_status' => 'required|in:attended,unattended',
            ]);

            $participant = Participant::findOrFail($participantId);
            $participant->update([
                'completion_status' => $request->completion_status,
            ]);

            return redirect()->back()->with('success', 'Completion status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // store participant grades
    public function gradeStore(Request $request)
    {
        try {
            $request->validate([
                'training_id' => 'required|exists:trainings,id',
                'participant_id' => 'required|exists:participants,id',
                'subject_id' => 'required|exists:subjects,id',
                'grade' => 'required|string|max:5',
            ]);

            // Check if the grade already exists for the same training, participant, and subject
            $existingGrade = Grade::where([
                'training_id' => $request->training_id,
                'participant_id' => $request->participant_id,
                'subject_id' => $request->subject_id,
            ])->first();

            if ($existingGrade) {
                return redirect()->back()->with('error', "This participant (ID: {$request->participant_id}) already has a grade for this subject (ID: {$request->subject_id}) in training (ID: {$request->training_id}).");
            }

            // Store the grade
            Grade::create([
                'training_id' => $request->training_id,
                'participant_id' => $request->participant_id,
                'subject_id' => $request->subject_id,
                'grade' => $request->grade,
            ]);

            return redirect()->back()->with('success', 'Grade added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding grade: ' . $e->getMessage());
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
            'epf_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('participants')->where(function ($query) use ($request) {
                    return $query->where('training_id', $request->training_id);
                }),
            ],
            'designation'                  => 'required|string|max:255',
            'salary_scale'                  => 'nullable|numeric|min:0|max:9999999999',
            'location'                     => 'nullable|string|max:255',
            'obligatory_period'            => 'nullable|date',
            'cost_per_head'                => 'nullable|numeric|min:0|max:9999999999', // You can use 'decimal:10,2' if you expect decimals
            'bond_completion_date'         => 'nullable|date',
            'bond_value'                   => 'nullable|numeric|min:0|max:9999999999', // You can use 'decimal:12,2' for decimals
            'date_of_signing'              => 'nullable|date',
            'age_as_at_commencement_date'  => 'nullable|numeric', // Assuming it's a decimal with 2 places after decimal
            'date_of_appointment'          => 'nullable|date',
            'date_of_appointment_to_the_present_post' => 'nullable|date',
            'date_of_birth'                => 'nullable|date',
            'division_id'                  => 'required|exists:divisions,id',
            'section_id'                    => 'nullable|exists:sections,id',

            // Surety Validation (2 sureties)
            'sureties'                      => 'nullable|array|max:2',
            'sureties.*.name'               => 'nullable|string|max:255',
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
            $participantId = $participant->id;
            // Store Sureties (up to 2)
            if ($request->sureties) {
                foreach ($request->sureties as $suretyData) {
                    Surety::create([
                        'name'           => $suretyData['name'],
                        'epf_number'     => $suretyData['epf_number'],
                        'nic'            => $suretyData['nic'],
                        'mobile'         => $suretyData['mobile'],
                        'address'        => $suretyData['address'],
                        'salary_scale'   => $suretyData['salary_scale'] ?? null,
                        'designation'    => $suretyData['suretydesignation'] ?? null,
                        'participant_id' => $participantId,
                    ]);
                }
            }

            // Store Multiple Remarks
            if ($request->has('remarks')) {
                $remarks = is_array($request->remarks) ? $request->remarks : [$request->remarks];

                foreach ($remarks as $remark) {
                    Remark::create([
                        'remark'        => $remark,
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
            DB::beginTransaction();

            // Validate the request data
            $request->validate([
                'name'                         => 'required|string|max:255',
                'epf_number'                   => 'required|string|max:50',
                'designation'                  => 'required|string|max:255',
                'salary_scale'                 => 'nullable|numeric|min:0|max:9999999999',
                'location'                     => 'nullable|string|max:255',
                'obligatory_period'            => 'nullable|date',
                'cost_per_head'                => 'nullable|numeric|min:0|max:9999999999',
                'bond_completion_date'         => 'nullable|date',
                'bond_value'                   => 'nullable|numeric|min:0|max:9999999999',
                'date_of_signing'              => 'nullable|date',
                'age_as_at_commencement_date'  => 'nullable|numeric',
                'date_of_appointment'          => 'nullable|date',
                'date_of_appointment_to_the_present_post' => 'nullable|date',
                'date_of_birth'                => 'nullable|date',
                'division_id'                  => 'required|exists:divisions,id',
                'section_id'                    => 'nullable|exists:sections,id',
                'training_id'                  => 'required|exists:trainings,id',

                // Surety Validation (2 sureties)
                'sureties'                      => 'nullable|array|max:2',
                'sureties.*.name'               => 'nullable|string|max:255',
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

            // Prepare updated data
            $updatedData = [
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
                'training_id'                   => $request->training_id,
                'sureties'                      => $request->sureties,
                'remarks'                       => $request->remarks,
            ];

            // Check if an approval request already exists
            $existingApproval = Approval::where('model_type', Participant::class)
                ->where('model_id', (string) $participant->id)
                ->where('action', 'update')
                ->where('status', 'pending')
                ->first();

            if ($existingApproval) {
                return redirect()->route('User.participant.Detail', ['id' => $participant->training_id])
                    ->with('info', 'An update request for this participant is already pending approval.');
            }

            // Create an approval request for the update
            Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Participant::class,
                'model_id'   => (string) $participant->id,
                'action'     => 'update',
                'new_data'   => json_encode($updatedData), // Store the updated data as JSON
                'status'     => 'pending',
                'division_id' => $request->division_id,  // Pass the division_id
            ]);

            DB::commit();
            return redirect()->route('User.participant.Detail', ['id' => $participant->training_id])
                ->with('success', 'Your update request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error occurred while sending update request: ' . $e->getMessage());
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
            DB::beginTransaction();

            // Find the participant by ID
            $participant = Participant::findOrFail($id);

            // Check if an approval request already exists for this deletion
            $existingApproval = Approval::where('model_type', Participant::class)
                ->where('model_id', (string) $participant->id)
                ->where('action', 'delete')
                ->where('status', 'pending')
                ->first();

            // Prevent duplicate approval requests
            if ($existingApproval) {
                return redirect()->back()->with('info', 'A deletion request is already pending for this participant.');
            }

            // Create an approval request for deletion
            $approvalRequest = Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Participant::class,
                'model_id'   => (string) $participant->id,
                'action'     => 'delete',
                'new_data'   => null,  // No new data as we are deleting the record
                'status'     => 'pending',
                'division_id' => Auth::user()->division_id,  // Pass the division_id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Your deletion request has been sent for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error occurred while sending deletion request: ' . $e->getMessage());
        }
    }



    //End Participant handling
    //training handling
    public function trainingsummaryView()
    {
        return view('User.report.training');
    }
}
