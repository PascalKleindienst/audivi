<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    protected $model = Track::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'position' => $this->faker->numberBetween(1, 10),
            'path' => $this->faker->filePath(),
            'start' => $this->faker->randomFloat(),
            'end' => $this->faker->randomFloat(),
        ];
    }
}
