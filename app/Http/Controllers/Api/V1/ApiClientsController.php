<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiClientException;
use App\Exceptions\Api\ApiCompanyException;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\CreateClientStatementRequest;
use App\Http\Requests\EditClientRequest;
use App\Http\Resources\Api\ClientCollectionResource;
use App\Http\Resources\Api\ClientResource;
use App\Http\Resources\Api\InvoiceCollectionResource;
use App\Http\Services\ClientsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiClientsController
{

    public function __construct(
        private ClientsService $clientsService,
    ) {
    }

    public function listClients(): ClientCollectionResource
    {
        return ClientCollectionResource::make(Auth::user()->clients);
    }

    /**
     * @throws ApiClientException
     */
    public function getClient(string $clientUuid): ClientResource
    {
        return $this->clientsService->getClient($clientUuid);
    }

    public function createClient(CreateClientRequest $request): ClientResource
    {
        return $this->clientsService->createClient($request);
    }

    /**
     * @throws ApiClientException
     */
    public function updateClient(string $clientUuid, EditClientRequest $request): JsonResponse
    {
        return $this->clientsService->updateClient($clientUuid, $request);
    }

    /**
     * @throws ApiClientException
     */
    public function deleteClient(string $clientUuid): JsonResponse
    {
        return $this->clientsService->deleteClient($clientUuid);
    }

    /**
     * @throws ApiClientException
     */
    public function getInvoices(string $clientUuid): InvoiceCollectionResource
    {
        return $this->clientsService->getInvoices($clientUuid);
    }

    /**
     * @throws ApiClientException
     * @throws ApiCompanyException
     */
    public function createClientStatement(string $clientUuid, CreateClientStatementRequest $request): JsonResponse
    {
        return $this->clientsService->createClientStatement($clientUuid, $request);
    }

    /**
     * @throws ApiClientException
     * @throws ApiCompanyException
     */
    public function getExistingClientStatement(string $clientUuid, CreateClientStatementRequest $request): StreamedResponse
    {
        return $this->clientsService->getExistingClientStatement($clientUuid, $request);
    }

    public function createAndGetStatement(string $clientUuid, CreateClientStatementRequest $request): StreamedResponse
    {
        return $this->clientsService->createAndGetStatement($clientUuid, $request);
    }

}
