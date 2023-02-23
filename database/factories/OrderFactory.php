<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => $this->faker->numberBetween(1, 20000),
            'price' => $this->faker->numberBetween(100, 1000),
            'user_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
