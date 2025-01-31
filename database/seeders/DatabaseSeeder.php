<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders
        $this->call([
            DivisionSeeder::class,
            SectionSeeder::class,
            SuperAdminSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CountryTableSeeder::class,
        ]);
    }
}
