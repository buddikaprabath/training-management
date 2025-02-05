<?php

namespace Database\Seeders;

use App\Models\Institute;
use Illuminate\Database\Seeder;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $institutes = [
            [
                'name' => 'Institute of Technology',
                'type' => 'Technical',
            ],
            [
                'name' => 'Institute of Business',
                'type' => 'Business',
            ],
            [
                'name' => 'Institute of Arts',
                'type' => 'Arts',
            ],
            [
                'name' => 'Institute of Science',
                'type' => 'Science',
            ],
            [
                'name' => 'Institute of Design',
                'type' => 'Design',
            ],
        ];

        foreach ($institutes as $institute) {
            Institute::create($institute);
        }
    }
}
