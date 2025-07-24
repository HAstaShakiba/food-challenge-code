<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShebaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sheba_request_success()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Request is saved successfully and is in pending status',
                'price' => 500000,
                'status' => 'pending',
                'fromShebaNumber' => 'IR123456789012345678901234',
                'toShebaNumber' => 'IR987654321098765432109876',
            ]);
        $this->assertDatabaseHas('sheba_requests', [
            'user_id' => $user->id,
            'price' => 500000,
            'status' => 'pending',
        ]);
    }

    public function test_create_sheba_request_insufficient_balance()
    {
        $user = User::factory()->create(['balance' => 1000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 5000,
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(400)
            ->assertJsonFragment([
                'code' => 'INSUFFICIENT_BALANCE',
            ]);
    }
} 