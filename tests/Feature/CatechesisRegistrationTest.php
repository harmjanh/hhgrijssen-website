<?php

namespace Tests\Feature;

use App\Enums\CatechesisGroup;
use App\Exports\CatechesisRegistrationsExport;
use App\Models\CatechesisRegistration;
use App\Models\CatechesisSeason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatechesisRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_registration_form_when_season_is_open(): void
    {
        CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);

        $this->get(route('catechesis-registrations.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('CatechesisRegistrations/Create')
                ->where('season', '2026-2027')
                ->has('groups', 2));
    }

    public function test_guest_is_redirected_when_no_season_is_open(): void
    {
        CatechesisSeason::factory()->create(['name' => '2026-2027', 'is_open' => false]);

        $this->get(route('catechesis-registrations.create'))
            ->assertRedirect(route('catechesis-registrations.closed'));

        $this->get(route('catechesis-registrations.closed'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('CatechesisRegistrations/Closed'));
    }

    public function test_guest_can_register_for_a_group(): void
    {
        $season = CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Monday1213->value,
        ])
            ->assertRedirect(route('catechesis-registrations.success'))
            ->assertSessionHas('catechesis_registration.first_name', 'Jan')
            ->assertSessionHas('catechesis_season', '2026-2027');

        $this->assertDatabaseHas('catechesis_registrations', [
            'season_id' => $season->id,
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Monday1213->value,
        ]);
    }

    public function test_registration_requires_all_fields(): void
    {
        CatechesisSeason::factory()->open()->create();

        $this->post(route('catechesis-registrations.store'), [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'phone', 'group']);

        $this->assertDatabaseCount('catechesis_registrations', 0);
    }

    public function test_registration_requires_a_valid_group(): void
    {
        CatechesisSeason::factory()->open()->create();

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => 'niet-bestaand',
        ])->assertSessionHasErrors('group');

        $this->assertDatabaseCount('catechesis_registrations', 0);
    }

    public function test_honeypot_rejects_spam(): void
    {
        CatechesisSeason::factory()->open()->create();

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Tuesday1415->value,
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('catechesis_registrations', 0);
    }

    public function test_registrations_cannot_be_submitted_when_season_is_closed(): void
    {
        CatechesisSeason::factory()->create(['is_open' => false]);

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Monday1213->value,
        ])->assertRedirect(route('catechesis-registrations.closed'));

        $this->assertDatabaseCount('catechesis_registrations', 0);
    }

    public function test_season_id_from_the_request_is_ignored(): void
    {
        $openSeason = CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);
        $otherSeason = CatechesisSeason::factory()->create(['name' => '2025-2026', 'is_open' => false]);

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Monday20Plus->value,
            'season_id' => $otherSeason->id,
        ])->assertRedirect(route('catechesis-registrations.success'));

        $this->assertDatabaseHas('catechesis_registrations', [
            'email' => 'jan@example.com',
            'season_id' => $openSeason->id,
        ]);

        $this->assertDatabaseMissing('catechesis_registrations', [
            'email' => 'jan@example.com',
            'season_id' => $otherSeason->id,
        ]);
    }

    public function test_same_person_can_register_again_in_a_new_season(): void
    {
        $previousSeason = CatechesisSeason::factory()->create(['name' => '2025-2026', 'is_open' => false]);

        CatechesisRegistration::factory()->create([
            'season_id' => $previousSeason->id,
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'group' => CatechesisGroup::Monday1213,
        ]);

        $newSeason = CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Tuesday1415->value,
        ])->assertRedirect(route('catechesis-registrations.success'));

        $this->assertDatabaseCount('catechesis_registrations', 2);
        $this->assertDatabaseHas('catechesis_registrations', [
            'email' => 'jan@example.com',
            'season_id' => $newSeason->id,
            'group' => CatechesisGroup::Tuesday1415->value,
        ]);
    }

    public function test_opening_a_season_closes_the_previous_one(): void
    {
        $previous = CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);
        $next = CatechesisSeason::factory()->open()->create(['name' => '2027-2028']);

        $this->assertFalse($previous->fresh()->is_open);
        $this->assertTrue($next->fresh()->is_open);
        $this->assertTrue(CatechesisSeason::current()->is($next));
    }

    public function test_success_page_shows_registration_from_session(): void
    {
        CatechesisSeason::factory()->open()->create(['name' => '2026-2027']);

        $this->post(route('catechesis-registrations.store'), [
            'first_name' => 'Jan',
            'last_name' => 'de Vries',
            'email' => 'jan@example.com',
            'phone' => '0612345678',
            'group' => CatechesisGroup::Tuesday1617->value,
        ]);

        $this->get(route('catechesis-registrations.success'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('CatechesisRegistrations/Success')
                ->where('season', '2026-2027')
                ->where('registration.first_name', 'Jan')
                ->where('registration.group_label', CatechesisGroup::Tuesday1617->label()));
    }

    public function test_export_contains_registrations_for_the_selected_season_only(): void
    {
        $season2026 = CatechesisSeason::factory()->create(['name' => '2026-2027']);
        $season2027 = CatechesisSeason::factory()->create(['name' => '2027-2028']);

        CatechesisRegistration::factory()->create([
            'season_id' => $season2026->id,
            'first_name' => 'Anna',
            'last_name' => 'Bakker',
            'email' => 'anna@example.com',
            'group' => CatechesisGroup::Monday1213,
        ]);

        CatechesisRegistration::factory()->create([
            'season_id' => $season2027->id,
            'first_name' => 'Bram',
            'last_name' => 'Visser',
            'email' => 'bram@example.com',
            'group' => CatechesisGroup::Tuesday1415,
        ]);

        $export = new CatechesisRegistrationsExport($season2026);
        $rows = $export->query()->get();

        $this->assertCount(1, $rows);
        $this->assertSame('Anna', $rows->first()->first_name);

        $mapped = $export->map($rows->first());
        $this->assertSame('Anna', $mapped[0]);
        $this->assertSame('Bakker', $mapped[1]);
        $this->assertSame(CatechesisGroup::Monday1213->label(), $mapped[4]);
    }
}
