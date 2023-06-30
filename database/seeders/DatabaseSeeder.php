<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Service;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(50)->create();
        User::where('role', 'freelancer')->get()->each(function (User $user) {
            // Check if the user already has a freelancer account
            if (!$user->freelancer) {
                Freelancer::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        });       
         \App\Models\Service::factory(200)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
