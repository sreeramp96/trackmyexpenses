<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        // Check if has transactions
        if ($category->transactions()->exists()) {
            return false;
        }

        return $category->delete();
    }

    public function getUserCategories(int $userId): Collection
    {
        return Category::forUser($userId)
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }
}
