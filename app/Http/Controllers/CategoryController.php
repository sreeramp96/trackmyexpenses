<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $categories = Category::forUser($request->user()->id)->get();
        return response()->json($categories);
    }

    public function store(CategoryRequest $request)
    {
        $category = $request->user()->categories()->create($request->validated());
        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return response()->json($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $category->update($request->validated());
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return response()->json(null, 204);
    }
}
