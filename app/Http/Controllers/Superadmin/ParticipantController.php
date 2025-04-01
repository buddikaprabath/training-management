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
    
    //participant handling
    public function participantview($trainingId)
    {
        try {
            // Retrieve training with related data
            $training = Training::with(['remarks', 'institutes', 'documents'])
                ->find($trainingId);

            if (!$training) {
                return redirect()->back()->with('error', 'Training not found.');
            }

            // Retrieve participants with pagination and their remarks
            $participants = $training->participants()->with('remarks')->paginate(10);

            // get the subject relationship
            $Subjects = $training->subjects;

            // Return the view with the data
            return view('SuperAdmin.participant.Detail', [
                'training' => $training,
                'participants' => $participants,
                'institutes' => $training->institutes,
                'documents' => $training->documents,
                'subjects' => $Subjects, // Pass all subjects to the view
            ]);
        } catch (\Exception $e) {
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

    protected EmpApiService $empApiService;

    public function __construct(EmpApiService $empApiService)
    {
        $this->empApiService = $empApiService;
    }
    //load the create participant blade
    public function createparticipant(Request $request, $trainingId)
    {
        try {
            $training = Training::findOrFail($trainingId);

            // If no EPF number submitted, return basic form
            if (!$request->filled('epf_number')) {
                return view('SuperAdmin.participant.create', compact('training'));
            }

            // Validate EPF number when present
            $validated = $request->validate([
                'epf_number' => 'required|string|max:20',
            ]);

            //get division name
            $employee = $this->empApiService->getEmployeeDetailsForParticipant($validated['epf_number']);

            if (!$employee) {
                return back()->withInput()->with('error', 'Employee not found');
            }
            return view('SuperAdmin.participant.create', [
                'training' => $training,
                'employee' => $employee,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
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
            'salary_scale'                 => 'required|string|max:2',
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
    public function storeParticipantDocument(Request $request)
    {
        try {
            $validated = $request->validate([
                'participant_id'     => 'required|exists:participants,id',
                'training_id'        => 'required|exists:trainings,id', // Ensure training_id is passed
                // Validate the document file
                'name'               => 'required|string|max:255',
                'status'             => 'nullable|string|max:50',
                'date_of_submitting' => 'nullable|date',
                'document_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Max 2MB
            ]);

            DB::beginTransaction();

            // Ensure the participant ID is passed correctly
            $participantId = $request->input('participant_id');

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
            return back()->with('success', 'Document uploaded successfully!');
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
                'salary_scale'                 => 'required|string|max:2',
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

            return redirect()->route('SuperAdmin.participant.Detail', ['id' => $participant->training_id])->with('success', 'Participant deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting participant: ' . $e->getMessage());
        }
    }


    //End Participant handling
}
