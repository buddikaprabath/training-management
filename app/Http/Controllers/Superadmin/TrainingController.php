<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Remark;
use App\Models\Country;
use App\Models\Subject;
use App\Models\Trainer;
use App\Models\Document;
use App\Models\Training;
use App\Models\Costbreak;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrainingController extends Controller
{
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
        $training_codes = DB::table('training_codes')->get();

        return view('SuperAdmin.training.create', compact('countries', 'institutes', 'trainers', 'training_codes'));
    }


    //store method for training data storing
    public function createtraining(Request $request)
    {
        $validated = $request->validate([
            'training_name'         => 'required|string|max:255',
            'training_code'         => 'required|string|max:10',
            'mode_of_delivery'      => 'required|string|max:255',
            'training_period_from'  => 'required|date',
            'training_period_to'    => 'required|date|after_or_equal:training_period_from',
            'total_training_hours'  => 'required|integer|max:999',
            'total_program_cost'    => 'required|numeric|between:0,9999999.99',
            'country'               => 'nullable',
            'course_type'           => 'required|string|max:255',
            'category'              => 'required|string|max:255',
            'training_custodian'    => 'nullable|string|max:255',
            'batch_size'            => 'nullable|integer|min:1', // Added min validation for positive batch size
            'division_id'           => 'required|exists:divisions,id',
            'section_id'            => 'nullable|exists:sections,id',
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to', // Updated to ensure the expiration date is after the training end date
            'duration'              => 'required|string|max:255',
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
                'duration'              => $validated['duration'],
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
            $training_codes = DB::table('training_codes')->get();

            // Return the view with all necessary data
            return view('SuperAdmin.training.create', compact('training', 'institutes', 'trainers', 'subjects', 'countries', 'training_codes'));
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
            'training_code'         => 'required|string|max:10',
            'mode_of_delivery'      => 'required|string|max:255',
            'training_period_from'  => 'required|date',
            'training_period_to'    => 'required|date|after_or_equal:training_period_from',
            'total_training_hours'  => 'required|integer|max:999',
            'total_program_cost'    => 'required|numeric|between:0,9999999.99',
            'country'               => 'nullable',
            'course_type'           => 'required|string|max:255',
            'category'              => 'required|string|max:255',
            'training_custodian'    => 'nullable|string|max:255',
            'batch_size'            => 'nullable|integer|min:1', // Added min validation for positive batch size
            'division_id'           => 'required|exists:divisions,id',
            'section_id'            => 'nullable|exists:sections,id',
            'other_comments'        => 'nullable|string|max:255', // Added max length for comments
            'training_structure'    => 'nullable|string|max:255',
            'exp_date'              => 'nullable|date|after_or_equal:training_period_to', // Updated to ensure the expiration date is after the training end date
            'duration'              => 'required|string|max:255',
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
                'duration'              => $validated['duration'],
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
    public function storeTrainingDocument(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'               => 'required|string|max:255',
                'status'             => 'nullable|string|max:50',
                'date_of_submitting' => 'nullable|date',
                'training_id'        => 'required',
                'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Max 2MB
            ]);
            DB::beginTransaction();
            // Store file in storage/app/public/documents
            $filePath = $request->file('document_file')->store('documents', 'public');

            // Create the document record
            Document::create([
                'name'               => $validated['name'],
                'status'             => $validated['status'] ?? null,
                'date_of_submitting' => $validated['date_of_submitting'] ?? null,
                'training_id'        => $validated['training_id'],
                'file_path'          => $filePath,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }

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
}
