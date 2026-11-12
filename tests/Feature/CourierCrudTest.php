<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourierCrudTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'CR-100',
            'name' => 'Budiono Hadi Agung',
            'email' => 'budi@example.test',
            'phone' => '+6281234567890',
            'national_id' => '3174010101900001',
            'address' => 'Jl. Merdeka No. 1',
            'city' => 'Jakarta',
            'vehicle_type' => 'motorcycle',
            'plate_number' => 'B 1234 XYZ',
            'level' => 3,
            'status' => 'active',
            'registered_at' => '2025-01-15',
        ], $overrides);
    }

    public function test_create_and_edit_forms_render(): void
    {
        $courier = Courier::factory()->create();

        $this->get(route('couriers.create'))->assertOk();
        $this->get(route('couriers.edit', $courier))->assertOk()->assertSee($courier->name);
    }

    public function test_show_displays_all_courier_data(): void
    {
        $courier = Courier::factory()->create();

        $this->get(route('couriers.show', $courier))
            ->assertOk()
            ->assertSeeText([
                $courier->code,
                $courier->name,
                $courier->email,
                $courier->phone,
                $courier->national_id,
                $courier->city,
                $courier->vehicle_type->label(),
                $courier->status->label(),
                $courier->registered_at->format('Y-m-d'),
            ], false);
    }

    public function test_show_returns_404_for_missing_courier(): void
    {
        $this->get(route('couriers.show', 999))->assertNotFound();
    }

    public function test_store_creates_courier(): void
    {
        $response = $this->post(route('couriers.store'), $this->validData());

        $courier = Courier::firstWhere('code', 'CR-100');
        $response->assertRedirect(route('couriers.show', $courier));
        $this->assertDatabaseHas('couriers', ['code' => 'CR-100', 'name' => 'Budiono Hadi Agung', 'level' => 3]);
    }

    public function test_store_allows_bicycle_without_plate_number(): void
    {
        $this->post(route('couriers.store'), $this->validData(['vehicle_type' => 'bicycle', 'plate_number' => null]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('couriers', ['code' => 'CR-100', 'plate_number' => null]);
    }

    #[DataProvider('requiredFields')]
    public function test_store_requires_field(string $field): void
    {
        $this->post(route('couriers.store'), $this->validData([$field => '']))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('couriers', 0);
    }

    public static function requiredFields(): array
    {
        return array_map(fn (string $field) => [$field], [
            'code', 'name', 'email', 'phone', 'national_id', 'address',
            'city', 'vehicle_type', 'plate_number', 'level', 'status', 'registered_at',
        ]);
    }

    #[DataProvider('invalidValues')]
    public function test_store_rejects_invalid_value(string $field, mixed $value): void
    {
        $this->post(route('couriers.store'), $this->validData([$field => $value]))
            ->assertSessionHasErrors($field);
    }

    public static function invalidValues(): array
    {
        return [
            'bad email' => ['email', 'not-an-email'],
            'short national id' => ['national_id', '123'],
            'non numeric national id' => ['national_id', 'abcdefghijklmnop'],
            'bad phone' => ['phone', '12ab'],
            'level too low' => ['level', 0],
            'level too high' => ['level', 6],
            'level not integer' => ['level', 'high'],
            'unknown vehicle' => ['vehicle_type', 'boat'],
            'unknown status' => ['status', 'retired'],
            'future registration date' => ['registered_at', '2999-01-01'],
            'bad registration date' => ['registered_at', 'yesterday-ish'],
            'name too long' => ['name', str_repeat('a', 151)],
        ];
    }

    #[DataProvider('uniqueFields')]
    public function test_store_rejects_value_already_in_database(string $field): void
    {
        $existing = Courier::factory()->create();

        $this->post(route('couriers.store'), $this->validData([$field => $existing->$field]))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('couriers', 1);
    }

    public static function uniqueFields(): array
    {
        return array_map(fn (string $field) => [$field], ['code', 'email', 'phone', 'national_id', 'plate_number']);
    }

    public function test_update_changes_courier(): void
    {
        $courier = Courier::factory()->create();

        $this->put(route('couriers.update', $courier), $this->validData(['name' => 'Updated Name', 'level' => 5]))
            ->assertRedirect(route('couriers.show', $courier));

        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'name' => 'Updated Name', 'level' => 5]);
    }

    public function test_update_accepts_the_couriers_own_unique_values(): void
    {
        $courier = Courier::factory()->create($this->validData());

        $this->put(route('couriers.update', $courier), $this->validData(['city' => 'Bandung']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'city' => 'Bandung']);
    }

    #[DataProvider('uniqueFields')]
    public function test_update_rejects_value_owned_by_another_courier(string $field): void
    {
        $other = Courier::factory()->create();
        $courier = Courier::factory()->create();

        $this->put(route('couriers.update', $courier), $this->validData([$field => $other->$field]))
            ->assertSessionHasErrors($field);
    }

    public function test_update_returns_404_for_missing_courier(): void
    {
        $this->put(route('couriers.update', 999), $this->validData())->assertNotFound();
    }

    public function test_destroy_deletes_courier_from_database(): void
    {
        $courier = Courier::factory()->create();

        $this->delete(route('couriers.destroy', $courier))->assertRedirect(route('couriers.index'));

        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
    }

    public function test_destroy_returns_404_for_missing_courier(): void
    {
        $this->delete(route('couriers.destroy', 999))->assertNotFound();
    }
}
