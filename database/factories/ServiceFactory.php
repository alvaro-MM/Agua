<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'excerpt' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'icon' => fake()->randomElement(array_keys(Service::ICONS)),
            'features' => fake()->sentences(3),
            'is_published' => true,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
