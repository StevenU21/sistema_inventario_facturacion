<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Services\ModelSearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BrandController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Brand::class);
        $perPage = request('per_page', 10);
        $brands = Brand::with('category')->latest()->paginate($perPage);
        $categories = Category::all();
        return view('admin.brands.index', compact('brands', 'categories'));
    }

    public function search(ModelSearchService $searchService)
    {
        $this->authorize('viewAny', Brand::class);
        $params = request()->all();
        $brands = $searchService->search(
            Brand::class,
            $params,
            ['name', 'description']
        );
        $categories = Category::all();
        return view('admin.brands.index', compact('brands', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Brand::class);
        $categories = Category::all();
        return view('admin.brands.create', compact('categories'));
    }

    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $data['category_id'] = $request->input('category_id');
        Brand::create($data);
        return redirect()->route('brands.index')->with('success', 'Marca creada correctamente.');
    }

    public function show(Brand $brand)
    {
        $this->authorize('view', $brand);
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        $this->authorize('update', $brand);
        $categories = Category::all();
        return view('admin.brands.edit', compact('brand', 'categories'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $data = $request->validated();
        $data['category_id'] = $request->input('category_id');
        $brand->update($data);
        return redirect()->route('brands.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Brand $brand)
    {
        $this->authorize('destroy', $brand);
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Marca eliminada correctamente.');
    }
}
