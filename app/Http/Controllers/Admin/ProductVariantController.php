<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\ModelSearchService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductVariantsExport;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Http\Requests\ProductVariantRequest;

class ProductVariantController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ProductVariant::class);
        $variants = ProductVariant::with(['product', 'color', 'size'])->paginate(10);
        $products = Product::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        return view('admin.product_variants.index', compact('variants', 'products', 'colors', 'sizes'));
    }

    public function search(Request $request, ModelSearchService $searchService)
    {
        $this->authorize('viewAny', ProductVariant::class);
        $params = $request->all();
        $allowedSorts = ['id', 'sku', 'barcode', 'product_id', 'color_id', 'size_id', 'created_at'];
        if (!empty($params['sort']) && !in_array($params['sort'], $allowedSorts)) {
            unset($params['sort']);
        }
        $variants = $searchService->search(
            ProductVariant::class,
            $params,
            ['sku', 'barcode', 'product.name'],
            ['product', 'color', 'size'],
            function ($query, $p) {
                if (!empty($p['product_id'])) {
                    $query->where('product_id', $p['product_id']);
                }
                if (!empty($p['color_id'])) {
                    $query->where('color_id', $p['color_id']);
                }
                if (!empty($p['size_id'])) {
                    $query->where('size_id', $p['size_id']);
                }
            }
        );
        $products = Product::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        return view('admin.product_variants.index', compact('variants', 'products', 'colors', 'sizes'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', ProductVariant::class);
        $productId = $request->input('product_id');
        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query = ProductVariant::with(['product', 'color', 'size']);
        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }
        if (!empty($colorId)) {
            $query->where('color_id', $colorId);
        }
        if (!empty($sizeId)) {
            $query->where('size_id', $sizeId);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%$search%")
                        ;
                    });
            });
        }
        $allowedSorts = ['id', 'sku', 'barcode', 'product_id', 'color_id', 'size_id', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
        $timestamp = now()->format('Ymd_His');
        $filename = "variantes_filtradas_{$timestamp}.xlsx";
        return Excel::download(new ProductVariantsExport($query), $filename);
    }

    public function create()
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('admin.product_variants.create', compact('products', 'colors', 'sizes'));
    }

    public function store(ProductVariantRequest $request)
    {
        ProductVariant::create($request->validated());
        return redirect()->route('product_variants.index')->with('success', 'Variante creada correctamente.');
    }

    public function show(ProductVariant $product_variant)
    {
        $this->authorize('view', $product_variant);
        return view('admin.product_variants.show', compact('product_variant'));
    }

    public function edit(ProductVariant $product_variant)
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('admin.product_variants.edit', compact('product_variant', 'products', 'colors', 'sizes'));
    }

    public function update(ProductVariantRequest $request, ProductVariant $product_variant)
    {
        $product_variant->update($request->validated());
        return redirect()->route('product_variants.index')->with('success', 'Variante actualizada correctamente.');
    }

    public function destroy(ProductVariant $product_variant)
    {
        $this->authorize('destroy', $product_variant);
        $product_variant->delete();
        return redirect()->route('product_variants.index')->with('success', 'Variante eliminada correctamente.');
    }
}
