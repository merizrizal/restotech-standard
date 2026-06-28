<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Artisan;
use Restotech\Standard\Models\DiningArea;

beforeEach(function (): void {
    Artisan::call('migrate:fresh', ['--force' => true]);
});

function backOfficeUser(): Authenticatable
{
    $user = new class extends Authenticatable
    {
        protected $table = 'restotech_back_office_users';

        public $timestamps = false;
    };

    $user->forceFill([
        'id' => 1,
        'name' => 'Back Office User',
        'email' => 'back-office@example.test',
    ]);
    $user->exists = true;

    return $user;
}

it('requires authentication for back office screens', function () {
    $this->get('/restotech/admin/dining-areas')
        ->assertRedirect('/login');
});

it('lists, creates, and updates dining areas', function () {
    $this->actingAs(backOfficeUser());

    $diningArea = DiningArea::factory()->create([
        'code' => 'AREA-100',
        'name' => 'Indoor',
        'sort_order' => 10,
    ]);

    $this->get('/restotech/admin/dining-areas')
        ->assertOk()
        ->assertSee('Dining Areas')
        ->assertSee('Indoor');

    $this->get('/restotech/admin/dining-areas/create')
        ->assertOk()
        ->assertSee('Create Dining Area');

    $createToken = csrf_token();
    $this->post('/restotech/admin/dining-areas', [
        '_token' => $createToken,
        'code' => 'AREA-200',
        'name' => 'Patio',
        'sort_order' => 20,
        'is_active' => '1',
        'notes' => 'Outdoor seating',
    ])->assertRedirect(route('restotech.standard.back_office.dining-areas.index'));

    $this->assertDatabaseHas('restotech_dining_areas', [
        'code' => 'AREA-200',
        'name' => 'Patio',
        'sort_order' => 20,
        'is_active' => 1,
        'notes' => 'Outdoor seating',
    ]);

    $this->get('/restotech/admin/dining-areas/' . $diningArea->id . '/edit')
        ->assertOk()
        ->assertSee('Edit Dining Area');

    $updateToken = csrf_token();
    $this->patch('/restotech/admin/dining-areas/' . $diningArea->id, [
        '_token' => $updateToken,
        'code' => 'AREA-100',
        'name' => 'Dining Room',
        'sort_order' => 15,
        'is_active' => '0',
        'notes' => 'Updated notes',
    ])->assertRedirect(route('restotech.standard.back_office.dining-areas.index'));

    $this->assertDatabaseHas('restotech_dining_areas', [
        'id' => $diningArea->id,
        'name' => 'Dining Room',
        'sort_order' => 15,
        'is_active' => 0,
        'notes' => 'Updated notes',
    ]);
});
