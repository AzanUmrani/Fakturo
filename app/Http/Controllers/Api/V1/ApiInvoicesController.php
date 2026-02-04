<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiCompanyException;
use App\Exceptions\Api\ApiInvoiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePaidStatusInvoiceRequest;
use App\Http\Requests\ChangeSentStatusInvoiceRequest;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\EditInvoiceRequest;
use App\Http\Resources\Api\InvoiceCollectionResource;
use App\Http\Resources\Api\InvoiceHistoryCollectionResource;
use App\Http\Resources\Api\InvoiceResource;
use App\Http\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiInvoicesController extends Controller
{

    public function __construct(
        private InvoiceService $invoiceService
    ) {
    }

    /**
     * @throws ApiCompanyException
     */
    public function listInvoices(string $companyUuid): InvoiceCollectionResource
    {
        return $this->invoiceService->listInvoices($companyUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createInvoice(string $companyUuid, CreateInvoiceRequest $request): invoiceResource
    {
        return $this->invoiceService->createInvoice($companyUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getInvoice(string $companyUuid, string $invoiceUuid): InvoiceResource
    {
        return $this->invoiceService->getInvoice($companyUuid, $invoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function editInvoice(string $companyUuid, string $invoiceUuid, EditInvoiceRequest $request): InvoiceResource
    {
        return $this->invoiceService->editInvoice($companyUuid, $invoiceUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function deleteInvoice(string $companyUuid, string $invoiceUuid): JsonResponse
    {
        return $this->invoiceService->deleteInvoice($companyUuid, $invoiceUuid);
    }

    public function getInvoiceHistory(string $companyUuid, string $invoiceUuid): InvoiceHistoryCollectionResource
    {
        return $this->invoiceService->getInvoiceHistory($companyUuid, $invoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createInvoicePreview(string $companyUuid, CreateInvoiceRequest $request): JsonResponse
    {
        return $this->invoiceService->createInvoicePreview($companyUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getFutureInvoicePreview(string $companyUuid): StreamedResponse
    {
        return $this->invoiceService->getFutureInvoicePreviewStreamedResource($companyUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getInvoicePreview(string $companyUuid, string $invoiceUuid): StreamedResponse
    {
        return $this->invoiceService->getInvoicePreviewStreamedResource($companyUuid, $invoiceUuid, 'pdf');
    }

    public function getInvoiceIsdocPreview(string $companyUuid, string $invoiceUuid): StreamedResponse
    {
        return $this->invoiceService->getInvoicePreviewStreamedResource($companyUuid, $invoiceUuid, 'isdoc');
    }

    public function getInvoiceIsdocPdfPreview(string $companyUuid, string $invoiceUuid): StreamedResponse
    {
        return $this->invoiceService->getInvoicePreviewStreamedResource($companyUuid, $invoiceUuid, 'isdocPdf');
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getInvoicesBulkZip(string $companyUuid, Request $request): StreamedResponse
    {
        return $this->invoiceService->getInvoicesBulkZip($companyUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function generateInvoicePdf(string $companyUuid, string $invoiceUuid): StreamedResponse
    {
        $this->invoiceService->generateInvoicePdf($companyUuid, $invoiceUuid);

        return $this->getInvoicePreview($companyUuid, $invoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changePaidStatus(string $companyUuid, string $invoiceUuid, ChangePaidStatusInvoiceRequest $request): InvoiceResource
    {
        return $this->invoiceService->changePaidStatus($request, $companyUuid, $invoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changeSentStatus(string $companyUuid, string $invoiceUuid, ChangeSentStatusInvoiceRequest $request): InvoiceResource
    {
        return $this->invoiceService->changeSentStatus($companyUuid, $invoiceUuid, $request);
    }
}
