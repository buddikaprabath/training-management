<?php

namespace Database\Seeders;

use App\Models\Training;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trainings = [
            [
                'training_name'         => 'Web Development Bootcamp',
                'training_code'         => 'WD101', // Ensure this code is unique
                'mode_of_delivery'      => 'Online',
                'training_period_from'  => '2025-03-01',
                'training_period_to'    => '2025-06-01',
                'total_training_hours'  => 150,
                'total_program_cost'    => 5000,
                'country'               => 'Sri Lanka',
                'training_structure'    => 'Module-based with assignments',
                'exp_date'              => '2025-06-01',
                'duration'              => '3 days',
                'batch_size'            => 25,
                'training_custodian'    => 'Institute of Technology',
                'course_type'           => 'Certificate',
                'category'              => 'Technical',
                'dead_line'             => '2025-02-28',
                'division_id'           => 1,
                'section_id'            => 1,
            ],
            [
                'training_name'         => 'Project Management Fundamentals',
                'training_code'         => 'PM202', // Ensure this code is unique
                'mode_of_delivery'      => 'In-person',
                'training_period_from'  => '2025-04-01',
                'training_period_to'    => '2025-06-30',
                'total_training_hours'  => 120,
                'total_program_cost'    => 3000,
                'country'               => 'Sri Lanka',
                'training_structure'    => 'Lectures, case studies',
                'exp_date'              => '2025-06-30',
                'duration'              => '3 month',
                'batch_size'            => 20,
                'training_custodian'    => 'Institute of Business',
                'course_type'           => 'Diploma',
                'category'              => 'Business',
                'dead_line'             => '2025-03-31',
                'division_id'           => 2,
                'section_id'            => 2,
            ],
            [
                'training_name'         => 'Digital Marketing Strategies',
                'training_code'         => 'DM303', // Ensure this code is unique
                'mode_of_delivery'      => 'Hybrid',
                'training_period_from'  => '2025-05-01',
                'training_period_to'    => '2025-08-01',
                'total_training_hours'  => 100,
                'total_program_cost'    => 4000,
                'country'               => 'Sri Lanka',
                'training_structure'    => 'Workshops, hands-on projects',
                'exp_date'              => '2025-08-01',
                'duration'              => '2 week',
                'batch_size'            => 30,
                'training_custodian'    => 'Institute of Marketing',
                'course_type'           => 'Certificate',
                'category'              => 'Marketing',
                'dead_line'             => '2025-04-30',
                'division_id'           => 3,
                'section_id'            => 3,
            ],
            [
                'training_name'         => 'Data Science and Analytics',
                'training_code'         => 'DS404', // Ensure this code is unique
                'mode_of_delivery'      => 'Online',
                'training_period_from'  => '2025-06-01',
                'training_period_to'    => '2025-09-01',
                'total_training_hours'  => 180,
                'total_program_cost'    => 6000,
                'country'               => 'Sri Lanka',
                'training_structure'    => 'Lectures, projects, quizzes',
                'exp_date'              => '2025-09-01',
                'duration'              => '10 days',
                'batch_size'            => 20,
                'training_custodian'    => 'Institute of Science',
                'course_type'           => 'Advanced Diploma',
                'category'              => 'Science',
                'dead_line'             => '2025-05-31',
                'division_id'           => 4,
                'section_id'            => 4,
            ],
        ];

        foreach ($trainings as $training) {
            Training::create($training);
        }
    }
}
