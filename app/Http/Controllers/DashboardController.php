<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\PreInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalClients = Client::where('user_id', $userId)->count();
        $totalCompanies = Company::where('user_id', $userId)->count();
        $totalProducts = Product::where('user_id', $userId)->count();
        $totalinvoice = Invoice::whereHas('company', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $totalpriinvoice = PreInvoice::whereHas('company', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $totalDocuments = $totalinvoice + $totalpriinvoice;

        return Inertia::render('dashboard', [
            'totalClients' => $totalClients,
            'totalCompanies' => $totalCompanies,
            'totalProducts' => $totalProducts,
            'totalDocuments' => $totalDocuments,
        ]);
    }
}