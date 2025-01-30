<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Superadmin',
            'username' => 'superadmin',
            'password' => bcrypt('password'),
            'email' => 'superadmin@example.com',
            'role' => 'superadmin',
            'division_id' => 1,
        ]);
    }
}
