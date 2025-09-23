<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethodRequest;
use App\Models\Department;
use App\Models\PaymentMethod;
use App\Services\ModelSearchService;
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

    public function search(ModelSearchService $searchService)
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $params = request()->all();
        $paymentMethods = $searchService->search(
            PaymentMethod::class,
            $params,
            ['name', 'description']
        );
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
        return redirect()->route('payment_methods.index')->with('updated', 'Método de pago actualizado correctamente.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('destroy', $paymentMethod);
        $paymentMethod->delete();
        return redirect()->route('payment_methods.index')->with('deleted', 'Método de pago eliminado correctamente.');
    }
}
