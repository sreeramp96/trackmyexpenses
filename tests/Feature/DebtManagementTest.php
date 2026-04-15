<?php

namespace Tests\Feature;

use App\Livewire\DebtManager;
use App\Livewire\TransactionFormModal;
use App\Models\Account;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DebtManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_debts_page_is_rendered()
    {
        $this->actingAs($this->user)
            ->get(route('debts.index'))
            ->assertStatus(200)
            ->assertSee('Debts');
    }

    public function test_can_create_debt()
    {
        Livewire::actingAs($this->user)
            ->test(DebtManager::class)
            ->set('contact_name', 'John Doe')
            ->set('direction', 'lent')
            ->set('amount', 1000)
            ->set('remaining_amount', 1000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('debts', [
            'user_id' => $this->user->id,
            'contact_name' => 'John Doe',
            'amount' => 1000,
        ]);
    }

    public function test_can_update_debt()
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(DebtManager::class)
            ->call('openModal', $debt->id)
            ->set('contact_name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'contact_name' => 'Updated Name',
        ]);
    }

    public function test_can_settle_debt_manually()
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id, 'is_settled' => false, 'remaining_amount' => 500]);

        Livewire::actingAs($this->user)
            ->test(DebtManager::class)
            ->call('settle', $debt->id);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'is_settled' => true,
            'remaining_amount' => 0,
        ]);
    }

    public function test_can_delete_debt()
    {
        $debt = Debt::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(DebtManager::class)
            ->call('delete', $debt->id);

        $this->assertSoftDeleted('debts', ['id' => $debt->id]);
    }

    public function test_recording_payment_updates_debt()
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 2000]);
        $debt = Debt::factory()->create([
            'user_id' => $this->user->id,
            'direction' => 'lent',
            'amount' => 1000,
            'remaining_amount' => 1000,
            'is_settled' => false,
        ]);

        // We test that recording a payment transaction updates the debt via observer
        Livewire::actingAs($this->user)
            ->test(TransactionFormModal::class)
            ->call('openModal', null, $debt->id)
            ->set('amount', 400)
            ->set('account_id', $account->id)
            ->call('save');

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'remaining_amount' => 600,
            'is_settled' => false,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 2400, // Income of 400 added to 2000
        ]);
    }
}
