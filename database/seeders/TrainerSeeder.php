<?php

namespace Database\Seeders;

use App\Models\Trainer;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert dummy data
        Trainer::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'mobile' => '0771234567',
            'institute_id' => 1, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'mobile' => '0777654321',
            'institute_id' => 2, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Mark Taylor',
            'email' => 'mark.taylor@example.com',
            'mobile' => '0779876543',
            'institute_id' => 3, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Emily Johnson',
            'email' => 'emily.johnson@example.com',
            'mobile' => '0781234567',
            'institute_id' => 4, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Michael Brown',
            'email' => 'michael.brown@example.com',
            'mobile' => '0787654321',
            'institute_id' => 5, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Linda White',
            'email' => 'linda.white@example.com',
            'mobile' => '0791234567',
            'institute_id' => 1, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'David Green',
            'email' => 'david.green@example.com',
            'mobile' => '0797654321',
            'institute_id' => 2, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Sophia Clark',
            'email' => 'sophia.clark@example.com',
            'mobile' => '0801234567',
            'institute_id' => 3, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Chris Lee',
            'email' => 'chris.lee@example.com',
            'mobile' => '0807654321',
            'institute_id' => 4, // Replace with actual institute_id
        ]);

        Trainer::create([
            'name' => 'Olivia Harris',
            'email' => 'olivia.harris@example.com',
            'mobile' => '0811234567',
            'institute_id' => 5, // Replace with actual institute_id
        ]);
    }
}
