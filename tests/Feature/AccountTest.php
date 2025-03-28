<?php

namespace Tests\Feature;

use App\Helpers\LuhnHelper;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_account_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/accounts', [
            'account_name' => 'Test Account',
            'account_type' => 'Personal',
            'currency' => 'USD',
            'balance' => 1000,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'account' => ['id', 'user_id', 'account_name', 'account_type', 'currency', 'balance']
            ]);

        $this->assertDatabaseHas('accounts', ['account_name' => 'Test Account']);
    }
}