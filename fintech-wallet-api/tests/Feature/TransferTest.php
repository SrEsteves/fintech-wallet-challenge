<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    private User $sender;
    private User $receiver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sender = User::factory()->create();
        Wallet::create(['user_id' => $this->sender->id, 'balance' => 1000]);

        $this->receiver = User::factory()->create();
        Wallet::create(['user_id' => $this->receiver->id, 'balance' => 1000]);
    }

    public function test_user_can_transfer_funds_successfully(): void
    {
        $this->actingAs($this->sender, 'sanctum');

        $response = $this->postJson('/api/transfer', [
            'email' => $this->receiver->email,
            'amount' => 250.50,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('wallets', ['user_id' => $this->sender->id, 'balance' => 749.50]);
        $this->assertDatabaseHas('wallets', ['user_id' => $this->receiver->id, 'balance' => 1250.50]);
    }

    public function test_transfer_fails_with_insufficient_funds(): void
    {
        $this->actingAs($this->sender, 'sanctum');

        $response = $this->postJson('/api/transfer', [
            'email' => $this->receiver->email,
            'amount' => 1500.00,
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('wallets', ['user_id' => $this->sender->id, 'balance' => 1000]);
        $this->assertDatabaseHas('wallets', ['user_id' => $this->receiver->id, 'balance' => 1000]);
    }

    public function test_transfer_fails_with_zero_or_negative_amount(): void
    {
        $this->actingAs($this->sender, 'sanctum');

        $responseNegative = $this->postJson('/api/transfer', [
            'email' => $this->receiver->email,
            'amount' => -50.00,
        ]);

        $responseNegative->assertStatus(422);

        $responseZero = $this->postJson('/api/transfer', [
            'email' => $this->receiver->email,
            'amount' => 0,
        ]);

        $responseZero->assertStatus(422);
    }

    public function test_user_cannot_transfer_to_self(): void
    {
        $this->actingAs($this->sender, 'sanctum');

        $response = $this->postJson('/api/transfer', [
            'email' => $this->sender->email,
            'amount' => 100.00,
        ]);

        $response->assertStatus(400);
    }

    public function test_transfer_records_transaction_history_correctly(): void
    {
        $this->actingAs($this->sender, 'sanctum');

        $this->postJson('/api/transfer', [
            'email' => $this->receiver->email,
            'amount' => 100.00,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->sender->id,
            'related_user_id' => $this->receiver->id,
            'type' => 'debit',
            'amount' => 100.00,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->receiver->id,
            'related_user_id' => $this->sender->id,
            'type' => 'credit',
            'amount' => 100.00,
        ]);
    }
}