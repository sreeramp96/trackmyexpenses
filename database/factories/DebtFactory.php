<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 10000);
        return [
            'user_id' => User::factory(),
            'contact_name' => $this->faker->name(),
            'direction' => $this->faker->randomElement(['lent', 'borrowed']),
            'amount' => $amount,
            'remaining_amount' => $this->faker->randomFloat(2, 0, $amount),
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'note' => $this->faker->sentence(),
            'is_settled' => false,
        ];
    }
}
