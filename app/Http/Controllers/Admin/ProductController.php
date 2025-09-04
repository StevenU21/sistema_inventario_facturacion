<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Product;
use App\Models\UnitMeasure;
use App\Services\FileService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize("viewAny", Product::class);
        $products = Product::with(['brand', 'category', 'tax', 'unitMeasure', 'entity'])->latest()->paginate(10);
        $brands = Brand::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        return view('admin.products.index', compact('products', 'brands', 'categories', 'units', 'taxes'));
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $perPage = $request->input('per_page', 10);
        $brandId = $request->input('brand_id');
        $categoryId = $request->input('category_id');
        $unitId = $request->input('unit_measure_id');
        $taxId = $request->input('tax_id');
        $status = $request->input('status');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Product::with(['brand', 'category', 'tax', 'unitMeasure', 'entity']);
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (!empty($unitId)) {
            $query->where('unit_measure_id', $unitId);
        }
        if (!empty($taxId)) {
            $query->where('tax_id', $taxId);
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%$search%")
                        ;
                    });
            });
        }
        $allowedSorts = ['id', 'name', 'brand_id', 'category_id', 'tax_id', 'unit_measure_id', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $products = $query->paginate($perPage)->appends($request->all());
        $brands = Brand::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        return view('admin.products.index', compact('products', 'brands', 'categories', 'units', 'taxes'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $brandId = $request->input('brand_id');
        $categoryId = $request->input('category_id');
        $unitId = $request->input('unit_measure_id');
        $taxId = $request->input('tax_id');
        $status = $request->input('status');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = Product::with(['brand', 'category', 'tax', 'unitMeasure', 'entity']);
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (!empty($unitId)) {
            $query->where('unit_measure_id', $unitId);
        }
        if (!empty($taxId)) {
            $query->where('tax_id', $taxId);
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%$search%")
                        ;
                    });
            });
        }
        $allowedSorts = ['id', 'name', 'brand_id', 'category_id', 'tax_id', 'unit_measure_id', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $timestamp = now()->format('Ymd_His');
        $filename = "productos_filtrados_{$timestamp}.xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport($query), $filename);
    }

    public function create()
    {
        $this->authorize("create", Product::class);
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(function ($entity) {
                return $entity->first_name . ' ' . $entity->last_name;
            }, 'id');
        $taxes = Tax::pluck('name', 'id');
        return view('admin.products.create', compact('categories', 'brands', 'units', 'entities', 'taxes'));
    }

    public function store(ProductRequest $request, FileService $fileService)
    {
        $this->authorize("create", Product::class);
        $data = $request->validated();
        $product = new Product($data);
        if ($request->hasFile('image')) {
            $product->image = $fileService->storeLocal($product, $request->file('image'));
        }
        $product->save();
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product)
    {
        $this->authorize("view", $product);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize("update", $product);
        $categories = Category::pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = UnitMeasure::pluck('name', 'id');
        $entities = Entity::where('is_active', true)
            ->where('is_supplier', true)
            ->get()
            ->pluck(function ($entity) {
                return $entity->first_name . ' ' . $entity->last_name;
            }, 'id');
        $taxes = Tax::pluck('name', 'id');
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'units', 'entities', 'taxes'));
    }

    public function update(ProductRequest $request, Product $product, FileService $fileService)
    {
        $this->authorize("update", $product);
        $data = $request->validated();
        $imagePath = $fileService->updateLocal($product, 'image', $request);
        if ($imagePath) {
            $data['image'] = $imagePath;
        }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product, FileService $fileService)
    {
        $this->authorize("destroy", $product);
        $fileService->deleteLocal($product, 'image');
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }
}
