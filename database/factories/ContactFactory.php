<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->numerify('3#########'),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => null,
        ];
    }
}
