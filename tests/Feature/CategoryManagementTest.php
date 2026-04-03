<?php

namespace Tests\Feature;

use App\Livewire\CategoryManager;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_category_manager(): void
    {
        $this->actingAs($this->user)
            ->get(route('categories.index'))
            ->assertStatus(200);
    }

    public function test_can_create_category(): void
    {
        $this->actingAs($this->user);

        Livewire::test(CategoryManager::class)
            ->set('name', 'Food')
            ->set('type', 'expense')
            ->set('color', '#ff0000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Food',
            'user_id' => $this->user->id,
            'type' => 'expense',
        ]);
    }

    public function test_can_update_category(): void
    {
        $this->actingAs($this->user);
        $category = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Food',
            'type' => 'expense',
            'color' => '#ff0000',
        ]);

        Livewire::test(CategoryManager::class)
            ->call('openModal', $category->id)
            ->set('name', 'Groceries')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Groceries',
        ]);
    }

    public function test_cannot_edit_system_category(): void
    {
        $this->actingAs($this->user);
        $category = Category::create([
            'user_id' => null, // System category
            'name' => 'Salary',
            'type' => 'income',
        ]);

        Livewire::test(CategoryManager::class)
            ->call('openModal', $category->id)
            ->assertSet('showModal', false); // Should not open modal for system categories
    }

    public function test_can_delete_category(): void
    {
        $this->actingAs($this->user);
        $category = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Food',
            'type' => 'expense',
            'color' => '#ff0000',
        ]);

        Livewire::test(CategoryManager::class)
            ->call('delete', $category->id);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}
