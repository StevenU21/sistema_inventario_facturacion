<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BrandController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Brand::class);
        $perPage = request('per_page', 10);
        $brands = Brand::latest()->paginate($perPage);
        return view('admin.brands.index', compact('brands'));
    }

    public function search()
    {
        $this->authorize('viewAny', Brand::class);
        $query = Brand::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if (request('name')) {
            $query->where('name', request('name'));
        }

        // Ordenamiento
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        $allowedSorts = ['id', 'name', 'description', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $perPage = request('per_page', 10);
        $brands = $query->paginate($perPage)->withQueryString();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        $this->authorize('create', Brand::class);
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request)
    {
        Brand::create($request->validated());
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
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());
        return redirect()->route('brands.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Brand $brand)
    {
        $this->authorize('destroy', $brand);
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Marca eliminada correctamente.');
    }
}
