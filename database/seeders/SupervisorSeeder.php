<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{
    public function run()
    {
        Supervisor::create([
            'supervisor_id' => 'SUP001',
            'username' => 'supervisor',
            'password' => Hash::make('password'),
            'name' => 'Sompot',
            'surname' => 'Boss',
            'company_id' => 1 // Assuming company with ID 1 exists or is nullable/not strict constraint yet
        ]);

        Supervisor::create([
            'supervisor_id' => 'SUP002',
            'username' => 'supervisor2',
            'password' => Hash::make('password'),
            'name' => 'Malai',
            'surname' => 'Manager',
            'company_id' => 2
        ]);
    }
}
