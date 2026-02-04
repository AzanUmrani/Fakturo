<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiCompanyException;
use App\Exceptions\Api\ApiInvoiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePaidStatusInvoiceRequest;
use App\Http\Requests\ChangeSentStatusInvoiceRequest;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\EditInvoiceRequest;
use App\Http\Resources\Api\PreInvoiceCollectionResource;
use App\Http\Resources\Api\PreInvoiceHistoryCollectionResource;
use App\Http\Resources\Api\PreInvoiceResource;
use App\Http\Services\PreInvoiceService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiPreInvoicesController extends Controller
{
    public function __construct(private PreInvoiceService $preInvoiceService) {}

    /**
     * @throws ApiCompanyException
     */
    public function listPreInvoices(string $companyUuid): PreInvoiceCollectionResource
    {
        return $this->preInvoiceService->listPreInvoices($companyUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createPreInvoice(string $companyUuid, CreateInvoiceRequest $request): PreInvoiceResource
    {
        return $this->preInvoiceService->createPreInvoice($companyUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getPreInvoice(string $companyUuid, string $preInvoiceUuid): PreInvoiceResource
    {
        return $this->preInvoiceService->getPreInvoice($companyUuid, $preInvoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function editPreInvoice(string $companyUuid, string $preInvoiceUuid, EditInvoiceRequest $request): PreInvoiceResource
    {
        return $this->preInvoiceService->editPreInvoice($companyUuid, $preInvoiceUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function deletePreInvoice(string $companyUuid, string $preInvoiceUuid): JsonResponse
    {
        return $this->preInvoiceService->deletePreInvoice($companyUuid, $preInvoiceUuid);
    }

    public function getPreInvoiceHistory(string $companyUuid, string $preInvoiceUuid): PreInvoiceHistoryCollectionResource
    {
        return $this->preInvoiceService->getPreInvoiceHistory($companyUuid, $preInvoiceUuid);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changePaidStatus(string $companyUuid, string $preInvoiceUuid, ChangePaidStatusInvoiceRequest $request): PreInvoiceResource
    {
        return $this->preInvoiceService->changePaidStatus($companyUuid, $preInvoiceUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changeSentStatus(string $companyUuid, string $preInvoiceUuid, ChangeSentStatusInvoiceRequest $request): PreInvoiceResource
    {
        return $this->preInvoiceService->changeSentStatus($companyUuid, $preInvoiceUuid, $request);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getPreInvoicePreview(string $companyUuid, string $preInvoiceUuid): StreamedResponse
    {
        return $this->preInvoiceService->getPreInvoicePreviewStreamedResource($companyUuid, $preInvoiceUuid, 'pdf');
    }
}
