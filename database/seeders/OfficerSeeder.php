<?php

namespace Database\Seeders;

use App\Models\Officer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Officer 1
        Officer::create([
            'officer_id' => 'OFF001',
            'username' => 'admin_one',
            'password' => Hash::make('password123'),
            'name' => 'Somchai',
            'surname' => 'Jaidee',
        ]);

        // Officer 2
        Officer::create([
            'officer_id' => 'OFF002',
            'username' => 'officer_two',
            'password' => Hash::make('password123'),
            'name' => 'Somsri',
            'surname' => 'Rakngan',
        ]);

        // Officer 3
        Officer::create([
            'officer_id' => 'OFF003',
            'username' => 'officer_three',
            'password' => Hash::make('password123'),
            'name' => 'Mana',
            'surname' => 'Meepart',
        ]);
    }
}
