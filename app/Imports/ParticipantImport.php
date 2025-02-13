<?php

namespace App\Imports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row to a Participant model and save it.
     */
    public function model(array $row)
    {
        return new Participant([
            'name'                                      => $row['name'],
            'epf_number'                                => $row['epf_number'],
            'designation'                               => $row['address'],
            'salary_scale'                              => $row['mobile'],
            'location'                                  => $row['nic'],
            'obligatory_period'                         => $row['salary_scale'],
            'cost_per_head'                             => $row['designation'],
            'bond_completion_date'                      => $row['division_id'],
            'bond_value'                                => $row['training_id'],
            'date_of_signing'                           => $row['date_of_birth'],
            'age_as_at_commencement_date'               => $row['date_of_appointment'],
            'date_of_appointment'                       => $row['remarks'],
            'date_of_appointment_to_the_present_post'   => $row['name'],
            'date_of_birth'                             => $row['name'],
            'division_id'                               => $row['name'],
            'section_id'                                => $row['name'],
            'name'                                      => $row['name'],
            'epf_number'                                => $row['name'],
            'nic'                                       => $row['name'],
            'mobile'                                    => $row['name'],
            'address'                                   => $row['name'],
            'salary_scale'                              => $row['name'],
            'designation'                               => $row['name'],
        ]);
    }
}
