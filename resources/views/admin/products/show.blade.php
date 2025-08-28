@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Detalle del Producto</h1>
        </div>
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="{{ $product->image_url }}" class="img-fluid rounded-start" alt="Imagen del producto">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text"><strong>Descripción:</strong> {{ $product->description }}</p>
                        <p class="card-text"><strong>Precio Compra:</strong> {{ $product->purchase_price }}</p>
                        <p class="card-text"><strong>Precio Venta:</strong> {{ $product->sale_price }}</p>
                        <p class="card-text"><strong>Stock:</strong> {{ $product->stock }}</p>
                        <p class="card-text"><strong>Stock Mínimo:</strong> {{ $product->min_stock }}</p>
                        <p class="card-text"><strong>Marca:</strong> {{ $product->brand->name ?? '-' }}</p>
                        <p class="card-text"><strong>Categoría:</strong> {{ $product->category->name ?? '-' }}</p>
                        <p class="card-text"><strong>Impuesto:</strong> {{ $product->tax->name ?? '-' }}</p>
                        <p class="card-text"><strong>Unidad de Medida:</strong> {{ $product->unitMeasure->name ?? '-' }}</p>
                        <p class="card-text"><strong>Entidad:</strong> {{ $product->entity->name ?? '-' }}</p>
                        <p class="card-text"><strong>Estado:</strong> {{ $product->productStatus->name ?? '-' }}</p>
                        <p class="card-text"><small class="text-muted">Creado: {{ $product->formatted_created_at }}</small></p>
                        <p class="card-text"><small class="text-muted">Actualizado: {{ $product->formatted_updated_at }}</small></p>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver</a>
        @can('update', $product)
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Editar</a>
        @endcan
        @can('delete', $product)
            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</button>
            </form>
        @endcan
    </div>
@endsection
