<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'HR Admin',
            'username' => 'hradmin',
            'password' => bcrypt('password'),
            'email' => 'hradmin@example.com',
            'role' => 'hradmin',
            'division_id' => 1,
        ]);
        // Create CATC Admin
        User::factory()->create([
            'name' => 'CATC Admin',
            'username' => 'catcadmin',
            'password' => bcrypt('password'),
            'email' => 'catcadmin@example.com',
            'role' => 'catcadmin',
            'division_id' => 2,
        ]);
    }
}
