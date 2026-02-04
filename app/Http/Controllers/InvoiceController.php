<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\EditInvoiceRequest;
use App\Http\Services\InvoiceService;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): Response
    {
        // Get all companies for the user
        $companies = Company::where('user_id', Auth::id())->get();

        if ($companies->isEmpty()) {
            syncLangFiles(['documents', 'invoices']);

            return Inertia::render('documents/invoices', [
                'invoices' => [],
                'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
                'company' => null,
                'companies' => [],
                'error' => 'No active company found. Please create or activate a company first.'
            ]);
        }

        // Get the first company or the specified one
        $company = $companies->first();
        if ($request->has('company_uuid')) {
            $company = $companies->where('uuid', $request->company_uuid)->first() ?? $company;
        }

        // Get invoices for the company
        $invoicesResource = $this->invoiceService->listInvoices($company->uuid);
        $invoices = $invoicesResource->collection;

        // Apply search filter if provided
        if ($request->search) {
            $search = $request->search;
            $invoices = $invoices->filter(function ($invoice) use ($search) {
                return str_contains(strtolower($invoice->number), strtolower($search)) ||
                    str_contains(strtolower($invoice->billed_to_client['name'] ?? ''), strtolower($search));
            });
        }

        // Apply sorting if provided
        $sortField = $request->sort_field ?? 'billed_date';
        $sortDirection = $request->sort_direction ?? 'desc';

        $invoices = $invoices->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');

        // Paginate the results
        $perPage = 10;
        $page = $request->input('page', 1);
        $total = $invoices->count();

        $invoices = $invoices->forPage($page, $perPage);

        $paginatedInvoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $invoices,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        syncLangFiles(['documents', 'invoices']);

        return Inertia::render('documents/invoices', [
            'invoices' => $paginatedInvoices,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
            'company' => $company,
            'companies' => $companies,
        ]);
    }

    public function preinvoiceview(Request $request, \App\Http\Services\PreInvoiceService $preInvoiceService): Response
    {
        // Get all companies for the user
        $companies = Company::where('user_id', Auth::id())->get();

        if ($companies->isEmpty()) {
            syncLangFiles(['documents', 'preinvoices']);
            return Inertia::render('documents/preinvoices', [
                'preInvoices' => [],
                'filters' => [],
                'companies' => [],
                'error' => 'No companies found. Please create a company first.'
            ]);
        }

        // Get the first company or the specified one
        $company = $companies->first();
        if ($request->has('company_uuid')) {
            $company = $companies->where('uuid', $request->company_uuid)->first() ?? $company;
        }

        // Get pre-invoices for the company
        $preInvoicesResource = $preInvoiceService->listPreInvoices($company->uuid);
        $preInvoices = $preInvoicesResource->collection;

        // Apply search filter if provided
        if ($request->search) {
            $search = $request->search;
            $preInvoices = $preInvoices->filter(function ($preInvoice) use ($search) {
                return str_contains(strtolower($preInvoice->number), strtolower($search)) ||
                    str_contains(strtolower($preInvoice->billed_to_client['name'] ?? ''), strtolower($search));
            });
        }

        // Apply sorting if provided
        $sortField = $request->sort_field ?? 'billed_date';
        $sortDirection = $request->sort_direction ?? 'desc';

        $preInvoices = $preInvoices->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');

        // Paginate the results
        $perPage = 10;
        $page = $request->input('page', 1);
        $total = $preInvoices->count();

        $preInvoices = $preInvoices->forPage($page, $perPage);

        $paginatedPreInvoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $preInvoices,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        syncLangFiles(['documents', 'preinvoices']);

        return Inertia::render('documents/preinvoices', [
            'preInvoices' => $paginatedPreInvoices,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
            'companies' => $companies
        ]);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(CreateInvoiceRequest $request): RedirectResponse
    {
        // Get company from request or use the user's first company
        $companyUuid = $request->input('company_uuid');
        
        $company = Company::where('user_id', Auth::id())
            ->when($companyUuid, function ($query) use ($companyUuid) {
                $query->where('uuid', $companyUuid);
            })
            ->first();

        if (!$company) {
            return redirect()->route('documents.invoices.index')
                ->with('error', 'No active company found. Please create or activate a company first.');
        }

        // Create the invoice
        $this->invoiceService->createInvoice($company->uuid, $request);

        return redirect()->route('documents.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Update the specified invoice.
     */
    public function update(EditInvoiceRequest $request, string $id): RedirectResponse
    {
        // Get company from request or use the user's first company
        $companyUuid = $request->input('company_uuid');
        
        $company = Company::where('user_id', Auth::id())
            ->when($companyUuid, function ($query) use ($companyUuid) {
                $query->where('uuid', $companyUuid);
            })
            ->first();

        if (!$company) {
            return redirect()->route('documents.invoices.index')
                ->with('error', 'No active company found. Please create or activate a company first.');
        }

        // Update the invoice
        $this->invoiceService->editInvoice($company->uuid, $id, $request);

        return redirect()->route('documents.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(string $id): RedirectResponse
    {
        // Get the user's active company
        $company = Company::where('user_id', Auth::id())
            ->first();

        if (!$company) {
            return redirect()->route('documents.invoices.index')
                ->with('error', 'No active company found. Please create or activate a company first.');
        }

        // Delete the invoice
        $this->invoiceService->deleteInvoice($company->uuid, $id);

        return redirect()->route('documents.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Get clients for the client select component.
     */
    public function getClients(Request $request)
    {
        $clients = Client::where('user_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identification_number', 'like', "%{$search}%")
                      ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'clients' => $clients,
        ]);
    }
}
