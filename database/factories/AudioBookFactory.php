<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AudioBook;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AudioBookFactory extends Factory
{
    protected $model = AudioBook::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'path' => $this->faker->filePath(),
            'title' => $this->faker->words(3, true),
            'subtitle' => $this->faker->words(3, true),
            'volume' => $this->faker->randomFloat(null, 1, 12),
            'description' => $this->faker->text(),
            'rating' => $this->faker->randomFloat(null, 0, 5),
            'cover' => null,
            'language' => 'en-GB',
            'published_at' => Carbon::now(),
        ];
    }
}
