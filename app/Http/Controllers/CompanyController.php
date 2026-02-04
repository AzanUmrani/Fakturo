<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $companies = Company::where('user_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identification_number', 'like', "%{$search}%")
                      ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->orderBy($request->sort_field ?? 'name', $request->sort_direction ?? 'asc')
            ->paginate(10)
            ->withQueryString();

        syncLangFiles(['companies']);

        return Inertia::render('companies/companies', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request): RedirectResponse
    {
        $company = new Company($request->validated());
        $company->user_id = Auth::id();
        $company->uuid = (string) Str::uuid();
        $company->default = false;
        $company->payment_methods = json_encode([
            'bank_transfer' => [
                'name' => '',
                'code' => '',
                'iban' => '',
                'swift' => '',
            ],
            'paypal' => '',
        ]);
        $company->template = json_encode([
            'template_primary_color' => '#b23a5a',
            'template_name' => 'Kronos',
            'visible_fields' => [
                'unit' => false,
                'tax' => false,
                'item_sku' => false,
                'item_discount' => false,
            ],
            'currency' => 'EUR',
            'language' => 'sk',
            'default' => '',
            'numbering' => [
                'invoices' => [
                    'upcoming_number' => 1,
                    'format' => 'YEAR:4;NUMBER:4',
                ]
            ],
            'texts' => '',
        ]);
        $company->save();

        return to_route('companies.index')->with('success', 'Company created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        // Check if the company belongs to the authenticated user
        if ($company->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $company->update($request->validated());

        return to_route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        // Check if the company belongs to the authenticated user
        if ($company->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $company->delete();

        return to_route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
