<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_transaction_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an account for the user
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000, // Initial balance
        ]);

        // Transaction data
        $transactionData = [
            'account_id' => $account->id,
            'amount' => 200,
            'type' => 'Credit', // or 'Debit'
            'description' => 'completed',
        ];

        // Send API request to create a transaction
        $response = $this->actingAs($user)->postJson('/api/transactions', $transactionData);

        // Check the response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'transaction' => ['id', 'account_id', 'amount', 'type', 'description']
            ]);

        // Ensure transaction is in the database
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => 200,
            'type' => 'Credit',
            'description' => 'completed',
        ]);
    }

    /** @test */
    public function it_fails_transaction_due_to_insufficient_balance()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an account with a low balance
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 50, // Low balance
        ]);

        // Try to withdraw more than available balance
        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'account_id' => $account->id,
            'amount' => 100, // More than available balance
            'type' => 'Debit',
            'description' => 'failed',
        ]);

        // Expect a validation error or failed transaction message
        $response->assertStatus(400) // Or another appropriate status code
            ->assertJson([
                'error' => 'Insufficient funds',
            ]);

        // Ensure transaction is NOT in the database
        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'amount' => 100,
            'type' => 'Debit',
            'description' => 'completed',
        ]);
    }

    /** @test */
    public function it_processes_a_deposit_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an account for the user
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 500,
        ]);

        // Perform a deposit
        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'account_id' => $account->id,
            'amount' => 300,
            'type' => 'Credit',
            'description' => 'completed',
        ]);

        // Ensure the transaction was successful
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'transaction' => ['id', 'account_id', 'amount', 'type', 'description']
            ]);

        // Verify new balance
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 800, // Previous balance (500) + Deposit (300)
        ]);
    }
}
