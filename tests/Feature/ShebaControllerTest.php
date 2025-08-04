<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ShebaRequest;

class ShebaControllerTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_SHEBA_1 = 'IR820540102680020817909002';
    private const VALID_SHEBA_2 = 'IR062960000000100324200001';
    private const INVALID_SHEBA = 'IR000000000000000000000000';

    public function test_create_sheba_request_success()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
            'note' => 'توضیح تست',
        ];
        $response = $this->postJson('/api/sheba', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Request is saved successfully and is in pending status',
                'price' => 500000,
                'status' => ShebaRequest::STATUS_PENDING,
                'fromShebaNumber' => self::VALID_SHEBA_1,
                'toShebaNumber' => self::VALID_SHEBA_2,
            ]);
        $this->assertDatabaseHas('sheba_requests', [
            'user_id' => $user->id,
            'price' => 500000,
            'status' => ShebaRequest::STATUS_PENDING,
        ]);
    }

    public function test_create_sheba_request_invalid_sheba()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $payload = [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::INVALID_SHEBA,
            'toShebaNumber' => self::INVALID_SHEBA,
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
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
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
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $response = $this->getJson('/api/sheba?user_id=' . $user->id);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'price' => 500000,
                'status' => ShebaRequest::STATUS_PENDING,
            ]);
    }

    public function test_confirm_sheba_request()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => ShebaRequest::STATUS_CONFIRMED,
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Request is Confirmed!',
                'status' => ShebaRequest::STATUS_CONFIRMED,
            ]);
        $this->assertDatabaseHas('sheba_requests', [
            'id' => $id,
            'status' => ShebaRequest::STATUS_CONFIRMED,
        ]);
    }

    public function test_cancel_sheba_request()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => ShebaRequest::STATUS_CANCELED,
            'note' => 'لغو توسط تست',
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Request is Canceled',
                'status' => ShebaRequest::STATUS_CANCELED,
            ])
            ->assertJsonPath('request.note', 'لغو توسط تست');
        $this->assertDatabaseHas('sheba_requests', [
            'id' => $id,
            'status' => ShebaRequest::STATUS_CANCELED,
            'note' => 'لغو توسط تست',
        ]);
    }

    public function test_confirm_or_cancel_request_not_found()
    {
        $response = $this->postJson('/api/sheba/999999', [
            'status' => ShebaRequest::STATUS_CONFIRMED,
        ]);
        $response->assertStatus(404);
    }

    public function test_confirm_or_cancel_request_not_pending()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $this->postJson('/api/sheba/' . $id, [
            'status' => ShebaRequest::STATUS_CONFIRMED,
        ]);
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => ShebaRequest::STATUS_CANCELED,
        ]);
        $response->assertStatus(400);
    }

    public function test_update_sheba_request_missing_status()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            // status is missing
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_update_sheba_request_invalid_status_value()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => 'invalid_status',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_update_sheba_request_invalid_note_type()
    {
        $user = User::factory()->create(['balance' => 1000000]);
        $create = $this->postJson('/api/sheba', [
            'user_id' => $user->id,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
        ]);
        $id = $create->json('request.id');
        $response = $this->postJson('/api/sheba/' . $id, [
            'status' => ShebaRequest::STATUS_CONFIRMED,
            'note' => 12345, // invalid type
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['note']);
    }
}
