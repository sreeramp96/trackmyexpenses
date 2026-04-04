<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_accounts_page_is_rendered()
    {
        $this->actingAs($this->user)
            ->get(route('accounts.index'))
            ->assertStatus(200)
            ->assertSee('Manage Accounts');
    }

    public function test_can_create_account()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\AccountManager::class)
            ->set('name', 'Savings Account')
            ->set('type', 'bank')
            ->set('balance', 5000)
            ->set('currency', 'INR')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('accounts', [
            'user_id' => $this->user->id,
            'name' => 'Savings Account',
            'balance' => 5000,
        ]);
    }

    public function test_can_update_account()
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\AccountManager::class)
            ->call('openModal', $account->id)
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_account()
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\AccountManager::class)
            ->call('delete', $account->id);

        $this->assertSoftDeleted('accounts', ['id' => $account->id]);
    }

    public function test_validation_works()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\AccountManager::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }
}
