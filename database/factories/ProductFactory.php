<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'category' => fake()->randomElement(['Bombas', 'Accesorios']),
            'description' => fake()->sentence(),
            'image_path' => null,
            'is_published' => true,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
