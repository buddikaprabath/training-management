<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sections
        $section = [
            'wing-1',
            'wing-2',
            'wing-3',
            'wing-4',
            'wing-5',
            'wing-6',
            'wing-7',
            'wing-8',
        ];

        foreach ($section as $sections) {
            Section::factory()->create([
                'section_name' => $sections,
                'division_id' => 2,
            ]);
        }
    }
}
