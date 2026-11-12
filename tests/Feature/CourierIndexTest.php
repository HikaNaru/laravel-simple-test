<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_courier_list(): void
    {
        $this->get('/')->assertRedirect(route('couriers.index'));
    }

    public function test_lists_couriers_ordered_by_name(): void
    {
        Courier::factory()->create(['name' => 'Charlie']);
        Courier::factory()->create(['name' => 'Alpha']);
        Courier::factory()->create(['name' => 'Bravo']);

        $this->get(route('couriers.index'))
            ->assertOk()
            ->assertSeeInOrder(['Alpha', 'Bravo', 'Charlie']);
    }

    public function test_paginates_ten_per_page(): void
    {
        Courier::factory()->count(12)->create();

        $response = $this->get(route('couriers.index'))->assertOk();
        $this->assertCount(10, $response->viewData('couriers'));

        $second = $this->get(route('couriers.index', ['page' => 2]))->assertOk();
        $this->assertCount(2, $second->viewData('couriers'));
    }

    public function test_search_finds_courier_by_partial_words(): void
    {
        Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
        Courier::factory()->create(['name' => 'Siti Aminah']);

        $this->get(route('couriers.index', ['search' => 'budi agung']))
            ->assertOk()
            ->assertSee('Budiono Hadi Agung')
            ->assertDontSee('Siti Aminah');
    }

    public function test_filters_by_multiple_levels(): void
    {
        foreach (range(1, 5) as $level) {
            Courier::factory()->create(['level' => $level]);
        }

        $response = $this->get(route('couriers.index', ['level' => '2,4']))->assertOk();

        $this->assertEqualsCanonicalizing([2, 4], $response->viewData('couriers')->pluck('level')->all());
    }

    public function test_sorts_by_registration_date(): void
    {
        Courier::factory()->create(['name' => 'Alpha', 'registered_at' => '2024-01-01']);
        Courier::factory()->create(['name' => 'Bravo', 'registered_at' => '2025-01-01']);

        $this->get(route('couriers.index', ['sort_registered' => 'desc']))->assertSeeInOrder(['Bravo', 'Alpha']);
        $this->get(route('couriers.index', ['sort_registered' => 'asc']))->assertSeeInOrder(['Alpha', 'Bravo']);
    }

    public function test_pagination_links_keep_filters(): void
    {
        Courier::factory()->count(12)->create(['level' => 3]);

        $this->get(route('couriers.index', ['level' => '3', 'sort_registered' => 'asc']))
            ->assertSee('sort_registered=asc', false)
            ->assertSee('page=2', false);
    }

    public function test_rejects_invalid_filters(): void
    {
        $this->from(route('couriers.index'))
            ->get(route('couriers.index', ['level' => '9,x', 'sort_registered' => 'sideways']))
            ->assertSessionHasErrors(['level.0', 'level.1', 'sort_registered']);
    }
}
