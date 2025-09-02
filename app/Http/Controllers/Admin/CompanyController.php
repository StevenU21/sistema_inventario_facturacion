<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\FileService;
use App\Models\Company;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Company::class);
        $company = Company::first();
        if (!$company) {
            return redirect()->route('companies.create');
        }
        return redirect()->route('companies.show', $company);
    }

    public function create()
    {
        $this->authorize('create', Company::class);
        return view('admin.companies.create');
    }

    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        $company = Company::create($data);
        $fileService = new FileService();
        if ($request->hasFile('logo')) {
            $logoPath = $fileService->storeLocal($company, $request->file('logo'));
            if ($logoPath) {
                $company->logo = $logoPath;
                $company->save();
            }
        }
        return redirect()->route('companies.index')->with('success', 'Compañía creada correctamente.');
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
        $data = $request->validated();
        $company->update($data);
        $fileService = new FileService();
        $logoPath = $fileService->updateLocal($company, 'logo', $request);
        if ($logoPath) {
            $company->logo = $logoPath;
            $company->save();
        }
        return redirect()->route('companies.index')->with('success', 'Compañía actualizada correctamente.');
    }
}
