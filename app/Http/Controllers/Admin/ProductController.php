<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Models\Tax;
use App\Models\UnitMeasure;
use App\Services\FileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize("viewAny", Product::class);
        $products = Product::with(['brand', 'category', 'tax', 'unitMeasure', 'entity'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
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
