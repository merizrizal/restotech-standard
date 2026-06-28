<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Artisan;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\TransactionDay;

beforeEach(function (): void {
    Artisan::call('migrate:fresh', ['--force' => true]);
});

function posUser(): Authenticatable
{
    $user = new class extends Authenticatable
    {
        protected $table = 'restotech_back_office_users';

        public $timestamps = false;
    };

    $user->forceFill([
        'id' => 2,
        'name' => 'POS User',
        'email' => 'pos@example.test',
    ]);
    $user->exists = true;

    return $user;
}

it('requires authentication for the POS shell', function () {
    $this->get('/restotech/pos')
        ->assertRedirect(route('restotech.standard.pos.login'));
});

it('renders the POS shell', function () {
    $this->withoutVite();
    $this->actingAs(posUser());

    $this->get('/restotech/pos')
        ->assertOk()
        ->assertSee('Restotech POS')
        ->assertSee('data-restotech-pos-app');
});

it('opens a table session through the internal POS endpoint', function () {
    $this->actingAs(posUser());

    $table = DiningTable::factory()->create();
    $transactionDay = TransactionDay::create([
        'business_date' => now()->toDateString(),
        'started_at' => now(),
        'status' => 'open',
    ]);

    CashierBalance::create([
        'transaction_day_id' => $transactionDay->id,
        'opened_at' => now(),
        'opening_balance_amount' => 0,
        'closing_balance_amount' => 0,
        'status' => 'open',
    ]);

    $this->post('/restotech/pos/internal/table-sessions/open', [
        '_token' => csrf_token(),
        'dining_table_id' => $table->id,
    ])->assertCreated()
        ->assertJsonPath('data.dining_table_id', $table->id)
        ->assertJsonPath('data.transaction_day_id', $transactionDay->id)
        ->assertJsonPath('data.status', 'open');

    $this->assertDatabaseHas('restotech_table_sessions', [
        'dining_table_id' => $table->id,
        'transaction_day_id' => $transactionDay->id,
        'status' => 'open',
    ]);

    expect(TableSession::query()->count())->toBe(1);
});
