@extends('layouts.app')
@section('title', 'Nueva Compra')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Crear Compra</h2>
        <x-session-message />

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 max-w-3xl">
            <form method="POST" action="{{ route('purchases.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Referencia</label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Proveedor</label>
                        <select name="entity_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring text-sm" required>
                            <option value="">Seleccionar</option>
                            @foreach($entities as $id=>$name)
                                <option value="{{ $id }}" {{ old('entity_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Almacén</label>
                        <select name="warehouse_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring text-sm" required>
                            <option value="">Seleccionar</option>
                            @foreach($warehouses as $id=>$name)
                                <option value="{{ $id }}" {{ old('warehouse_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Método de pago</label>
                        <select name="payment_method_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring text-sm" required>
                            <option value="">Seleccionar</option>
                            @foreach($methods as $id=>$name)
                                <option value="{{ $id }}" {{ old('payment_method_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Usuario</label>
                        <input type="number" name="user_id" value="{{ old('user_id', auth()->id()) }}" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring text-sm" required />
                    </div>
                    <div class="hidden">
                        <input type="number" name="subtotal" value="0" />
                        <input type="number" name="total" value="0" />
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('purchases.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white">Guardar y agregar detalles</button>
                </div>
            </form>
        </div>
    </div>
@endsection
