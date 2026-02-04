<?php

namespace App\Http\Services;

use App\Exceptions\Api\ApiCompanyException;
use App\Http\Requests\CompanySetTemplateRequest;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyBasicInfoRequest;
use App\Http\Requests\UpdateCompanySignatureRequest;
use App\Http\Resources\Api\CompanyCollectionResource;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Utils\InvoiceHelper;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\PreInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompaniesService
{

    public function listCompanies(): CompanyCollectionResource
    {
        return CompanyCollectionResource::make(Auth::user()->companies);
    }

    public function createCompany(CreateCompanyRequest $request): CompanyResource
    {
        $loggedUser = Auth::user();

        $loggedUserDefaultCompany = $loggedUser->companies()->where('default', true)->first();

        $newCompany = Company::create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $loggedUser->id,

            'default' => !$loggedUserDefaultCompany ? true : $request->get('default'),
            'name' => $request->get('name'),
            'state' => $request->get('state'),
            'street' => $request->get('street'),
            'street_extra' => $request->get('street_extra'),
            'zip' => $request->get('zip'),
            'city' => $request->get('city'),

            'tax_type' => $request->get('tax_type') ?? 'NO',
            'identification_number' => $request->get('identification_number'), // ico
            'vat_identification_number' => $request->get('vat_identification_number'), // dic
            'vat_identification_number_sk' => $request->get('vat_identification_number_sk'), // icdph

            'registry_info' => $request->get('registry_info'),

            'contact_name' => $request->get('contact_name'),
            'contact_phone' => $request->get('contact_phone'),
            'contact_email' => $request->get('contact_email'),
            'contact_web' => $request->get('contact_web'),

            'payment_methods' => json_encode($request->get('payment_methods')),
            'template' => json_encode(Company::getDefaultTemplate(), JSON_UNESCAPED_UNICODE),
        ]);

        if ($newCompany->default) {
            $loggedUser->companies()->where('id', '!=', $newCompany->id)->update(['default' => false]);
        }

        // logo
        $logoBase64 = $request->get('logo_base64');
        if ($logoBase64) {
            // TODO
        }

        // signature
        $signatureBase64 = $request->get('signature_base64');
        if ($signatureBase64) {
            $signatureStored = Storage::disk('local')->put($newCompany->getSignaturePath(), base64_decode($signatureBase64));
        }

        return CompanyResource::make($newCompany);
    }

    /**
     * @throws ApiCompanyException
     */
    public function getCompany(string $companyUuid): CompanyResource
    {
        $company = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        return CompanyResource::make($company);
    }

    /**
     * @throws ApiCompanyException
     */
    public function deleteCompany(string $companyUuid): JsonResponse
    {
        $loggedUser = Auth::user();

        /** @var Company $companyToDelete */
        $companyToDelete = $loggedUser->companies()->where('uuid', $companyUuid)->first();
        if (!$companyToDelete) {
            throw ApiCompanyException::companyNotFound();
        }

        $companyToDelete->delete();

        if ($companyToDelete->default) {
            $lastCompany = $loggedUser->companies()->where('id', '!=', $companyToDelete->id)->orderBy('id', 'desc')->first();
            if ($lastCompany) {
                $lastCompany->update(['default' => true]);
            }
        }

        // remove company files
        Storage::disk('local')->deleteDirectory($companyToDelete->getResourcesPath());

        return response()->json([
            'message' => 'COMPANY_DELETED',
        ]);
    }

    /**
     * @throws ApiCompanyException
     */
    public function getCompanySignature(string $companyUuid): StreamedResponse
    {
        $company = Auth::user()->companies()->where('uuid', $companyUuid)->first();

        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        if (Storage::disk('local')->missing($company->getSignaturePath())) {
            throw ApiCompanyException::signatureNotFound();
        }

        return Storage::disk('local')->response($company->getSignaturePath());
    }

    /**
     * @throws ApiCompanyException
     */
    public function updateCompanySignature(string $companyUuid, UpdateCompanySignatureRequest $request): JsonResponse
    {
        $companyToUpdate = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$companyToUpdate) {
            throw ApiCompanyException::companyNotFound();
        }

        $signatureStored = false;
        $signatureContent = $request->get('signature_base64');
        if ($signatureContent) {
            $signaturePath = $companyToUpdate->getSignaturePath();

            // save new signature
            Storage::disk('local')->put(
                $signaturePath,
                base64_decode($request->get('signature_base64'))
            );
            $signatureStoragePath = Storage::disk('local')->path($signaturePath);

            // load new signature
            $source = imagecreatefrompng($signatureStoragePath);
            // alpha background
            $bgColor = imagecolorallocatealpha($source, 255, 255, 255, 127);

            // rotate new signature
            $rotate = imagerotate($source, 90, $bgColor);

            // save alpha
            imagesavealpha($rotate, true);

            // save
            $signatureStored = imagepng($rotate, $signatureStoragePath);

            if ($signatureStored) {
                $companyToUpdate->touch();
            }
        }

        if (!$signatureStored) {
            throw ApiCompanyException::signatureNotUpdated();
        }

        return response()->json([
            'message' => 'COMPANY_SIGNATURE_UPDATED',
        ]);
    }

    public function incrementNextInvoiceNumber(Company $company, Invoice $invoice): bool
    {
        $template = $company->template;
        $template['invoice']['numbering']['upcoming'] = InvoiceHelper::getInvoiceNumberWithoutYear($invoice->number, $template['invoice']['numbering']['format'] ?? '') + 1;

        $company->template = json_encode($template, JSON_UNESCAPED_UNICODE);

        return $company->save();
    }

    public function incrementNextPreInvoiceNumber(Company $company, PreInvoice $preInvoice): bool
    {
        $template = $company->template;
        $template['preInvoice']['numbering']['upcoming'] = InvoiceHelper::getInvoiceNumberWithoutYear($preInvoice->number, $template['preInvoice']['numbering']['format'] ?? '') + 1;

        $company->template = json_encode($template, JSON_UNESCAPED_UNICODE);

        return $company->save();
    }

    /**
     * @throws ApiCompanyException
     */
    public function updateCompanyBasicInfo(string $companyUuid, UpdateCompanyBasicInfoRequest $request)
    {
        $companyToUpdate = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$companyToUpdate) {
            throw ApiCompanyException::companyNotFound();
        }

        $companyToUpdate->update(
            $request->only(
                'default',
                'name',
                'state',
                'street',
                'street_extra',
                'zip',
                'city',

                'tax_type',
                'identification_number', // ico
                'vat_identification_number', // dic
                'vat_identification_number_sk', // icdph

                'registry_info',

                'contact_name',
                'contact_phone',
                'contact_email',
                'contact_web',
            )
        );

        $default = $request->get('default');
        if ($default) {
            Auth::user()->companies()->where('id', '!=', $companyToUpdate->id)->update(['default' => false]);
        }

        return CompanyResource::make($companyToUpdate);
    }

    /**
     * @throws ApiCompanyException
     */
    public function updateCompanyTemplate(string $companyUuid, CompanySetTemplateRequest $request): CompanyResource
    {
        $companyToUpdate = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$companyToUpdate) {
            throw ApiCompanyException::companyNotFound();
        }

        $currentTemplate = $companyToUpdate->template;

        /* INVOICE */
        $currentTemplate['invoice']['template'] = $request->get('template');
        $currentTemplate['invoice']['primary_color'] = $request->get('primary_color');
        $currentTemplate['invoice']['currency'] = $request->get('currency');
        $currentTemplate['invoice']['language'] = $request->get('language');

        $requestInvoiceNumberingTemplate = $request->get('numbering');
        $currentTemplate['invoice']['numbering']['prefix'] = $requestInvoiceNumberingTemplate['prefix'] ?? '';
        $currentTemplate['invoice']['numbering']['upcoming'] = $requestInvoiceNumberingTemplate['upcoming'];
        $currentTemplate['invoice']['numbering']['format'] = $requestInvoiceNumberingTemplate['format'];
        $currentTemplate['invoice']['numbering']['due_date_additional_days'] = $requestInvoiceNumberingTemplate['due_date_additional_days'];

        $requestInvoiceFormatsTemplate = $request->get('formats');
        $currentTemplate['invoice']['formats']['date'] = $requestInvoiceFormatsTemplate['date'];
        $currentTemplate['invoice']['formats']['decimal'] = $requestInvoiceFormatsTemplate['decimal'];
        $currentTemplate['invoice']['formats']['thousands'] = $requestInvoiceFormatsTemplate['thousands'];

        $requestVisibilityTemplate = $request->get('visibility');
        $currentTemplate['invoice']['visibility']['due_date'] = $requestVisibilityTemplate['due_date'];
        $currentTemplate['invoice']['visibility']['send_date'] = $requestVisibilityTemplate['send_date'];
        $currentTemplate['invoice']['visibility']['quantity'] = $requestVisibilityTemplate['quantity'];
        $currentTemplate['invoice']['visibility']['payment'] = $requestVisibilityTemplate['payment'];
        $currentTemplate['invoice']['visibility']['qr_payment'] = $requestVisibilityTemplate['qr_payment'];

        $requestQrTemplate = $request->get('qr') ?? Company::getDefaultTemplate()['invoice']['qr'];
        $currentTemplate['invoice']['qr']['provider'] = $requestQrTemplate['provider'];

        /* PREINVOICE */
        $preInvoiceData = $request->get('preInvoice');
        $currentTemplate['preInvoice']['template'] = $preInvoiceData['template'] ?? Company::getDefaultTemplate()['preInvoice']['template'];
        $currentTemplate['preInvoice']['primary_color'] = $preInvoiceData['primary_color'] ?? Company::getDefaultTemplate()['preInvoice']['primary_color'];
        $currentTemplate['preInvoice']['currency'] = $preInvoiceData['currency'] ?? Company::getDefaultTemplate()['preInvoice']['currency'];
        $currentTemplate['preInvoice']['language'] = $preInvoiceData['language'] ?? Company::getDefaultTemplate()['preInvoice']['language'];

        $requestPreInvoiceNumberingTemplate = $preInvoiceData['numbering'] ?? Company::getDefaultTemplate()['preInvoice']['numbering'];
        $currentTemplate['preInvoice']['numbering']['prefix'] = $requestPreInvoiceNumberingTemplate['prefix'] ?? '';
        $currentTemplate['preInvoice']['numbering']['upcoming'] = $requestPreInvoiceNumberingTemplate['upcoming'];
        $currentTemplate['preInvoice']['numbering']['format'] = $requestPreInvoiceNumberingTemplate['format'];
        $currentTemplate['preInvoice']['numbering']['due_date_additional_days'] = $requestPreInvoiceNumberingTemplate['due_date_additional_days'];

        $requestPreInvoiceFormatsTemplate = $preInvoiceData['formats'] ?? Company::getDefaultTemplate()['preInvoice']['formats'];
        $currentTemplate['preInvoice']['formats']['date'] = $requestPreInvoiceFormatsTemplate['date'];
        $currentTemplate['preInvoice']['formats']['decimal'] = $requestPreInvoiceFormatsTemplate['decimal'];
        $currentTemplate['preInvoice']['formats']['thousands'] = $requestPreInvoiceFormatsTemplate['thousands'];

        $requestVisibilityTemplate = $preInvoiceData['visibility'] ?? Company::getDefaultTemplate()['preInvoice']['visibility'];
        $currentTemplate['preInvoice']['visibility']['due_date'] = $requestVisibilityTemplate['due_date'];
        $currentTemplate['preInvoice']['visibility']['send_date'] = $requestVisibilityTemplate['send_date'];
        $currentTemplate['preInvoice']['visibility']['quantity'] = $requestVisibilityTemplate['quantity'];
        $currentTemplate['preInvoice']['visibility']['payment'] = $requestVisibilityTemplate['payment'];
        $currentTemplate['preInvoice']['visibility']['qr_payment'] = $requestVisibilityTemplate['qr_payment'];

        $requestQrTemplate = $preInvoiceData['qr'] ?? Company::getDefaultTemplate()['preInvoice']['qr'];
        $currentTemplate['preInvoice']['qr']['provider'] = $requestQrTemplate['provider'];

        $companyToUpdate->template = json_encode($currentTemplate, JSON_UNESCAPED_UNICODE);
        $companyToUpdate->save();

        return CompanyResource::make($companyToUpdate);
    }

    /**
     * @throws ApiCompanyException
     */
    public function updateCompanyBankAccount(string $companyUuid, \App\Http\Requests\UpdateCompanyBankAccountRequest $request)
    {
        /** @var Company $companyToUpdate */
        $companyToUpdate = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$companyToUpdate) {
            throw ApiCompanyException::companyNotFound();
        }

        $companyPaymentMethods = $companyToUpdate->payment_methods;
        $companyPaymentMethods['bank_transfer'] = $request->get('bank_transfer');

        $companyToUpdate->update([
            'payment_methods' => json_encode($companyPaymentMethods, JSON_UNESCAPED_UNICODE),
        ]);

        return CompanyResource::make($companyToUpdate);
    }

}
