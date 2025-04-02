<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Grade;
use App\Models\Remark;
use App\Models\Surety;
use App\Models\Document;
use App\Models\Training;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Services\EmpApiService;
use Illuminate\Validation\Rule;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantController extends Controller
{

    /**
     * Display the participant management view for a specific training program
     * 
     * This method retrieves and displays all participants associated with a given training program,
     * along with related information including remarks, institutes, documents, and subjects.
     * The participants are paginated for better performance and user experience.
     *
     * @param int $trainingId The ID of the training program to view participants for
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * 
     * @throws \Exception Catches and handles any unexpected errors during execution
     */
    public function participantview($trainingId)
    {
        try {
            // Eager load the training with its related models to optimize queries:
            // - remarks: Feedback or notes about the training
            // - institutes: Organizations associated with the training
            // - documents: Files related to the training
            $training = Training::with(['remarks', 'institutes', 'documents'])
                ->find($trainingId);

            // Handle case where training doesn't exist
            if (!$training) {
                return redirect()
                    ->back()
                    ->with('error', 'Training not found. Please verify the training ID.');
            }

            // Get paginated list of participants (10 per page) with their remarks
            // Using pagination to improve performance with large datasets
            $participants = $training->participants()
                ->with('remarks') // Eager load participant remarks
                ->paginate(10);

            // Get all subjects associated with this training
            // Subjects represent the curriculum or topics covered in the training
            $subjects = $training->subjects;

            // Prepare and return the view with all necessary data
            return view('SuperAdmin.participant.Detail', [
                // The main training program details
                'training' => $training,

                // Paginated list of participants with their remarks
                'participants' => $participants,

                // Institutes associated with the training (organizers/partners)
                'institutes' => $training->institutes,

                // Documents related to the training program
                'documents' => $training->documents,

                // All subjects covered in this training
                'subjects' => $subjects,
            ]);
        } catch (\Exception $e) {
            // Log the error (consider adding logging here)
            // \Log::error('Error viewing participants: ' . $e->getMessage());

            // Redirect back with user-friendly error message
            return redirect()
                ->back()
                ->with('error', 'An error occurred while loading participants.');
        }
    }
    /**
     * Update the completion status of a training participant
     * 
     * This method handles the AJAX or form submission to change a participant's
     * training completion status between 'attended' and 'unattended'. It validates
     * the input, updates the participant record, and returns appropriate feedback.
     *
     * @param \Illuminate\Http\Request $request Incoming request containing:
     *               - participant_id (required): The ID of the participant to update
     *               - completion_status (required): Must be 'attended' or 'unattended'
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If participant not found
     * @throws \Exception For any other unexpected errors
     */
    public function updatecompletionStatus(Request $request)
    {
        try {
            // 1. Input Validation
            // Ensure we have both required fields with valid values
            $validatedData = $request->validate([
                'participant_id' => 'required|integer|exists:participants,id',
                'completion_status' => 'required|in:attended,unattended',
            ]);

            // 2. Retrieve Participant
            // Find or fail will automatically throw ModelNotFoundException if not found
            $participant = Participant::findOrFail($validatedData['participant_id']);

            // 3. Update Status
            // Only update the completion_status field to maintain data integrity
            $participant->update([
                'completion_status' => $validatedData['completion_status'],
            ]);

            // 4. Post-Update Actions (commented examples)
            // Consider adding these in future if needed:
            // - Log the status change
            // - Send notification to participant
            // - Update training statistics

            // 5. Success Response
            return redirect()
                ->back()
                ->with('success', 'Completion status updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where participant doesn't exist
            return redirect()
                ->back()
                ->with('error', 'Participant not found. Please verify the participant ID.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Catch-all for any other unexpected errors
            // Consider logging the full error here
            // Log::error('Status update failed: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Store a grade record for a participant in a specific training subject
     *
     * Validates and stores a grade assignment while preventing duplicate entries for the same
     * participant-subject-training combination. Ensures data integrity through validation
     * and transaction safety.
     *
     * @param \Illuminate\Http\Request $request Contains:
     *              - training_id (required|exists:trainings,id)
     *              - participant_id (required|exists:participants,id)
     *              - subject_id (required|exists:subjects,id)
     *              - grade (required|string|max:5)
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function gradeStore(Request $request)
    {
        // Begin database transaction for data integrity
        DB::beginTransaction();

        try {
            // 1. VALIDATION
            $validatedData = $request->validate([
                'training_id' => 'required|integer|exists:trainings,id',
                'participant_id' => 'required|integer|exists:participants,id',
                'subject_id' => 'required|integer|exists:subjects,id',
                'grade' => [
                    'required',
                    'string',
                    'max:5',
                    // Add any specific grade format validation if needed
                    // Example: 'regex:/^[A-F][+-]?$/' for letter grades with optional +/-
                ],
            ]);

            // 2. DUPLICATE CHECK
            $existingGrade = Grade::where([
                'training_id' => $validatedData['training_id'],
                'participant_id' => $validatedData['participant_id'],
                'subject_id' => $validatedData['subject_id'],
            ])->first();

            if ($existingGrade) {
                // More user-friendly error message without exposing IDs
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'This participant already has a grade recorded for this subject in the selected training.');
            }

            // 3. GRADE CREATION
            $grade = Grade::create([
                'training_id' => $validatedData['training_id'],
                'participant_id' => $validatedData['participant_id'],
                'subject_id' => $validatedData['subject_id'],
                'grade' => $validatedData['grade'],
                // Consider adding:
                // 'recorded_by' => auth()->id(), // Track who entered the grade
                // 'recorded_at' => now(),        // Timestamp of grade entry
            ]);

            // 4. POST-CREATION ACTIONS
            // Example future enhancements:
            // - Update training statistics
            // - Notify participant/coordinator
            // - Log grade entry in audit trail

            DB::commit(); // Finalize transaction

            return redirect()
                ->back()
                ->with('success', 'Grade recorded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Log database errors specifically
            // Log::error("Grade storage database error: {$e->getMessage()}");
            return redirect()
                ->back()
                ->with('error', 'A database error occurred while saving the grade.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Grade storage error: {$e->getMessage()}");
            return redirect()
                ->back()
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Employee API Service for fetching employee details
     * 
     * @var EmpApiService
     */
    protected EmpApiService $empApiService;

    /**
     * Constructor for dependency injection
     *
     * @param EmpApiService $empApiService Service class for employee data retrieval
     */
    public function __construct(EmpApiService $empApiService)
    {
        $this->empApiService = $empApiService;
    }

    /**
     * Display the participant creation form with employee lookup functionality
     *
     * Handles two scenarios:
     * 1. Initial form load (no EPF number submitted)
     * 2. Employee lookup (EPF number submitted)
     *
     * @param Request $request HTTP request containing optional epf_number
     * @param int $trainingId ID of the training to associate with the participant
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function createparticipant(Request $request, $trainingId)
    {
        try {
            // 1. TRAINING VALIDATION
            // Fail immediately if training doesn't exist
            $training = Training::findOrFail($trainingId);

            // 2. INITIAL FORM REQUEST
            // Return basic form if no EPF lookup requested
            if (!$request->filled('epf_number')) {
                return view('SuperAdmin.participant.create', [
                    'training' => $training,
                    'employee' => null, // Explicit null for clarity
                ]);
            }

            // 3. EMPLOYEE LOOKUP VALIDATION
            $validated = $request->validate([
                'epf_number' => [
                    'required',
                    'string',
                    'max:20',
                    // Consider adding format validation if EPF has specific pattern
                    // 'regex:/^[A-Z]\d{6}$/' 
                ],
            ]);

            // 4. EMPLOYEE DATA RETRIEVAL
            $employee = $this->empApiService->getEmployeeDetailsForParticipant(
                $validated['epf_number']
            );

            if (!$employee) {
                return back()
                    ->withInput()
                    ->with('error', 'Employee not found. Please verify the EPF number.')
                    ->with('epf_error', true); // Flag for client-side handling
            }

            // 5. SUCCESSFUL LOOKUP
            return view('SuperAdmin.participant.create', [
                'training' => $training,
                'employee' => $employee,
                // Consider adding:
                // 'departments' => Department::all(), // If needed for dropdowns
                // 'sections' => Section::all(),       // If needed for dropdowns
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Training not found
            return redirect()
                ->route('trainings.index') // Adjust to your training list route
                ->with('error', 'The specified training program was not found.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // EPF number validation failed
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('epf_error', true);
        } catch (\Exception $e) {
            // Log full error for debugging
            // \Log::error("Participant creation error: {$e->getMessage()}");

            return redirect()
                ->back()
                ->with('error', 'System error while loading form: ' . $e->getMessage());
        }
    }
    /**
     * Store a new participant record with associated sureties and remarks
     * 
     * Handles the creation of a participant along with their sureties (up to 2) and
     * optional remarks. Uses database transactions to ensure data integrity.
     *
     * @param \Illuminate\Http\Request $request HTTP request containing:
     *              - Participant details (required)
     *              - Sureties data (optional, max 2)
     *              - Remarks (optional)
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function participantstore(Request $request)
    {
        // 1. VALIDATION RULES
        $validatedData = $request->validate([
            // Participant Basic Info
            'name' => 'required|string|max:255',
            'epf_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('participants')->where(function ($query) use ($request) {
                    return $query->where('training_id', $request->training_id);
                }),
            ],
            'designation' => 'required|string|max:255',
            'salary_scale' => 'required|string|size:2', // Changed to size:2 for exact length
            'location' => 'nullable|string|max:255',

            // Training Contract Details
            'obligatory_period' => 'nullable|date|after_or_equal:today',
            'cost_per_head' => 'nullable|numeric|between:0,9999999999.99',
            'bond_completion_date' => 'nullable|date|after:today',
            'bond_value' => 'nullable|numeric|between:0,9999999999.99',
            'date_of_signing' => 'nullable|date|before_or_equal:today',

            // Personal Details
            'age_as_at_commencement_date' => 'nullable|integer|min:18|max:70',
            'date_of_appointment' => 'nullable|date|before_or_equal:today',
            'date_of_appointment_to_the_present_post' => 'nullable|date|before_or_equal:today',
            'date_of_birth' => 'nullable|date|before:-18 years', // Minimum 18 years old

            // Organizational Structure
            'division_id' => 'nullable|exists:divisions,id',
            'section_id' => 'nullable|exists:sections,id',
            'training_id' => 'required|exists:trainings,id',

            // Sureties (up to 2)
            'sureties' => 'nullable|array|max:2',
            'sureties.*.suretyname' => 'required_with:sureties|string|max:255',
            'sureties.*.nic' => 'required_with:sureties|string|max:12|regex:/^[0-9]{9}[vVxX]?$/',
            'sureties.*.mobile' => 'required_with:sureties|string|max:15|regex:/^[0-9]{10}$/',
            'sureties.*.address' => 'required_with:sureties|string|max:255',
            'sureties.*.salary_scale' => 'nullable|numeric|between:0,999999999.99',
            'sureties.*.suretydesignation' => 'required_with:sureties|string|max:255',
            'sureties.*.epf_number' => 'required_with:sureties|string|max:50',

            // Remarks
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // 2. PARTICIPANT CREATION
            $participant = Participant::create([
                'name' => $validatedData['name'],
                'epf_number' => $validatedData['epf_number'],
                'designation' => $validatedData['designation'],
                'salary_scale' => $validatedData['salary_scale'],
                'location' => $validatedData['location'] ?? null,
                'obligatory_period' => $validatedData['obligatory_period'] ?? null,
                'cost_per_head' => $validatedData['cost_per_head'] ?? null,
                'bond_completion_date' => $validatedData['bond_completion_date'] ?? null,
                'bond_value' => $validatedData['bond_value'] ?? null,
                'date_of_signing' => $validatedData['date_of_signing'] ?? null,
                'age_as_at_commencement_date' => $validatedData['age_as_at_commencement_date'] ?? null,
                'date_of_appointment' => $validatedData['date_of_appointment'] ?? null,
                'date_of_appointment_to_the_present_post' => $validatedData['date_of_appointment_to_the_present_post'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                'division_id' => $validatedData['division_id'] ?? null,
                'section_id' => $validatedData['section_id'] ?? null,
                'training_id' => $validatedData['training_id'],
            ]);

            // 3. SURETY CREATION (if provided)
            if (!empty($validatedData['sureties'])) {
                foreach ($validatedData['sureties'] as $suretyData) {
                    Surety::create([
                        'name' => $suretyData['suretyname'],
                        'epf_number' => $suretyData['epf_number'],
                        'nic' => $suretyData['nic'],
                        'mobile' => $suretyData['mobile'],
                        'address' => $suretyData['address'],
                        'salary_scale' => $suretyData['salary_scale'] ?? null,
                        'designation' => $suretyData['suretydesignation'],
                        'participant_id' => $participant->id,
                    ]);
                }
            }

            // 4. REMARK CREATION (if provided)
            if (!empty($validatedData['remarks'])) {
                foreach (array_filter($validatedData['remarks']) as $remark) {
                    if (!empty(trim($remark))) {
                        Remark::create([
                            'remark' => trim($remark),
                            'training_id' => $validatedData['training_id'],
                            'participant_id' => $participant->id,
                        ]);
                    }
                }
            }

            DB::commit();

            // 5. POST-CREATION ACTIONS
            // Example future enhancements:
            // - Send notification to participant
            // - Log the creation in audit trail
            // - Trigger any onboarding workflows

            return redirect()
                ->route('SuperAdmin.training.Detail', ['id' => $validatedData['training_id']])
                ->with('success', 'Participant added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Log::error("Participant creation database error: {$e->getMessage()}");
            return back()
                ->withInput()
                ->with('error', 'Database error while saving participant.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Participant creation error: {$e->getMessage()}");
            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    /**
     * Store a document for a participant
     *
     * Handles file upload and creates a document record associated with a participant and training
     */
    public function storeParticipantDocument(Request $request)
    {
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                // Participant must exist in database
                'participant_id'     => 'required|exists:participants,id',
                // Training must exist in database
                'training_id'        => 'required|exists:trainings,id',
                // Document metadata
                'name'               => 'required|string|max:255',
                'status'             => 'nullable|string|max:50',
                'date_of_submitting' => 'nullable|date',
                // File upload requirements
                'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // 2MB max size
            ]);

            // Start database transaction
            DB::beginTransaction();

            // Get participant ID from request
            $participantId = $request->input('participant_id');

            // Verify participant exists
            $participant = Participant::findOrFail($participantId);

            // Get training ID from request
            $trainingId = $request->input('training_id');

            // Additional check for training ID (though already validated)
            if (!$trainingId) {
                return redirect()->back()->with('error', 'Training ID is required.');
            }

            // Store the uploaded file in public storage
            $filePath = $request->file('document_file')->store('documents', 'public');

            // Create document record in database
            Document::create([
                'name'               => $validated['name'],
                'status'             => $validated['status'] ?? null, // Default to null if not provided
                'date_of_submitting' => $validated['date_of_submitting'] ?? null, // Default to null if not provided
                'participant_id'     => $participant->id,
                'training_id'        => $trainingId, // Associate with training
                'file_path'          => $filePath, // Store path to uploaded file
            ]);

            // Commit the transaction
            DB::commit();

            // Return success response
            return back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Return error response
            return redirect()->back()->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }

    /**
     * Display the participant edit form
     * 
     * Retrieves a participant's details along with their related remarks, 
     * sureties, and training information for editing.
     *
     * @param int $id The ID of the participant to edit
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function participantedit($id)
    {
        try {
            // Eager load the participant with their:
            // - Remarks (comments/notes)
            // - Sureties (guarantors)
            // - Associated training program
            $participant = Participant::with(['remarks', 'sureties', 'training'])->findOrFail($id);

            // Return the create/edit view with the participant data
            // Uses same view for both create and edit operations
            return view('SuperAdmin.participant.create', compact('participant'));
        } catch (\Exception $e) {
            // Handle any errors (e.g., participant not found)
            // Redirect back with error message
            return back()->with('error', 'Error loading edit page: ' . $e->getMessage());
        }
    }
    /**
     * Update an existing participant record
     * 
     * Handles updating participant details along with their sureties and remarks.
     * Validates input data before processing updates.
     * 
     * @param Request $request HTTP request containing update data
     * @param int $id ID of the participant to update
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateparticipant(Request $request, $id)
    {
        try {
            // Validate all incoming request data
            $request->validate([
                // Basic participant information
                'name' => 'required|string|max:255',
                'epf_number' => 'required|string|max:50',
                'designation' => 'required|string|max:255',
                'salary_scale' => 'required|string|max:2',

                // Optional participant details
                'location' => 'nullable|string|max:255',
                'obligatory_period' => 'nullable|date',
                'cost_per_head' => 'nullable|numeric|min:0|max:9999999999',
                'bond_completion_date' => 'nullable|date',
                'bond_value' => 'nullable|numeric|min:0|max:9999999999',
                'date_of_signing' => 'nullable|date',
                'age_as_at_commencement_date' => 'nullable|numeric',
                'date_of_appointment' => 'nullable|date',
                'date_of_appointment_to_the_present_post' => 'nullable|date',
                'date_of_birth' => 'nullable|date',

                // Organizational relationships
                'division_id' => 'nullable|exists:divisions,id',
                'section_id' => 'nullable|exists:sections,id',
                'training_id' => 'required|exists:trainings,id',

                // Surety information (maximum 2 sureties)
                'sureties' => 'nullable|array|max:2',
                'sureties.*.suretyname' => 'nullable|string|max:255',
                'sureties.*.nic' => 'nullable|string|max:12',
                'sureties.*.mobile' => 'nullable|string|max:15',
                'sureties.*.address' => 'nullable|string|max:255',
                'sureties.*.salary_scale' => 'nullable|numeric|max:999999999',
                'sureties.*.suretydesignation' => 'nullable|string|max:255',
                'sureties.*.epf_number' => 'nullable|string|max:50',

                // Remarks/notes
                'remarks' => 'nullable|array',
                'remarks.*' => 'nullable|string|max:500',
            ]);

            // Retrieve the existing participant record
            $participant = Participant::findOrFail($id);

            // Update core participant information (excluding relationships)
            $participant->update($request->except(['remarks', 'sureties']));

            // Handle remarks update - first delete existing then create new ones
            if ($request->has('remarks')) {
                $participant->remarks()->delete(); // Clear existing remarks
                foreach ($request->remarks as $remarkText) {
                    if (!empty($remarkText)) {
                        $participant->remarks()->create([
                            'remark' => $remarkText,
                            'training_id' => $participant->training_id
                        ]);
                    }
                }
            }

            // Handle sureties update - update existing or create new ones
            if ($request->has('sureties')) {
                foreach ($request->sureties as $index => $suretyData) {
                    if (isset($participant->sureties[$index])) {
                        // Update existing surety record
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
                        // Create new surety record
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

            // Redirect back to participant details with success message
            return redirect()
                ->route('SuperAdmin.participant.Detail', ['id' => $participant->training_id])
                ->with('success', 'Participant updated successfully.');
        } catch (\Exception $e) {
            // Handle any errors during the update process
            return back()
                ->with('error', 'Error updating participant: ' . $e->getMessage());
        }
    }
    /**
     * Export participant column structure as an Excel file
     * 
     * Generates a template Excel file with column headers for participant data.
     * Used to provide a template for data import or manual entry.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportParticipantColumns()
    {
        try {
            // Generate and download an Excel file containing:
            // - Column headers for participant data
            // - No actual participant data (just structure)
            return Excel::download(new ParticipantExport, 'participant_columns.xlsx');
        } catch (\Exception $e) {
            // Handle potential errors during export generation:
            // - File permission issues
            // - Memory limits
            // - Excel package errors
            return redirect()
                ->back()
                ->with('error', 'An error occurred while exporting: ' . $e->getMessage());
        }
    }

    /**
     * Import participants from an Excel/CSV file
     * 
     * Handles the upload and processing of participant data from spreadsheet files.
     * Associates imported participants with the specified training program.
     *
     * @param Request $request HTTP request containing:
     *              - file (required Excel/CSV file)
     *              - training_id (required valid training ID)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importParticipants(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            // Must be either Excel or CSV file
            'file' => 'required|mimes:xlsx,csv',
            // Training must exist in database
            'training_id' => 'required|exists:trainings,id'
        ]);

        try {
            // Extract the training ID from the request
            $trainingId = $request->training_id;

            // Process the import using Laravel Excel package
            // Passes the training ID to the import class for participant association
            Excel::import(new ParticipantImport($trainingId), $request->file('file'));

            // Return success response with confirmation message
            return redirect()
                ->back()
                ->with('success', 'Participants imported successfully!');
        } catch (\Exception $e) {
            // Handle potential import errors:
            // - Invalid file structure
            // - Data validation failures
            // - Database errors
            return redirect()
                ->back()
                ->with('error', 'Error importing participants: ' . $e->getMessage());
        }
    }

    /**
     * Delete a participant and all related data
     * 
     * Handles the complete removal of a participant record along with all
     * associated data including remarks, sureties, and documents.
     * Uses proper error handling and cascading deletes.
     *
     * @param int $id ID of the participant to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyparticipant($id)
    {
        try {
            // Find the participant record or fail
            $participant = Participant::findOrFail($id);

            // Delete all related data first to maintain referential integrity:

            // Remove all associated remarks/comments
            $participant->remarks()->delete();

            // Remove all associated sureties/guarantors
            $participant->sureties()->delete();

            // Remove all associated documents/files
            $participant->documents()->delete();

            // Finally delete the participant record itself
            $participant->delete();

            // Redirect back to participant list for the training
            // Include the training ID in the redirect route
            return redirect()
                ->route('SuperAdmin.participant.Detail', ['id' => $participant->training_id])
                ->with('success', 'Participant deleted successfully.');
        } catch (\Exception $e) {
            // Handle any errors that occur during deletion:
            // - Database constraints
            // - Missing records
            // - Permission issues
            return back()
                ->with('error', 'Error deleting participant: ' . $e->getMessage());
        }
    }
    //End Participant handling
}
