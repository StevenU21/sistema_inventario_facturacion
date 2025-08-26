<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Category::class);
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('destroy', $category);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
