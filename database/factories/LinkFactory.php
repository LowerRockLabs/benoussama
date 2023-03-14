<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site' => $this->faker->domainName,
            'price' => $this->faker->numberBetween(100, 1000),
            'as' => $this->faker->numberBetween(100, 1000),
            'traffic' => $this->faker->numberBetween(100, 1000),
            'cuntry' => $this->faker->country,
            'country_id' => Country::inRandomOrder()->select('id')->first()->id,
            'industry' => $this->faker->jobTitle,
        ];
    }
}
