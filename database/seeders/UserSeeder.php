<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'CATC Wing 1 User',
            'username' => 'catcwing1user',
            'password' => bcrypt('password'),
            'email' => 'catcwing1user@example.com',
            'role' => 'user',
            'division_id' => 2,
            'section_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'CATC Wing 2 User',
            'username' => 'catcwing2user',
            'password' => bcrypt('password'),
            'email' => 'catcwing2user@example.com',
            'role' => 'user',
            'division_id' => 2,
            'section_id' => 2,
        ]);
        User::factory()->create([
            'name' => 'CATC Wing 3 User',
            'username' => 'catcwing3user',
            'password' => bcrypt('password'),
            'email' => 'catcwing3user@example.com',
            'role' => 'user',
            'division_id' => 2,
            'section_id' => 3,
        ]);

        User::factory()->create([
            'name' => 'CATC Wing 4 User',
            'username' => 'catcwing4user',
            'password' => bcrypt('password'),
            'email' => 'catcwing4user@example.com',
            'role' => 'user',
            'division_id' => 2,
            'section_id' => 4,
        ]);

        User::factory()->create([
            'name' => 'CATC Wing 5 User',
            'username' => 'catcwing5user',
            'password' => bcrypt('password'),
            'email' => 'catcwing5user@example.com',
            'role' => 'user',
            'division_id' => 2,
            'section_id' => 5,
        ]);

        User::factory()->create([
            'name' => 'IT User',
            'username' => 'ituser',
            'password' => bcrypt('password'),
            'email' => 'ituser@example.com',
            'role' => 'user',
            'division_id' => 3,
        ]);
        User::factory()->create([
            'name' => 'Finance User',
            'username' => 'financeuser',
            'password' => bcrypt('password'),
            'email' => 'financeuser@example.com',
            'role' => 'user',
            'division_id' => 4,
        ]);
        User::factory()->create([
            'name' => 'SCM User',
            'username' => 'scmuser',
            'password' => bcrypt('password'),
            'email' => 'scmuser@example.com',
            'role' => 'user',
            'division_id' => 5,
        ]);
        User::factory()->create([
            'name' => 'Marketing User',
            'username' => 'marketinguser',
            'password' => bcrypt('password'),
            'email' => 'marketinguser@example.com',
            'role' => 'user',
            'division_id' => 6,
        ]);

        User::factory()->create([
            'name' => 'Security User',
            'username' => 'securityuser',
            'password' => bcrypt('password'),
            'email' => 'securityuser@example.com',
            'role' => 'user',
            'division_id' => 6,
        ]);
    }
}
