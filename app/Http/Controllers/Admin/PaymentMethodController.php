<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethodRequest;
use App\Models\Department;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentMethodController extends Controller
{
    use AuthorizesRequests;


    public function index()
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $perPage = request('per_page', 10);
    $perPage = request('per_page', 10);
    $paymentMethods = PaymentMethod::latest()->paginate($perPage);
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    public function search()
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $query = PaymentMethod::query();

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
        $paymentMethods = $query->paginate($perPage)->withQueryString();
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        $this->authorize('create', PaymentMethod::class);
        return view('admin.payment_methods.create');
    }

    public function store(PaymentMethodRequest $request)
    {
        PaymentMethod::create($request->validated());
        return redirect()->route('payment_methods.index')->with('success', 'Método de pago creado correctamente.');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $this->authorize('view', $paymentMethod);
        return view('admin.payment_methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($request->validated());
        return redirect()->route('payment_methods.index')->with('success', 'Método de pago actualizado correctamente.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('destroy', $paymentMethod);
        $paymentMethod->delete();
        return redirect()->route('payment_methods.index')->with('success', 'Método de pago eliminado correctamente.');
    }
}
