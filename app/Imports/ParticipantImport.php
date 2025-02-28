<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Remark;
use App\Models\Surety;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ParticipantImport implements ToModel, WithHeadingRow
{
    protected $trainingId;

    // Constructor to accept training_id
    public function __construct($trainingId)
    {
        $this->trainingId = $trainingId;
    }

    /**
     * Map each row to a Participant model and save it.
     */
    public function model(array $row)
    {
        // Check if participant EPF number already exists for the same training_id
        $existingParticipant = Participant::where('epf_number', $row['epf_number'])
            ->where('training_id', $this->trainingId)
            ->first();

        if ($existingParticipant) {
            // If the EPF number exists for the same training, skip this row
            return null;
        }

        // Division name to ID mapping
        $divisionMapping = [
            'HR' => 1,
            'CATC' => 2,
            'IT' => 3,
            'FINANCE' => 4,
            'SCM' => 5,
            'MARKETING' => 6,
            'SECURITY' => 7
        ];

        // Section name to ID mapping
        $sectionMapping = [
            'WING 1' => 1,
            'WING 2' => 2,
            'WING 3' => 3,
            'WING 4' => 4,
            'WING 5' => 5,
            'WING 6' => 6,
            'WING 7' => 7,
            'WING 8' => 8
        ];

        // Convert date strings to proper Carbon date format
        $bondCompletionDate = Carbon::createFromFormat('d.m.Y', $row['bond_completion_date'])->toDateString();
        $dateOfSigning = Carbon::createFromFormat('d.m.Y', $row['date_of_signing'])->toDateString();
        $dateOfAppointment = Carbon::createFromFormat('d.m.Y', $row['date_of_appointment'])->toDateString();
        $dateOfAppointmentToPresentPost = Carbon::createFromFormat('d.m.Y', $row['date_of_appointment_to_the_present_post'])->toDateString();
        $dateOfBirth = Carbon::createFromFormat('d.m.Y', $row['date_of_birth'])->toDateString();


        // Create the Participant model first
        $participant = new Participant([
            'name'                                      => $row['name'],
            'epf_number'                                => $row['epf_number'],
            'designation'                               => $row['designation'],
            'salary_scale'                              => $row['salary_scale'],
            'location'                                  => $row['location'],
            'obligatory_period'                         => $row['obligatory_period'],
            'cost_per_head'                             => $row['cost_per_head'],
            'bond_completion_date'                      => $bondCompletionDate,
            'bond_value'                                => $row['bond_value'],
            'date_of_signing'                           => $dateOfSigning,
            'age_as_at_commencement_date'               => $row['age_as_at_commencement_date'],
            'date_of_appointment'                       => $dateOfAppointment,
            'date_of_appointment_to_the_present_post'   => $dateOfAppointmentToPresentPost,
            'date_of_birth'                             => $dateOfBirth,
            'division_id'                               => $divisionMapping[$row['division_name']],
            'section_id'                                => isset($row['section_name']) && isset($sectionMapping[$row['section_name']])
                ? $sectionMapping[$row['section_name']]
                : null,
            'training_id'                               => $this->trainingId // Use the passed training_id here
        ]);

        $participant->save();

        // Create Surety 1 model
        Surety::create([
            'name' => $row['surety_1_name'],
            'epf_number' => $row['surety_1_epf_number'],
            'nic' => $row['surety_1_nic'],
            'mobile' => $row['surety_1_mobile'],
            'address' => $row['surety_1_address'],
            'salary_scale' => $row['surety_1_salary_scale'],
            'designation' => $row['surety_1_designation'],
            'participant_id' => $participant->id,
        ]);

        // Create Surety 2 model
        Surety::create([
            'name' => $row['surety_2_name'],
            'epf_number' => $row['surety_2_epf_number'],
            'nic' => $row['surety_2_nic'],
            'mobile' => $row['surety_2_mobile'],
            'address' => $row['surety_2_address'],
            'salary_scale' => $row['surety_2_salary_scale'],
            'designation' => $row['surety_2_suretydesignation'],
            'participant_id' => $participant->id,
        ]);

        // Create Remark entry for 'other_comments'
        Remark::create([
            'remark' => $row['other_comments'],
            'participant_id' => $participant->id,
            'training_id' => $this->trainingId, // Use the participant's training_id
        ]);

        return $participant;
    }
}
