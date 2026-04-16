<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'سيارات',
            'عقارات',
            'وظائف',
            'إلكترونيات',
            'أثاث منزلي',
            'خدمات',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'icon' => fake()->randomElement(['car', 'building', 'briefcase', 'cpu', 'sofa', 'wrench']),
            'description' => fake()->sentence(8),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
