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
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Request is saved successfully and is in pending status',
                'price' => 500000,
                'status' => 'pending',
                'fromShebaNumber' => 'IR820540102680020817909002',
                'toShebaNumber' => 'IR062960000000100324200001',
            ]);
        $this->assertDatabaseHas('sheba_requests', [
            'user_id' => $user->id,
            'price' => 500000,
            'status' => 'pending',
        ]);
    }

    public function test_create_sheba_request_invalid_sheba()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR000000000000000000000000', // نامعتبر
            'toShebaNumber' => 'IR000000000000000000000000', // نامعتبر
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fromShebaNumber', 'toShebaNumber']);
    }

    public function test_create_sheba_request_insufficient_balance()
    {
        $user = User::factory()->create(['balance' => 1000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 5000,
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(400)
            ->assertJsonFragment([
                'code' => 'INSUFFICIENT_BALANCE',
            ]);
    }

    public function test_list_sheba_requests()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
        ]);
        $response = $this->getJson('/api/sheba?user_id=' . $user->id);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'price' => 500000,
                'status' => 'pending',
            ]);
    }

    public function test_confirm_sheba_request()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => 'confirmed',
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Request is Confirmed!',
                'status' => 'confirmed',
            ]);
        $this->assertDatabaseHas('sheba_requests', [
            'id' => $id,
            'status' => 'confirmed',
        ]);
    }

    public function test_cancel_sheba_request()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => 'canceled',
            'note' => 'لغو توسط تست',
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Request is Canceled',
                'status' => 'canceled',
            ])
            ->assertJsonPath('request.note', 'لغو توسط تست');
        $this->assertDatabaseHas('sheba_requests', [
            'id' => $id,
            'status' => 'canceled',
            'note' => 'لغو توسط تست',
        ]);
    }

    public function test_confirm_or_cancel_request_not_found()
    {
        $response = $this->postJson('/api/sheba/999999', [
            'status' => 'confirmed',
        ]);
        $response->assertStatus(404);
    }

    public function test_confirm_or_cancel_request_not_pending()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => 'IR820540102680020817909002',
            'toShebaNumber' => 'IR062960000000100324200001',
        ]);
        $id = $create->json('request.id');
        $this->postJson('/api/sheba/' . $id, [
            'status' => 'confirmed',
        ]);
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => 'canceled',
        ]);
        $response->assertStatus(400);
    }
} 