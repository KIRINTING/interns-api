<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mentor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MentorSeeder extends Seeder
{
    public function run()
    {
        // Clear old data to prevent duplicates if desired, or just use create
        // DB::table('mentors')->truncate(); 

        Mentor::create([
            'mentor_id' => 'MEN001',
            'username' => 'mentor',
            'password' => Hash::make('password'),
            'name' => 'Wichai',
            'surname' => 'Kru',
            'department' => 'Computer Science'
        ]);

        Mentor::create([
            'mentor_id' => 'MEN002',
            'username' => 'mentor2',
            'password' => Hash::make('password'),
            'name' => 'Suda',
            'surname' => 'Ajarn',
            'department' => 'Information Technology'
        ]);
    }
}
