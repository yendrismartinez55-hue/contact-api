
<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->imageUrl(),
        ];
    }
}
