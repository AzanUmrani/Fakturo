<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\ApiClientsController;
use App\Http\Controllers\Api\V1\ApiCompaniesController;
use App\Http\Controllers\Api\V1\ApiInvoicesController;
use App\Http\Controllers\Api\V1\ApiPreInvoicesController;
use App\Http\Controllers\Api\V1\ApiProductController;
use App\Http\Controllers\Api\V1\ApiReceiptsController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Services\InvoiceService;
use App\Http\Utils\FileHelper;
use App\Http\Utils\PdfHelper;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/* public routes*/
/* Auth - forgot password */
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetCode']);
Route::post('forgot-password/reset', [ForgotPasswordController::class, 'reset']);
/* Auth */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/social', [AuthController::class, 'loginSocial']);
Route::post('/login/guest', [AuthController::class, 'loginGuest']);

/* protected routes */
Route::group(['middleware' => 'auth:sanctum'], function () {
    /* Auth */
    Route::post('/logout', [AuthController::class, 'logout']);

    /* My account */
    Route::get('/user/subscription', [AuthController::class, 'isSubscribedAndActive']);
    Route::post('/user/subscription', [AuthController::class, 'addSubscription']);
    Route::post('/user/add-social', [AuthController::class, 'addSocial']);
    Route::post('/user/remove-social', [AuthController::class, 'removeSocial']);
    Route::delete('/user/delete', [AuthController::class, 'delete']);

    /* autocomplete */
    Route::get('/autocomplete/company', [\App\Http\Controllers\Api\AutocompleteController::class, 'getCompanyDataFromExternalSource']);

    /* client */
    Route::get('/user/clients', [ApiClientsController::class, 'listClients']);
    Route::post('/user/client', [ApiClientsController::class, 'createClient']);
    Route::get('/user/client/{clientUuid}', [ApiClientsController::class, 'getClient']);
    Route::post('/user/client/{clientUuid}', [ApiClientsController::class, 'updateClient']);
    Route::delete('/user/client/{clientUuid}', [ApiClientsController::class, 'deleteClient']);
    Route::get('/user/client/{clientUuid}/invoices', [ApiClientsController::class, 'getInvoices']);
    Route::post('/user/client/{clientUuid}/createStatement', [ApiClientsController::class, 'createClientStatement']); /* create */
    Route::get('/user/client/{clientUuid}/getStatement', [ApiClientsController::class, 'getExistingClientStatement']); /* get existing */
    Route::get('/user/client/{clientUuid}/createAndGetStatement', [ApiClientsController::class, 'createAndGetStatement']); /* create and get */

    /* products */
    Route::get('/user/products', [ApiProductController::class, 'listProducts']);
    Route::post('/user/product', [ApiProductController::class, 'createProduct']);
    Route::get('/user/product/{productUuid}', [ApiProductController::class, 'getProduct']);
    Route::post('/user/product/{productUuid}', [ApiProductController::class, 'updateProduct']);
    Route::delete('/user/product/{productUuid}', [ApiProductController::class, 'deleteProduct']);
    Route::get('/user/product/{productUuid}/image', [ApiProductController::class, 'getProductImage']);

    /* company */
    Route::get('/user/companies', [ApiCompaniesController::class, 'listCompanies']);
    Route::post('/user/company', [ApiCompaniesController::class, 'createCompany']);
    Route::get('/user/company/{companyUuid}', [ApiCompaniesController::class, 'getCompany']);
    Route::post('/user/company/{companyUuid}/template', [ApiCompaniesController::class, 'updateCompanyTemplate']);
    Route::post('/user/company/{companyUuid}/basic', [ApiCompaniesController::class, 'updateCompanyBasicInfo']);
    Route::post('/user/company/{companyUuid}/bankAccount', [ApiCompaniesController::class, 'updateCompanyBankAccount']);
    Route::delete('/user/company/{companyUuid}', [ApiCompaniesController::class, 'deleteCompany']);
    Route::get('/user/company/{companyUuid}/signature', [ApiCompaniesController::class, 'getCompanySignature']);
    Route::post('/user/company/{companyUuid}/signature', [ApiCompaniesController::class, 'updateCompanySignature']);

    /* documents */
    /* documents - invoices */
    Route::post('/user/company/{companyUuid}/invoice/createFutureInvoicePreview', [ApiInvoicesController::class, 'createInvoicePreview']); /* preview */
    Route::get('/user/company/{companyUuid}/invoice/getFutureInvoicePreview', [ApiInvoicesController::class, 'getFutureInvoicePreview']); /* preview */
    Route::get('/user/company/{companyUuid}/invoices/pdfBulk', [ApiInvoicesController::class, 'getInvoicesBulkZip']);
    Route::get('/user/company/{companyUuid}/invoices', [ApiInvoicesController::class, 'listInvoices']);
    Route::post('/user/company/{companyUuid}/invoice', [ApiInvoicesController::class, 'createInvoice']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}', [ApiInvoicesController::class, 'getInvoice']);
    Route::post('/user/company/{companyUuid}/invoice/{invoiceUuid}', [ApiInvoicesController::class, 'editInvoice']);
    Route::delete('/user/company/{companyUuid}/invoice/{invoiceUuid}', [ApiInvoicesController::class, 'deleteInvoice']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/history', [ApiInvoicesController::class, 'getInvoiceHistory']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/pdf', [ApiInvoicesController::class, 'getInvoicePreview']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/isdoc', [ApiInvoicesController::class, 'getInvoiceIsdocPreview']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/pdfIsdoc', [ApiInvoicesController::class, 'getInvoiceIsdocPdfPreview']);
    Route::post('/user/company/{companyUuid}/invoice/{invoiceUuid}/pdfGenerate', [ApiInvoicesController::class, 'generateInvoicePdf']);
//    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/isdocGenerate', [ApiInvoicesController::class, '']);
    Route::post('/user/company/{companyUuid}/invoice/{invoiceUuid}/paid', [ApiInvoicesController::class, 'changePaidStatus']);
    Route::post('/user/company/{companyUuid}/invoice/{invoiceUuid}/sent', [ApiInvoicesController::class, 'changeSentStatus']);
    /* invoice receipts */
    Route::post('/user/company/{companyUuid}/invoice/{invoiceUuid}/receipt', [ApiReceiptsController::class, 'createInvoiceReceipt']);
    Route::delete('/user/company/{companyUuid}/invoice/{invoiceUuid}/receipt/{receiptUuid}', [ApiReceiptsController::class, 'deleteInvoiceReceipt']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/receipts', [ApiReceiptsController::class, 'getInvoiceReceipts']);
    Route::get('/user/company/{companyUuid}/invoice/{invoiceUuid}/receipt/pdf', [ApiReceiptsController::class, 'getInvoiceReceiptPdf']);
    /* documents - preinvoices (proforma) */
    Route::get('/user/company/{companyUuid}/preinvoices', [ApiPreInvoicesController::class, 'listPreInvoices']);
    Route::post('/user/company/{companyUuid}/preinvoice', [ApiPreInvoicesController::class, 'createPreInvoice']);
    Route::get('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}', [ApiPreInvoicesController::class, 'getPreInvoice']);

    Route::post('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}', [ApiPreInvoicesController::class, 'editPreInvoice']);
    Route::delete('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}', [ApiPreInvoicesController::class, 'deletePreInvoice']);
    Route::get('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}/history', [ApiPreInvoicesController::class, 'getPreInvoiceHistory']);
    Route::get('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}/pdf', [ApiPreInvoicesController::class, 'getPreInvoicePreview']);
    Route::post('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}/paid', [ApiPreInvoicesController::class, 'changePaidStatus']);
    Route::post('/user/company/{companyUuid}/preinvoice/{preInvoiceUuid}/sent', [ApiPreInvoicesController::class, 'changeSentStatus']);
    /* documents - recurrent invoices */

    /* receipts */
    Route::get('/user/company/{companyUuid}/receipts', [ApiReceiptsController::class, 'listReceipts']);
    Route::post('/user/company/{companyUuid}/receipt', [ApiReceiptsController::class, 'createReceipt']);
    Route::get('/user/company/{companyUuid}/receipt/{receiptUuid}', [ApiReceiptsController::class, 'getReceipt']);
    Route::post('/user/company/{companyUuid}/receipt/{receiptUuid}', [ApiReceiptsController::class, 'updateReceipt']);
    Route::delete('/user/company/{companyUuid}/receipt/{receiptUuid}', [ApiReceiptsController::class, 'deleteReceipt']);
    Route::get('/user/company/{companyUuid}/receipt/{receiptUuid}/pdf', [ApiReceiptsController::class, 'getReceiptPdf']);
});
