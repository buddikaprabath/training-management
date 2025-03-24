<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainingCode; // Import the TrainingCode model

class CourseCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of training codes
        $trainingCodes = [
            'ACOM',
            'AEIS',
            'ATRC',
            'ARMT',
            'AROP',
            'ARC',
            'AUQA',
            'AVSC',
            'CATR',
            'CVE',
            'OMMT',
            'IENG',
            'ETO',
            'FNMT',
            'FNRS',
            'GEMT',
            'HRMT',
            'INTE',
            'INST',
            'IND',
            'LDN',
            'LEG',
            'MRKT',
            'MEE',
            'MED',
            'SISP',
            'SCMT',
            'ACC',
            'ANC',
            'CRF',
            'CRM',
            'DSN',
            'DRS',
            'ELI',
            'EXD',
            'HSF',
            'HSM',
            'ICT',
            'INR',
            'LAN',
            'MDA',
            'MST',
            'OFM',
            'PRM',
            'QLM',
            'SEC',
            'SFT',
            'SPE',
            'SUS',
            'UCF'

        ];

        // Insert the training codes into the database
        foreach ($trainingCodes as $code) {
            TrainingCode::create([
                'training_codes' => $code,
            ]);
        }
    }
}
