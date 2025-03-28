<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Helpers\LuhnHelper;

class AccountFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'account_name' => $this->faker->word() . ' Account',
            'account_number' => LuhnHelper::generateAccountNumber(),
            'account_type' => $this->faker->randomElement(['Personal', 'Business']),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'balance' => $this->faker->randomFloat(2, 100, 10000)
        ];
    }
}