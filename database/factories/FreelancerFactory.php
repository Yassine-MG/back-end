<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Freelancer;
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
        $categories = ['Developer', 'Data', 'Writing & Translation', 'Business', 'Video & Animation', 'Designer'];
        $user = User::where('role', 'freelancer')
        ->whereDoesntHave('freelancer')
        ->inRandomOrder()
        ->first();

        if (!$user) {
            throw new \Exception('No eligible users found to create a freelancer.');
        }

        $existingFreelancer = Freelancer::where('user_id', $user->id)->exists();
        if ($existingFreelancer) {
            throw new \Exception('User already has a Freelancer associated.');
        }

        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'displayed_name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'cv' => $this->faker->url(),
            'occupation' => $this->faker->name(),
            'category' => $this->faker->randomElement($categories),
            'certification' => $this->faker->name(),
            'photo' => $this->faker->imageUrl(),
            'education' => $this->faker->name(),
            'user_id' => $user->id,
        ];
    }
}

