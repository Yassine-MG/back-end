<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Freelancer>
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'displayed_name' => fake()->name(),
            'description' => fake()->text(),
            'cv' => fake()->Url(),
            'occupation' => fake()->name(),
            'skills' => fake()->name(),
            'certification' => fake()->name(),
            'photo' => fake()->imageUrl(),
            'education' => fake()->name(),
            'user_id' => $this->faker->randomElement(User::pluck('id')),        
        ];
    }
}
