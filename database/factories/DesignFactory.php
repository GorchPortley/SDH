<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Design>
 */
class DesignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 30),
            'name' => $this->faker->company(),
            'tag' => $this->faker->bs,
            'active' =>$this->faker->boolean(),
            'category' => $this->faker->randomElement(
                ['Subwoofer', 'Full-Range', 'Two-Way', 'Three-Way','Four-Way+','Portable', 'Esoteric', 'System']),
            'price' => $this->faker->randomFloat(2, 0.01, 1000),
            'build_cost' => $this->faker->randomFloat(2, 0.01, 1000),
            'impedance' => $this->faker->randomElement(['2','4','8']),
            'power' => $this->faker->randomNumber(),
            'summary' => $this->faker->paragraph(),
            'description' => $this->faker->paragraph()

        ];
    }
}
