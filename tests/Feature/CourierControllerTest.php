<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_couriers(): void
    {
        Courier::factory()->count(20)->create();

        $response = $this->getJson('/api/couriers?per_page=10');

        $response
            ->assertOk()
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 20)
            ->assertJsonCount(10, 'data');
    }

    public function test_index_sorts_by_name_by_default(): void
    {
        Courier::factory()->create([
            'name' => 'Zulkifli',
        ]);

        Courier::factory()->create([
            'name' => 'Andi',
        ]);

        $response = $this->getJson('/api/couriers');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Andi')
            ->assertJsonPath('data.1.name', 'Zulkifli');
    }

    public function test_index_can_sort_by_joined_at(): void
    {
        Courier::factory()->create([
            'name' => 'Courier A',
            'joined_at' => '2024-02-01',
        ]);

        Courier::factory()->create([
            'name' => 'Courier B',
            'joined_at' => '2024-01-01',
        ]);

        $response = $this->getJson(
            '/api/couriers?sort=joined_at'
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Courier B');
    }

    public function test_index_supports_multi_word_search(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
        ]);

        Courier::factory()->create([
            'name' => 'Joko Santoso',
        ]);

        $response = $this->getJson(
            '/api/couriers?search=budi%20agung'
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.0.name',
                'Budiono Hadi Agung'
            );
    }

    public function test_index_can_filter_multiple_levels(): void
    {
        Courier::factory()->create([
            'level' => 2,
        ]);

        Courier::factory()->create([
            'level' => 3,
        ]);

        Courier::factory()->create([
            'level' => 5,
        ]);

        $response = $this->getJson(
            '/api/couriers?level=2,3'
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_store_creates_courier(): void
    {
        $payload = [
            'name' => 'Budi Agung',
            'phone_number' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Malang',
            'level' => 2,
            'status' => 'active',
            'joined_at' => '2024-01-10',
        ];

        $response = $this->postJson(
            '/api/couriers',
            $payload
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Budi Agung');

        $this->assertDatabaseHas('couriers', [
            'name' => 'Budi Agung',
            'phone_number' => '081234567890',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson(
            '/api/couriers',
            []
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'phone_number',
                'level',
                'joined_at',
            ]);
    }

    public function test_update_changes_courier(): void
    {
        $courier = Courier::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            [
                'name' => 'New Name',
                'phone_number' => $courier->phone_number,
                'email' => $courier->email,
                'address' => 'Malang',
                'level' => 3,
                'status' => 'active',
                'joined_at' => '2024-02-01',
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'New Name',
            'level' => 3,
        ]);
    }

    public function test_update_allows_same_phone_number_for_current_courier(): void
    {
        $courier = Courier::factory()->create([
            'phone_number' => '081234567890',
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            [
                'name' => 'Updated Name',
                'phone_number' => '081234567890',
                'email' => $courier->email,
                'address' => $courier->address,
                'level' => 2,
                'status' => 'active',
                'joined_at' => '2024-01-01',
            ]
        );

        $response->assertOk();
    }

    public function test_destroy_deletes_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('couriers', [
            'id' => $courier->id,
        ]);
    }

    public function test_show_returns_404_when_courier_not_found(): void
    {
        $response = $this->getJson('/api/couriers/999999');

        $response->assertNotFound();
    }

    public function test_destroy_returns_404_when_courier_not_found(): void
    {
        $response = $this->deleteJson(
            '/api/couriers/999999'
        );

        $response->assertNotFound();
    }
}
