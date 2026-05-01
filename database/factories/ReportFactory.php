<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $statuses = Report::statuses();
        $categories = Report::categories();

        return [
            'user_id' => User::factory(),
            'category' => fake()->randomElement($categories),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'postcode' => 'SW1A 1AA',
            'address' => fake()->streetAddress(),
            'latitude' => 51.501364,
            'longitude' => -0.141890,
            'status' => fake()->randomElement($statuses),
            'additional_notes' => fake()->optional()->sentence(),
        ];
    }
}
