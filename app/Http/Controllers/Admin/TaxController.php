<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaxController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Tax::class);
        $taxes = Tax::latest()->paginate(10);
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        $this->authorize('create', Tax::class);
        return view('admin.taxes.create');
    }

    public function store(TaxRequest $request)
    {
        Tax::create($request->validated());
        return redirect()->route('taxes.index')->with('success', 'Impuesto creado correctamente.');
    }

    public function show(Tax $tax)
    {
        $this->authorize('view', $tax);
        return view('admin.taxes.show', compact('tax'));
    }

    public function edit(Tax $tax)
    {
        $this->authorize('update', $tax);
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(TaxRequest $request, Tax $tax)
    {
        $tax->update($request->validated());
        return redirect()->route('taxes.index')->with('success', 'Impuesto actualizado correctamente.');
    }

    public function destroy(Tax $tax)
    {
        $this->authorize('destroy', $tax);
        $tax->delete();
        return redirect()->route('taxes.index')->with('success', 'Impuesto eliminado correctamente.');
    }
}
