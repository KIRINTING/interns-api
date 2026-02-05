<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Student 1
        Student::create([
            'student_id' => '64123456789', // Username for login
            'national_id' => '1234567890123', // Password for login
            'student_code' => '64123456789',
            'name' => 'Somchai',
            'surname' => 'Rakrian',
            'major' => 'Computer Science',
            'group' => 'A',
            'status' => 'active',
            'mentor_id' => 'MEN001',
        ]);

        // Sample Student 2
        Student::create([
            'student_id' => '64987654321',
            'national_id' => '9876543210987',
            'student_code' => '64987654321',
            'name' => 'Somsri',
            'surname' => 'Deejai',
            'major' => 'Information Technology',
            'group' => 'B',
            'status' => 'active',
        ]);
    }
}
