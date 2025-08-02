<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        $trartDate = fake()->dateTimeThisMonth();
        return [
            'start_date' => $trartDate,
            'end_date' => Carbon::createfromDate($trartDate)->addDays(fake()->numberBetween(1, 5)),
            'status' => fake()->randomElement(['waiting', 'approved', 'canceled']),
        ];
    }
}
