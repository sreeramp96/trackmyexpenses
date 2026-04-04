<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BudgetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_budgets_page_is_rendered()
    {
        $this->actingAs($this->user)
            ->get(route('budgets.index'))
            ->assertStatus(200)
            ->assertSee('Budgets');
    }

    public function test_can_create_budget()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense']);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\BudgetManager::class)
            ->set('categoryId', $category->id)
            ->set('amount', 3000)
            ->set('period', 'monthly')
            ->set('startDate', now()->startOfMonth()->format('Y-m-d'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('budgets', [
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'amount' => 3000,
        ]);
    }

    public function test_can_update_budget()
    {
        $budget = Budget::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\BudgetManager::class)
            ->call('openModal', $budget->id)
            ->set('amount', 4500)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 4500,
        ]);
    }

    public function test_can_delete_budget()
    {
        $budget = Budget::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\BudgetManager::class)
            ->call('delete', $budget->id);

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_can_navigate_months()
    {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\BudgetManager::class);
            
        $currentMonth = $component->get('month');
        
        $component->call('previousMonth');
        $this->assertEquals($currentMonth === 1 ? 12 : $currentMonth - 1, $component->get('month'));
        
        $component->call('nextMonth');
        $this->assertEquals($currentMonth, $component->get('month'));
    }
}
