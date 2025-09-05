<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\ModelSearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Category::class);
        $perPage = request('per_page', 10);
        $categories = Category::latest()->paginate($perPage);
        return view('admin.categories.index', compact('categories'));
    }

    public function search(ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Category::class);
        $params = request()->all();
        $categories = $searchService->search(
            Category::class,
            $params,
            ['name', 'description']
        );
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
        return redirect()->route('categories.index')->with('success', 'Categoria creada correctamente.');
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
        return redirect()->route('categories.index')->with('updated', 'Categoria actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('destroy', $category);
        $category->delete();
        return redirect()->route('categories.index')->with('deleted', 'Categoria eliminada correctamente.');
    }
}
