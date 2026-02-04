<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanySetTemplateRequest;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyBankAccountRequest;
use App\Http\Requests\UpdateCompanyBasicInfoRequest;
use App\Http\Requests\UpdateCompanySignatureRequest;
use App\Http\Resources\Api\CompanyCollectionResource;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Services\CompaniesService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiCompaniesController extends Controller
{
    public function __construct(
        private CompaniesService $companiesService,
    ) {
    }

    public function listCompanies(): CompanyCollectionResource
    {
        return $this->companiesService->listCompanies();
    }

    public function createCompany(CreateCompanyRequest $request): CompanyResource
    {
        return $this->companiesService->createCompany($request);
    }

    public function getCompany(string $companyUuid): CompanyResource
    {
        return $this->companiesService->getCompany($companyUuid);
    }

    public function deleteCompany(string $companyUuid): JsonResponse
    {
        return $this->companiesService->deleteCompany($companyUuid);
    }

    public function getCompanySignature(string $companyUuid): StreamedResponse
    {
        return $this->companiesService->getCompanySignature($companyUuid);
    }

    public function updateCompanySignature(string $companyUuid, UpdateCompanySignatureRequest $request): JsonResponse
    {
        return $this->companiesService->updateCompanySignature($companyUuid, $request);
    }

    public function updateCompanyBasicInfo(string $companyUuid, UpdateCompanyBasicInfoRequest $request)
    {
        return $this->companiesService->updateCompanyBasicInfo($companyUuid, $request);
    }

    public function updateCompanyTemplate(string $companyUuid, CompanySetTemplateRequest $request): CompanyResource
    {
        return $this->companiesService->updateCompanyTemplate($companyUuid, $request);
    }

    public function updateCompanyBankAccount(string $companyUuid, UpdateCompanyBankAccountRequest $request): CompanyResource
    {
        return $this->companiesService->updateCompanyBankAccount($companyUuid, $request);
    }
}
