<?php

namespace App\Http\Services;

use App\Enums\InvoiceHistoryTypeEnum;
use App\Exceptions\Api\ApiClientException;
use App\Exceptions\Api\ApiCompanyException;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\CreateClientStatementRequest;
use App\Http\Requests\EditClientRequest;
use App\Http\Resources\Api\ClientResource;
use App\Http\Resources\Api\InvoiceCollectionResource;
use App\Http\Utils\FileHelper;
use App\Http\Utils\PdfHelper;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientsService
{

    /**
     * @throws ApiClientException
     */
    public function getClient(string $clientUuid): ClientResource
    {
        $loggedUser = Auth::user();

        $foundClient = Client::where('uuid', $clientUuid)
            ->where('user_id', $loggedUser->id)
            ->first();

        if (!$foundClient) {
            throw ApiClientException::clientNotFound();
        }

//        $invoices = Invoice::whereJsonContains('billed_to_client', ['id' => $foundClient->id])->get();
        $invoices = Invoice::where('billed_to_client_id', $foundClient->id)->get(); // faster ? TODO test with more data
        $invoicesMetaData = [
            'count' => [
                'total' => 0,
                'paid' => 0,
                'unpaid' => 0,
            ],
            'amount' => [],// [currency_3_code => [total, paid, unpaid], ...]
        ];

        foreach ($invoices as $invoice) {
            $invoicesMetaData['count']['total']++;
            if ($invoice->paid) {
                $invoicesMetaData['count']['paid']++;
            } else {
                $invoicesMetaData['count']['unpaid']++;
            }

            if (!isset($invoicesMetaData['amount'][$invoice->currency_3_code])) {
                $invoicesMetaData['amount'][$invoice->currency_3_code] = [
                    'total' => 0,
                    'paid' => 0,
                    'unpaid' => 0,
                ];
            }

            $invoicesMetaData['amount'][$invoice->currency_3_code]['total'] += $invoice->totalPrice;
            if ($invoice->paid) {
                $invoicesMetaData['amount'][$invoice->currency_3_code]['paid'] += $invoice->totalPrice;
            } else {
                $invoicesMetaData['amount'][$invoice->currency_3_code]['unpaid'] += $invoice->totalPrice;
            }
        }

        $foundClient->invoicesMetaData = $invoicesMetaData;

        return ClientResource::make($foundClient);
    }

    public function createClient(CreateClientRequest $request): ClientResource
    {
        $loggedUser = Auth::user();

        $newClient = Client::create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $loggedUser->id,

            'name' => $request->get('name'),
            'state' => $request->get('state'),
            'street' => $request->get('street'),
            'street_extra' => $request->get('street_extra'),
            'zip' => $request->get('zip'),
            'city' => $request->get('city'),

            'identification_number' => $request->get('identification_number') ?? '', // ico
            'vat_identification_number' => $request->get('vat_identification_number') ?? '', // dic
            'vat_identification_number_sk' => $request->get('vat_identification_number_sk'), // icdph

            'registry_info' => $request->get('registry_info'),

            'contact_name' => $request->get('contact_name'),
            'contact_phone' => $request->get('contact_phone'),
            'contact_email' => $request->get('contact_email'),
            'contact_web' => $request->get('contact_web'),
        ]);

        return ClientResource::make($newClient);
    }

    /**
     * @throws ApiClientException
     */
    public function updateClient(string $clientUuid, EditClientRequest $request): JsonResponse
    {
        $loggedUser = Auth::user();

        $clientToUpdate = Client::where('uuid', $clientUuid)
            ->where('user_id', $loggedUser->id)
            ->first();

        if (!$clientToUpdate) {
            throw ApiClientException::clientNotFound();
        }

        $requestOnly = $request->only(
            'name',
            'state',
            'street',
            'street_extra',
            'zip',
            'city',

            'identification_number', // ico
            'vat_identification_number', // dic
            'vat_identification_number_sk', // icdph

            'registry_info',

            'contact_name',
            'contact_phone',
            'contact_email',
            'contact_web',
        );

        if (empty($requestOnly['identification_number'])) {
            $requestOnly['identification_number'] = '';
        }

        if (empty($requestOnly['vat_identification_number'])) {
            $requestOnly['vat_identification_number'] = '';
        }

        $clientToUpdate->update($requestOnly);

        return response()->json([
            'message' => 'CLIENT_UPDATED',
        ]);
    }

    /**
     * @throws ApiClientException
     */
    public function deleteClient(string $clientUuid): JsonResponse
    {
        $loggedUser = Auth::user();

        $clientToDelete = Client::where('uuid', $clientUuid)
            ->where('user_id', $loggedUser->id)
            ->first();

        if (!$clientToDelete) {
            throw ApiClientException::clientNotFound();
        }

        $clientToDelete->delete();

        return response()->json([
            'message' => 'CLIENT_DELETED',
        ]);
    }

    /**
     * @throws ApiClientException
     */
    public function getInvoices(string $clientUuid): InvoiceCollectionResource
    {
        $clientId =  Client::where('uuid', $clientUuid)->first()->id;

        if (!$clientId) {
            throw ApiClientException::clientNotFound();
        }

        return InvoiceCollectionResource::make(
            Invoice::where('billed_to_client_id', $clientId)->get()
        );
    }

    public function createAndGetStatement(string $clientUuid, CreateClientStatementRequest $request): StreamedResponse
    {
        /* TODO unoptimal */
        $createStatementResult = $this->createClientStatement($clientUuid, $request);
        return $this->getExistingClientStatement($clientUuid, $request);
    }

    /**
     * @throws ApiClientException
     * @throws ApiCompanyException
     */
    public function getExistingClientStatement(string $clientUuid, CreateClientStatementRequest $request): StreamedResponse
    {
        $loggedUser = Auth::user();

        $client = Client::where('uuid', $clientUuid)
            ->where('user_id', $loggedUser->id)
            ->first();

        if (!$client) {
            throw ApiClientException::clientNotFound();
        }

        $company = $loggedUser->companies()->where('default', true)->first();

        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        ['body' => $bodyHtmlFilePath, 'footer' => $footerHtmlFilePath, 'pdf' => $pdfFilePath] = FileHelper::getClientStatementFilePathList(
            $company->user_id,
            $company->id,
            $client->id,
            $request->get('fromDate'),
            $request->get('toDate'),
            $request->get('onlyUnpaidInvoices')
        );

        // check if pdf exists
        if (!Storage::disk('local')->exists($pdfFilePath)) {
            throw ApiClientException::clientStatementPdfNotGenerated();
        }

        return Storage::disk('local')->response($pdfFilePath);
    }

    /**
     * @throws ApiClientException
     * @throws ApiCompanyException
     */
    public function createClientStatement(string $clientUuid, CreateClientStatementRequest $request): JsonResponse
    {
        $loggedUser = Auth::user();

        $client = Client::where('uuid', $clientUuid)
            ->where('user_id', $loggedUser->id)
            ->first();

        if (!$client) {
            throw ApiClientException::clientNotFound();
        }

        $company = $loggedUser->companies()->where('default', true)->first();

        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        /* get all client invoices */
        $invoices = Invoice::where('billed_to_client_id', $client->id)
            ->where('billed_date', '>=', $request->get('fromDate'))
            ->where('billed_date', '<=', $request->get('toDate'));

        if ($request->get('onlyUnpaidInvoices') == 1) {
            $invoices = $invoices->where('paid', 0);
        }

        $invoices = $invoices->get();

        if (empty($invoices)) {
            throw ApiClientException::clientHasNoInvoices();
        }

        $invoiceList = [
            'data' => [], // [currency_3_code => [invoice, ...], ...]
            'metaData' => [], // [currency_3_code => [total, paid, unpaid], ...]
        ];
        foreach ($invoices as $invoice) {
            if (!isset($invoiceList['data'][$invoice->currency_3_code])) {
                $invoiceList['data'][$invoice->currency_3_code] = [];
                $invoiceList['metaData'][$invoice->currency_3_code] = [
                    'total' => 0,
                    'paid' => 0,
                    'unpaid' => 0,
                ];
            }

            $price = !empty($invoice->totalPrice_with_tax) ? $invoice->totalPrice_with_tax : $invoice->totalPrice;

            $invoiceList['metaData'][$invoice->currency_3_code]['total'] += $price;
            if ($invoice->paid && !$request->get('onlyUnpaidInvoices')) {
                $invoiceList['metaData'][$invoice->currency_3_code]['paid'] += $price;
            } else {
                $invoiceList['metaData'][$invoice->currency_3_code]['unpaid'] += $price;
            }

            $invoiceList['data'][$invoice->currency_3_code][] = $invoice;
        }
        unset($invoices);

        // set document language based on invoice settings
        App::setLocale(mb_strtolower($company->template['invoice']['language'] ?? 'en'));

        /* generate html content */
        $generatedHtmlResult = $this->generateStatementHtmlContent(
            $invoiceList,
            $company,
            $client,
            $request->get('fromDate'),
            $request->get('toDate'),
            $request->get('onlyUnpaidInvoices')
        );

        /* generate pdf content */
        $pdfSaved = PdfHelper::generatePdf(
            $generatedHtmlResult['filePathList']['body'],
            $generatedHtmlResult['filePathList']['footer'],
            $generatedHtmlResult['filePathList']['pdf']
        );

        if (!$pdfSaved) {
            throw ApiClientException::clientStatementPdfNotGenerated();
        }

        return response()->json([
            'message' => 'CLIENT_STATEMENT_CREATED',
        ]);
    }


    private function generateStatementHtmlContent(array $invoiceList, Company $company, Client $client, string $fromDate, string $toDate, bool $onlyUnpaidInvoices): array
    {
        /* file path */
        ['body' => $bodyHtmlFilePath, 'footer' => $footerHtmlFilePath, 'pdf' => $pdfFilePath] = FileHelper::getClientStatementFilePathList(
            $company->user_id,
            $company->id,
            $client->id,
            $fromDate,
            $toDate,
            $onlyUnpaidInvoices
        );

        /* blade template data */
        $bladeData = [
            'billedFromCompany' => $company,
            'client' => $client,

            'template' => $company->template['invoice'],
            'invoiceList' => $invoiceList,

            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ];

        $templateName = $company->template['invoice']['template'] ?? 'Kronos';

        /* creating view */
        $statementBodyHtmlContent = view('templates.statements.'.$templateName.'.'.$templateName.'Body', $bladeData);
        $statementFooterHtmlContent = view('templates.statements.'.$templateName.'.'.$templateName.'Footer', $bladeData);

        /* saving to html files */
        $createdbodyHtml = Storage::disk('local')->put($bodyHtmlFilePath, $statementBodyHtmlContent);
        $createdFooterHtml = Storage::disk('local')->put($footerHtmlFilePath, $statementFooterHtmlContent);

        return [
            'generated' => $createdbodyHtml && $createdFooterHtml,
            'filePathList' => [
                'body' => $bodyHtmlFilePath,
                'footer' => $footerHtmlFilePath,
                'pdf' => $pdfFilePath
            ]
        ];
    }
}
