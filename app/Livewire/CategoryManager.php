<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CategoryManager extends Component
{
    public bool $showModal = false;

    public ?int $categoryId = null;

    public string $name = '';

    public string $type = 'expense';

    public string $color = '#94a3b8';

    public string $icon = '';

    public ?int $parentId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:income,expense,transfer',
        'color' => 'required|string|max:7',
        'icon' => 'nullable|string|max:255',
        'parentId' => 'nullable|exists:categories,id',
    ];

    public function openModal(?int $id = null, ?string $type = null)
    {
        $this->reset(['name', 'type', 'color', 'icon', 'parentId', 'categoryId']);

        if ($id) {
            $category = Category::findOrFail($id);
            if ($category->user_id !== Auth::id()) {
                session()->flash('error', 'You cannot edit system categories.');

                return;
            }
            $this->categoryId = $id;
            $this->name = $category->name;
            $this->type = $category->type;
            $this->color = $category->color ?? '#94a3b8';
            $this->icon = $category->icon ?? '';
            $this->parentId = $category->parent_id;
        } elseif ($type) {
            $this->type = $type;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $service = app(CategoryService::class);
        $data = [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'type' => $this->type,
            'color' => $this->color,
            'icon' => $this->icon,
            'parent_id' => $this->parentId,
        ];

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            if ($category->user_id === Auth::id()) {
                $service->update($category, $data);
            }
        } else {
            $service->create($data);
        }

        $this->showModal = false;
        $this->reset(['name', 'type', 'color', 'icon', 'parentId', 'categoryId']);
    }

    public function delete(int $id)
    {
        $category = Category::findOrFail($id);
        if ($category->user_id !== Auth::id()) {
            session()->flash('error', 'You cannot delete system categories.');

            return;
        }

        $service = app(CategoryService::class);
        if (! $service->delete($category)) {
            session()->flash('error', 'Cannot delete category with associated transactions.');

            return;
        }

        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $categories = app(CategoryService::class)->getUserCategories(Auth::id());
        $parentOptions = Category::forUser(Auth::id())
            ->whereNull('parent_id')
            ->when($this->categoryId, fn ($q) => $q->where('id', '!=', $this->categoryId))
            ->get();

        return view('livewire.category-manager', [
            'categories' => $categories,
            'parentOptions' => $parentOptions,
        ])->layout('layouts.app', ['heading' => 'Categories']);
    }
}
