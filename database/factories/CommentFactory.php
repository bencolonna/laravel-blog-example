<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'comment' => $this->faker->paragraph(),
            'post_id' => \App\Models\Post::factory(),
            'user_id' => \App\Models\User::factory(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
