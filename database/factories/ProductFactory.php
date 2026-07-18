<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name_en' => fake()->words(3, true),
            'name_bn' => fake('bn_BD')->words(3, true),
            'description_en' => fake()->paragraph(),
            'description_bn' => fake('bn_BD')->paragraph(),
            'price' => fake()->randomFloat(2, 50, 500),
            'compare_at_price' => fake()->optional()->randomFloat(2, 500, 700),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'stock' => fake()->numberBetween(0, 100),
            'primary_image' => null,
            'gallery_images' => null,
            'is_active' => true,
            'is_featured' => fake()->boolean(30),
            'order' => fake()->numberBetween(0, 100),
        ];
    }
}
