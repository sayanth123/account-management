<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;

class TransactionFactory extends Factory
{
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'type' => $this->faker->randomElement(['Credit', 'Debit']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence()
        ];
    }
}