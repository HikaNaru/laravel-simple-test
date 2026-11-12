<?php

namespace Tests\Unit;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_every_word_in_any_order_of_the_name(): void
    {
        Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
        Courier::factory()->create(['name' => 'Budi Santoso']);
        Courier::factory()->create(['name' => 'Agung Prasetyo']);

        $names = Courier::search('budi agung')->pluck('name');

        $this->assertSame(['Budiono Hadi Agung'], $names->all());
    }

    public function test_search_matches_code_phone_and_email(): void
    {
        $courier = Courier::factory()->create([
            'code' => 'CR-777',
            'phone' => '+6281200000001',
            'email' => 'rider@example.test',
        ]);
        Courier::factory()->create();

        $this->assertTrue(Courier::search('CR-777')->first()->is($courier));
        $this->assertTrue(Courier::search('81200000001')->first()->is($courier));
        $this->assertTrue(Courier::search('rider@example')->first()->is($courier));
    }

    public function test_search_returns_nothing_when_a_word_matches_no_courier(): void
    {
        Courier::factory()->create(['name' => 'Budiono Hadi Agung']);

        $this->assertCount(0, Courier::search('budi unknownword')->get());
    }

    public function test_search_ignores_empty_keyword(): void
    {
        Courier::factory()->count(3)->create();

        $this->assertCount(3, Courier::search(null)->get());
        $this->assertCount(3, Courier::search('   ')->get());
    }

    public function test_filter_by_levels_supports_one_or_many_levels(): void
    {
        foreach (range(1, 5) as $level) {
            Courier::factory()->create(['level' => $level]);
        }

        $this->assertSame([2], Courier::filterByLevels([2])->pluck('level')->all());
        $this->assertEqualsCanonicalizing([2, 4], Courier::filterByLevels([2, 4])->pluck('level')->all());
    }

    public function test_filter_by_levels_ignores_empty_list(): void
    {
        Courier::factory()->count(3)->create();

        $this->assertCount(3, Courier::filterByLevels([])->get());
    }

    public function test_order_by_registered_at_sorts_in_the_given_direction(): void
    {
        $old = Courier::factory()->create(['registered_at' => '2024-01-01']);
        $new = Courier::factory()->create(['registered_at' => '2025-06-01']);

        $this->assertSame([$old->id, $new->id], Courier::orderByRegisteredAt('asc')->pluck('id')->all());
        $this->assertSame([$new->id, $old->id], Courier::orderByRegisteredAt('desc')->pluck('id')->all());
    }

    public function test_order_by_registered_at_ignores_invalid_direction(): void
    {
        $this->assertStringNotContainsString('order by', Courier::orderByRegisteredAt('sideways')->toSql());
        $this->assertStringNotContainsString('order by', Courier::orderByRegisteredAt(null)->toSql());
    }
}
