<?php

namespace App\Http\Controllers\Admin\HRAdmin;

use App\Models\Grade;
use App\Models\Remark;
use App\Models\Surety;
use App\Models\Approval;
use App\Models\Document;
use App\Models\Training;
use App\Models\Participant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Illuminate\Validation\Rule;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HRAdminParticipantController extends Controller
{
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
            return view('Admin.HRAdmin.participant.Detail', [
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

    //load the create participant blade
    public function createparticipant($trainingId)
    {
        try {
            // Attempt to find the training
            $training = Training::findOrFail($trainingId); // This will automatically throw a ModelNotFoundException if not found

            // Return the view with the training data
            return view('Admin.HRAdmin.participant.create', compact('training'));
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
            'salary_scale'                 => 'nullable|numeric|min:0|max:9999999999',
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
            'division_id'                  => 'nullable|exists:divisions,id',
            'section_id'                   => 'nullable|exists:sections,id',
            'training_id'                  => 'required|exists:trainings,id',

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

            return redirect()->route('Admin.HRAdmin.training.Detail')->with('success', 'Participant added successfully! You can now upload documents.');
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

            return view('Admin.HRAdmin.participant.create', compact('participant'));
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
                'division_id'                  => 'nullable|exists:divisions,id',
                'section_id'                   => 'nullable|exists:sections,id',
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
                // Add Sureties and Remarks to updated data
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
                return redirect()->route('Admin.HRAdmin.participant.Detail', ['id' => $participant->training_id])
                    ->with('error', 'An update request for this participant is already pending approval.');
            }

            // Create an approval request for the update
            Approval::create([
                'user_id'    => Auth::id(),
                'model_type' => Participant::class,
                'model_id'   => (string) $participant->id,
                'action'     => 'update',
                'new_data'   => json_encode($updatedData), // Store the updated data, including sureties and remarks
                'status'     => 'pending',
            ]);

            DB::commit();
            return redirect()->route('Admin.HRAdmin.participant.Detail', ['id' => $participant->training_id])
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
}
