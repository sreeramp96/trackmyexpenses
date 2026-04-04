<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function create(array $data): Category
    {
        try {
            return Category::create($data);
        } catch (UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name and type already exists.',
            ]);
        }
    }

    public function update(Category $category, array $data): Category
    {
        try {
            $category->update($data);

            return $category;
        } catch (UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name and type already exists.',
            ]);
        }
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
