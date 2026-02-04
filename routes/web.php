<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::get('/', function () {
    /* TODO syncLangFiles */
    return Inertia::render('welcome');
})->name('home');

// Language switching route
Route::get('/language/{locale}', function (Request $request, $locale) {
    $referer = $request->headers->get('referer');

    // If referer is not set, redirect to home
    if (!$referer) {
        return redirect()->route('home', ['locale' => $locale]);
    }

    // Parse the referer URL
    $refererUrl = parse_url($referer);
    $path = $refererUrl['path'] ?? '/';

    // Build the query string with the new locale
    $query = isset($refererUrl['query']) ? parse_str($refererUrl['query'], $queryParams) : [];
    if (is_array($query)) {
        $queryParams['locale'] = $locale;
        $queryString = http_build_query($queryParams);
    } else {
        $queryString = "locale=$locale";
    }

    // Redirect back to the referer with the new locale
    return redirect($path . '?' . $queryString);
})->name('language.switch');

Route::get('/privacy-policy', function () {
    /* TODO syncLangFiles */
    return Inertia::render('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-use', function () {
    /* TODO syncLangFiles */
    return Inertia::render('terms-of-use');
})->name('terms-of-use');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Client routes
    Route::resource('clients', \App\Http\Controllers\ClientController::class)->except(['create', 'edit', 'show']);

    // Product routes
    Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['create', 'edit', 'show']);

    Route::match(['post', 'put', 'patch'], '/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])
    ->name('productss.update');

    // Product image route
    Route::get('user/product/{productUuid}/image', [\App\Http\Controllers\ProductController::class, 'showImage'])->name('products.image');

    // Company routes
    Route::resource('companies', \App\Http\Controllers\CompanyController::class)->except(['create', 'edit', 'show']);

    // Document routes
    Route::prefix('documents')->name('documents.')->group(function () {
        // Invoice routes
        Route::get('invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('pre-invoices', [\App\Http\Controllers\InvoiceController::class, 'preinvoiceview'])->name('preinvoiceview');
        Route::put('invoices/{id}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{id}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // Client select route for invoice form
        Route::get('clients', [\App\Http\Controllers\InvoiceController::class, 'getClients'])->name('clients');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// HTML to PDF test routes
Route::get('/pdf-test', [PdfTestController::class, 'index'])->name('pdf-test.index');
Route::post('/pdf-test/generate', [PdfTestController::class, 'generatePdf'])->name('pdf-test.generate');
