<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_transactions_page_is_rendered()
    {
        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertStatus(200)
            ->assertSee('Transaction History');
    }

    public function test_can_filter_transactions_by_search()
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'note' => 'Coffee with friends',
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'note' => 'Monthly Rent',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TransactionList::class)
            ->set('search', 'Coffee')
            ->assertSee('Coffee with friends')
            ->assertDontSee('Monthly Rent');
    }

    public function test_can_filter_transactions_by_type()
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'income',
            'note' => 'Salary',
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'expense',
            'note' => 'Grocery',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TransactionList::class)
            ->set('type', 'income')
            ->assertSee('Salary')
            ->assertDontSee('Grocery');
    }

    public function test_can_delete_transaction()
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 1000]);
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'type' => 'expense',
            'amount' => 100,
        ]);

        // Verify balance was adjusted by factory/observer
        $this->assertEquals(900, $account->fresh()->balance);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TransactionList::class)
            ->call('deleteTransaction', $transaction->id);

        $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
        
        // Verify balance was reversed
        $this->assertEquals(1000, $account->fresh()->balance);
    }
}
