<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\CompanyRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Company::class);
        $companies = Company::latest()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $this->authorize('create', Company::class);
        return view('admin.companies.create');
    }

    public function store(CompanyRequest $request)
    {
        // Lógica para almacenar una nueva compañía
    }

    public function show(Company $company)
    {
        $this->authorize('view', $company);
        return view('admin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(CompanyRequest $request, Company $company)
    {
        // Lógica para actualizar la compañía
    }
}
