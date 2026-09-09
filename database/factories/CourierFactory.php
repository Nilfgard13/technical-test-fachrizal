<?php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Courier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->unique()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'level' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement([
                'active',
                'inactive',
            ]),
            'joined_at' => fake()->date(),
        ];
    }
}
