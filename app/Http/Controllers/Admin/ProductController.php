<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Services\FileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize("viewAny", Product::class);
        $products = Product::with(['brand', 'category', 'tax', 'unitMeasure', 'entity', 'productStatus'])->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize("create", Product::class);
        return view('admin.products.create');
    }

    public function store(ProductRequest $request, FileService $fileService)
    {
        $this->authorize("create", Product::class);
        $data = $request->validated();
        $product = new Product($data);
        if ($request->hasFile('image')) {
            $product->image = $fileService->storeLocal($product, 'image', $request->file('image'));
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
        return view('admin.products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product, FileService $fileService)
    {
        $this->authorize("update", $product);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $fileService->updateLocal($product, 'image', $request->file('image'));
        }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product, FileService $fileService)
    {
        $this->authorize("delete", $product);
        $fileService->deleteLocal($product, 'image');
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }
}