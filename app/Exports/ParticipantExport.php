<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\Auth;

class ParticipantExport implements FromArray
{
    /**
     * Return the array of column names to export.
     *
     * @return array
     */
    public function array(): array
    {
        // Get the logged-in user's role
        $user = Auth::user();
        $role = $user->role; // Assuming the role is stored in the 'role' field in the 'users' table

        // Base columns that will be exported for all users
        $columns = [
            'name',
            'epf_number',
            'designation',
            'salary_scale',
            'location',
            'obligatory_period',
            'cost_per_head',
            'bond_completion_date',
            'bond_value',
            'date_of_signing',
            'age_as_at_commencement_date',
            'date_of_appointment',
            'date_of_appointment_to_the_present_post',
            'date_of_birth',
            'division_name',
            'section_name',
            'surety_1_name',
            'surety_1_nic',
            'surety_1_mobile',
            'surety_1_address',
            'surety_1_salary_scale',
            'surety_1_designation',
            'surety_1_epf_number',
            'surety_2_name',
            'surety_2_nic',
            'surety_2_mobile',
            'surety_2_address',
            'surety_2_salary_scale',
            'surety_2_suretydesignation',
            'surety_2_epf_number',
            'other_comments',
        ];

        // Conditionally add/remove columns based on the user's role
        if ($role == 'catcadmin') {
            // Remove 'division_name' and 'section_name' for CATC Admin
            $columns = array_filter($columns, function ($column) {
                return !in_array($column, ['division_name']);
            });
        } elseif ($role == 'user') {
            // Remove 'division_name' and 'section_name' for User
            $columns = array_filter($columns, function ($column) {
                return !in_array($column, ['division_name', 'section_name']);
            });
        }

        // If the user is Super Admin or HR Admin, export as is (no changes to columns)
        return [$columns];
    }
}
