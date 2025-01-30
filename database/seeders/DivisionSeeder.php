<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create divisions
        $divisions = [
            'HR',
            'CATC',
            'IT',
            'Finance',
            'SCM',
            'Marketing',
            'security',
        ];

        foreach ($divisions as $division) {
            Division::factory()->create([
                'division_name' => $division,
            ]);
        }
    }
}
