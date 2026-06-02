<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $framePrice = fake()->numberBetween(200000, 1000000);
        $lensPrice = fake()->numberBetween(200000, 1500000);

        return [
            'invoice_number' => 'INV-' . fake()->unique()->numerify('####-###'),
            'receive_from' => fake()->name(),
            'patient_name' => fake()->name(),
            'optometrist_name' => 'Dr. ' . fake()->lastName(),
            'pay_for' => fake()->randomElement(['Kacamata', 'Lensa', 'Frame', 'Sunglasses']),
            'frame_type' => fake()->randomElement(['Titanium', 'Plastik', 'Stainless', 'Alloy']),
            'frame_price' => $framePrice,
            'lens_type' => fake()->randomElement(['Single Vision', 'Progressive', 'Bifocal', 'Anti Radiasi']),
            'lens_price' => $lensPrice,
            'total_price' => $framePrice + $lensPrice,
            'amount_in_words' => fake()->sentence(6),
            'date' => now()->format('Y-m-d'),
        ];
    }
}
