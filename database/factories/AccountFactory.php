<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company().' Account',
            'type' => $this->faker->randomElement(['bank', 'cash', 'credit_card', 'wallet', 'loan']),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'currency' => 'INR',
            'is_active' => true,
        ];
    }
}
