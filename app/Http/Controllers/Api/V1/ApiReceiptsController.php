<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiCompanyException;
use App\Exceptions\Api\ApiReceiptException;
use App\Http\Requests\CreateReceiptRequest;
use App\Http\Requests\EditReceiptRequest;
use App\Http\Resources\Api\ReceiptCollectionResource;
use App\Http\Resources\Api\ReceiptResource;
use App\Http\Services\ReceiptsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiReceiptsController
{
    public function __construct(
        private ReceiptsService $receiptsService,
    ) {
    }

    /**
     * @throws ApiCompanyException
     */
    public function listReceipts(string $companyUuid): ReceiptCollectionResource
    {
        return $this->receiptsService->listReceipts($companyUuid);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function getReceipt(string $companyUuid, string $receiptUuid): ReceiptResource
    {
        return $this->receiptsService->getReceipt($companyUuid, $receiptUuid);
    }

    /**
     * @throws ApiCompanyException
     */
    public function createReceipt(string $companyUuid, CreateReceiptRequest $request): ReceiptResource
    {
        return $this->receiptsService->createReceipt($companyUuid, $request);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function updateReceipt(string $companyUuid, string $receiptUuid, EditReceiptRequest $request): JsonResponse
    {
        return $this->receiptsService->updateReceipt($companyUuid, $receiptUuid, $request);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function deleteReceipt(string $companyUuid, string $receiptUuid): JsonResponse
    {
        return $this->receiptsService->deleteReceipt($companyUuid, $receiptUuid);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function getReceiptPdf(string $companyUuid, string $receiptUuid): StreamedResponse
    {
        return $this->receiptsService->getReceiptPdf($companyUuid, $receiptUuid);
    }

    /**
     * Creates a receipt associated with an invoice
     *
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function createInvoiceReceipt(string $companyUuid, string $invoiceUuid): ReceiptResource
    {
        return $this->receiptsService->createInvoiceReceipt($companyUuid, $invoiceUuid);
    }

    /**
     * Deletes a receipt associated with an invoice
     *
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     * @throws ApiReceiptException
     */
    public function deleteInvoiceReceipt(string $companyUuid, string $invoiceUuid, string $receiptUuid): JsonResponse
    {
        return $this->receiptsService->deleteInvoiceReceipt($companyUuid, $invoiceUuid, $receiptUuid);
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function getInvoiceReceipts(string $companyUuid, string $invoiceUuid): ReceiptCollectionResource
    {
        return $this->receiptsService->getInvoiceReceipts($companyUuid, $invoiceUuid);
    }

    /**
     * Returns a streamed response with the receipt PDF for a receipt associated with an invoice
     *
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     * @throws ApiReceiptException
     */
    public function getInvoiceReceiptPdf(string $companyUuid, string $invoiceUuid): StreamedResponse
    {
        return $this->receiptsService->getInvoiceReceiptPdf($companyUuid, $invoiceUuid);
    }
}
