<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->numerify('3#########'),
            'address' => fake()->address(),
            'email' => fake()->safeEmail(),
            'avatar' => null,
            'user_id' => User::factory(),
        ];
    }
}