<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad_id' => Ad::factory(),
            'path' => 'ads/sample-'.fake()->numberBetween(1, 5).'.jpg',
            'alt_text' => fake()->sentence(3),
            'sort_order' => fake()->numberBetween(0, 5),
            'is_primary' => false,
        ];
    }
}
