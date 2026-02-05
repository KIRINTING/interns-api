<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // factory(User::class, 10)->create();

        factory(User::class)->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            OfficerSeeder::class,
            StudentSeeder::class,
            MentorSeeder::class,
            SupervisorSeeder::class,
        ]);
    }
}
