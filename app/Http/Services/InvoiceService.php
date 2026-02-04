<?php

namespace App\Http\Services;

use App\Enums\InvoiceHistoryTypeEnum;
use App\Enums\QrCodeProvider;
use App\Exceptions\Api\ApiCompanyException;
use App\Exceptions\Api\ApiInvoiceException;
use App\Http\Requests\ChangePaidStatusInvoiceRequest;
use App\Http\Requests\ChangeSentStatusInvoiceRequest;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\EditInvoiceRequest;
use App\Http\Resources\Api\InvoiceCollectionResource;
use App\Http\Resources\Api\InvoiceHistoryCollectionResource;
use App\Http\Resources\Api\InvoiceResource;
use App\Http\Utils\BankHelper;
use App\Http\Utils\FileHelper;
use App\Http\Utils\InvoiceHelper;
use App\Http\Utils\PdfHelper;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\Receipt;
use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Gray;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\RendererStyle\Fill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Trinetus\PayBySquareGenerator\PayBySquareGenerator;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class InvoiceService
{

    const TMP_INVOICE_PREFIX = 'tmp_preview_';

    /**
     * @return array<Company, Invoice|null>
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getLoggedUserCompanyAndInvoice(string $companyUuid, ?string $invoiceUuid = null): array
    {
        /** @var Company $company */
        $company = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        if (!$invoiceUuid) {
            return [$company, null];
        }

        /** @var Invoice $invoice */
        $invoice = $company->invoices()->where('uuid', $invoiceUuid)->first();
        if (!$invoice) {
            throw ApiInvoiceException::invoiceNotFound();
        }

        return [$company, $invoice];
    }

    /**
     * @throws ApiCompanyException
     */
    public function listInvoices(string $companyUuid): InvoiceCollectionResource
    {
        /** @var Company $company */
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);

        return InvoiceCollectionResource::make(
            $company->invoices()
                ->orderBy('number', 'desc')
                ->get()
        );
    }


    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createInvoice(string $companyUuid, CreateInvoiceRequest $request): InvoiceResource
    {
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);

        // get billed client
        $billedClient = Auth::user()->clients()->where('id', $request->get('billed_client_id'))->first();
        if (!$billedClient) {
            throw ApiInvoiceException::billedClientNotFound();
        }

        /* check if same invoice number doesnt exists */
        $invoiceNumber = $request->get('number');
        $invoiceNumberExists = $company->invoices()->where('number', $invoiceNumber)->first();
        if ($invoiceNumberExists) {
            throw ApiInvoiceException::invoiceNumberAlreadyExists();
        }

        $newInvoice = $this->getInvoiceModelFromCreateInvoiceRequest($company, $billedClient, $request);
        $newInvoice->save();

        // increment upcoming_number
        (new CompaniesService())->incrementNextInvoiceNumber($company, $newInvoice);

        // increment user's invoice count
        Auth::user()->increment('invoice_count');

        // generate invoice PDF
        if (is_string($newInvoice->qr_provider)) {
            $qrProvider = QrCodeProvider::from($newInvoice->qr_provider);
        }
        $qrProvider = $qrProvider ?? QrCodeProvider::UNIVERSAL;
        $this->generateInvoicePdf($companyUuid, $newInvoice, qrCodeProvider: $qrProvider);

        return InvoiceResource::make($newInvoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getInvoice(string $companyUuid, string $invoiceUuid): InvoiceResource
    {
        [$_, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        return InvoiceResource::make($invoice);
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function editInvoice(string $companyUuid, string $invoiceUuid, EditInvoiceRequest $request): InvoiceResource
    {
        [$company, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        // get billed client
        $billedClient = Auth::user()->clients()->where('id', $request->get('billed_client_id'))->first();
        if (!$billedClient) {
            throw ApiInvoiceException::billedClientNotFound();
        }

        /* invoice */
        $newInvoiceData = [
            'prefix' => $company->template['invoice']['numbering']['prefix'] ?? '',
            'number' => $request->get('number'),
            'billed_date' => $request->get('billed_date'),
            'due_date' => $request->get('due_date'),
            'send_date' => $request->get('send_date'),

            'variable_symbol' => $request->get('variable_symbol'),
            'constant_symbol' => $request->get('constant_symbol'),
            'specific_symbol' => $request->get('specific_symbol'),

            'order_id' => $request->get('order_id'),

            'billed_from_client' => json_encode($company, JSON_UNESCAPED_UNICODE), // json
            'billed_to_client' => json_encode($billedClient, JSON_UNESCAPED_UNICODE), // json

            'items' => json_encode($request->get('items')), // json
            'bank_transfer' => json_encode($request->get('bank_transfer')), // json
            'payment' => $request->get('payment'),
            'note' => $request->get('note'),

            'totalPrice' => $request->get('totalPrice'),
            'totalPrice_with_tax' => null, /* TODO */
            'totalPrice_tax' => 0,
            'cash_payment_rounding' => $request->get('cash_payment_rounding') ?? 0,
            'tax_data' => null,

            'currency_3_code' => $request->get('currency_3_code'),
            'language_2_code' => $request->get('language_2_code'),

            // TODO ability to change in create/edit invoice screen ?
            'template' => $company->template['invoice']['template'],
            'template_primary_color' => $company->template['invoice']['primary_color'],
            'template_date_format' => $company->template['invoice']['formats']['date'],
            'template_price_decimal_format' => $company->template['invoice']['formats']['decimal'],
            'template_price_thousands_format' => $company->template['invoice']['formats']['thousands'],

            'template_show_due_date' => $company->template['invoice']['visibility']['due_date'],
            'template_show_send_date' => $company->template['invoice']['visibility']['send_date'] ?? true, /* TODO remove when apple aprove old app */
            'template_show_quantity' => $company->template['invoice']['visibility']['quantity'],
            'template_show_payment' => $company->template['invoice']['visibility']['payment'],
            'template_show_qr_payment' => $company->template['invoice']['visibility']['qr_payment'],

            'qr_provider' => $company->template['invoice']['qr']['provider'] ?? QrCodeProvider::UNIVERSAL,
        ];

        /* same clculations in edit */
        $recalculatedPrices = $this->getRecalculatedInvoicePrices(json_decode($newInvoiceData['items'], true) ?? []);

        $newInvoiceData['totalPrice'] = $recalculatedPrices['totalPrice'];
        $newInvoiceData['totalPrice_with_tax'] = $recalculatedPrices['totalPrice_with_tax'];
        $newInvoiceData['totalPrice_tax'] = $recalculatedPrices['totalTax'];
        $newInvoiceData['tax_data'] = !empty($recalculatedPrices['taxData']) ? json_encode($recalculatedPrices['taxData'], JSON_UNESCAPED_UNICODE) : null;

        $invoice->update($newInvoiceData);

        /* history */
        InvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'invoice_id' => $invoice->id,
            'type' => InvoiceHistoryTypeEnum::Updated,
        ]);

        /* generate new invoice */
//        $this->generateInvoicePdf($companyUuid, $invoice, qrCodeProvider: QrCodeProvider::from($invoice->qr_provider));
        $this->generateInvoicePdf($companyUuid, $invoice, qrCodeProvider: QrCodeProvider::UNIVERSAL);

        return InvoiceResource::make($invoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function deleteInvoice(string $companyUuid, string $invoiceUuid): JsonResponse
    {
        [$company, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        $deleted = $invoice->delete();
        if ($deleted) {
            [
                'body' => $bodyHtmlFilePath,
                'footer' => $footerHtmlFilePath,
                'isdoc' => $invoiceIsdocFilePath,
                'pdf' => $invoicePdfFilePath,
                'isdocPdf' => $invoiceIsdocPdfFilePath,
            ] = FileHelper::getDocumentResourceFilePathList(
                'invoices',
                $company->user_id,
                $company->id,
                $invoice->id,
                $invoice->billed_date
            );

            /* Isdoc */
            Storage::disk('local')->delete($invoiceIsdocFilePath);

            /* PDF */
            Storage::disk('local')->delete($invoicePdfFilePath);
//            Storage::disk('local')->delete($invoiceIsdocPdfFilePath); // TODO

            /* HTML */
            Storage::disk('local')->delete($bodyHtmlFilePath);
            Storage::disk('local')->delete($footerHtmlFilePath);
        }

        (new CompaniesService())->incrementNextInvoiceNumber($company, $invoice);

        return response()->json([
            'message' => 'COMPANY_INVOICE_DELETED',
        ]);
    }

    public function getInvoiceHistory(string $companyUuid, string $invoiceUuid): InvoiceHistoryCollectionResource
    {
        [$_, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        return InvoiceHistoryCollectionResource::make(
            $invoice->getInvoiceHistory()->orderBy('created_at', 'desc')->get()
        );
    }


    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function changePaidStatus(ChangePaidStatusInvoiceRequest $request, string $companyUuid, string $invoiceUuid): InvoiceResource
    {
        [$_, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        /* paid */
        $invoice->paid = $request->get('paid');
        $invoice->save();

        /* history */
        InvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'invoice_id' => $invoice->id,
            'type' => $invoice->paid ? InvoiceHistoryTypeEnum::Paid : InvoiceHistoryTypeEnum::Unpaid,
        ]);

        return InvoiceResource::make($invoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changeSentStatus(string $companyUuid, string $invoiceUuid, ChangeSentStatusInvoiceRequest $request): InvoiceResource
    {
        [$_, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        /* sent */
        $invoice->sent = $request->get('sent');
        $invoice->save();

        /* history */
        InvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'invoice_id' => $invoice->id,
            'type' => InvoiceHistoryTypeEnum::Sent,
        ]);

        return InvoiceResource::make($invoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createInvoicePreview(string $companyUuid, CreateInvoiceRequest $request)
    {
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);
        $billedClient = Auth::user()->clients()->where('id', $request->get('billed_client_id'))->first();
        if (!$billedClient) {
            throw ApiInvoiceException::billedClientNotFound();
        }

        $tmpInvoice = $this->getInvoiceModelFromCreateInvoiceRequest($company, $billedClient, $request);
        $tmpInvoice->id = 0; // we not storing it! /* TODO problem when more users withing one account create preview */

        $generatedInvoice = $this->generateInvoicePdf($companyUuid, $tmpInvoice, true, qrCodeProvider: QrCodeProvider::from($tmpInvoice->qr_provider));
        if (!$generatedInvoice) {
            throw ApiInvoiceException::invoicePdfNotGenerated();
        }

        return response()->json([
            'message' => 'COMPANY_INVOICE_PREVIEW_CREATED',
        ]);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getFutureInvoicePreviewStreamedResource(string $companyUuid): StreamedResponse
    {
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);

        try {
            return Storage::disk('local')->response(
                FileHelper::getFutureInvoicePdfPath(
                    $company->user_id,
                    $company->id
                )
            );
        } catch (\Throwable $e) {
            throw ApiInvoiceException::invoicePdfNotFound('pdf');
        }
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function getInvoicePreviewStreamedResource(string $companyUuid, string $invoiceUuid, string $type): StreamedResponse
    {
        [$company, $invoice] = $this->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        try {
            return Storage::disk('local')->response(
                FileHelper::getDocumentResourceFilePathList(
                    'invoices',
                    $company->user_id,
                    $company->id,
                    $invoice->id,
                    $invoice->billed_date
                )[$type]
            );
        } catch (\Throwable $e) {
            $this->generateInvoicePdf($companyUuid, $invoice);
            throw ApiInvoiceException::invoicePdfNotFound($type);
        }
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getInvoicesBulkZip(string $companyUuid, Request $request): StreamedResponse
    {
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);

        $invoiceUuidList = $request->get('invoiceUuidList') ?? '';
        $invoiceUuidList = explode(',', $invoiceUuidList);


        $zip = new \ZipArchive();
        $zippppp = FileHelper::getInvoicesBulkZipFilePath(
            $company->user_id,
            $company->id
        );

        Storage::disk('local')->makeDirectory($zippppp['dirPath']);
        $tmpZipFilePath = Storage::disk('local')->path($zippppp['fullPath']);

        if ($zip->open($tmpZipFilePath, \ZipArchive::CREATE) === true) {
            $missingFilesList = [];
            $notAddedFilesList = [];
            $chunkedInvoiceUuidList = array_chunk($invoiceUuidList, 50);

            foreach ($chunkedInvoiceUuidList as $chunkedInvoiceUuidList) {
                $invoiceList = Invoice::whereIn('uuid', $chunkedInvoiceUuidList)->get();
                foreach ($invoiceList as $invoice) {
                    [
                        'pdf' => $invoicePdfFilePath,
                        'isdocPdf' => $invoiceIsdocPdfFilePath, /* TODO */
                    ] = FileHelper::getDocumentResourceFilePathList(
                        'invoices',
                        $company->user_id,
                        $company->id,
                        $invoice->id,
                        $invoice->billed_date
                    );

                    /* check if file exists */
                    if (!Storage::disk('local')->exists($invoicePdfFilePath)) {
                        $missingFilesList[] = basename($invoicePdfFilePath);
                        $this->generateInvoicePdf($companyUuid, $invoice, qrCodeProvider: QrCodeProvider::from($invoice->qr_provider));
                    }

                    // Add the PDF file to the ZIP file
                    $added = $zip->addFile(Storage::path($invoicePdfFilePath), basename($invoicePdfFilePath));
                    if (!$added) {
                        $notAddedFilesList[] = basename($invoicePdfFilePath);
                    }
                }
            }

            if ($missingFilesList) {
                $missingFilesList[] = 'These files were generated automatically, because they were missing.';
                $missingFilesList[] = '';
                $zip->addFromString('force_create_missing_invoice_list.txt', implode("\n", $missingFilesList));
            }

            if ($notAddedFilesList) {
                $notAddedFilesList[] = 'These files were not added to ZIP.';
                $notAddedFilesList[] = '';
                $zip->addFromString('invoice_error_list.txt', implode("\n", $notAddedFilesList));
            }

            // Close the ZIP file
            $zipClosed = $zip->close();

            if ($zipClosed) {
                return Storage::disk('local')->response($zippppp['fullPath']);
            }
        }

        throw ApiInvoiceException::invoiceZipNotGenerated();
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function generateInvoicePdf(string $companyUuid, Invoice|string $invoiceOrInvoiceUuid, bool $preview = false, QrCodeProvider $qrCodeProvider = QrCodeProvider::UNIVERSAL): bool
    {
        [$company, $_] = $this->getLoggedUserCompanyAndInvoice($companyUuid);

        if ($invoiceOrInvoiceUuid instanceof Invoice) {
            $invoice = $invoiceOrInvoiceUuid;
        } else {
            $invoice = Invoice::where('uuid', $invoiceOrInvoiceUuid)->first();
            if (!$invoice) {
                throw ApiInvoiceException::invoiceNotFound();
            }
        }

        if ($company->id !== $invoice->company_id) {
            throw ApiInvoiceException::invoiceNotFound();
        }

        // set document language based on invoice settings
        App::setLocale(mb_strtolower($invoice->language_2_code ?? 'en'));

        // generate HTML
        $invoiceBodyHtml = view('templates.invoices.'.$invoice->template.'.'.$invoice->template.'Body', [
            'invoice' => $invoice,
            'nationalBankNumber' => BankHelper::ibanToNationalAccount($invoice->bank_transfer['iban'] ?? ''),
            'qr' => $this->getQrCode($invoice, $qrCodeProvider),
        ]);
        $invoiceFooterHtml = view('templates.invoices.'.$invoice->template.'.'.$invoice->template.'Footer', [
            'invoice' => $invoice,
            'nationalBankNumber' => BankHelper::ibanToNationalAccount($invoice->bank_transfer['iban'] ?? ''),
        ]);

        // store HTML to storage path
        [
            'body' => $bodyHtmlFilePath,
            'footer' => $footerHtmlFilePath,
            'isdoc' => $invoiceIsdocFilePath,
            'pdf' => $invoicePdfFilePath,
            'isdocPdf' => $invoiceIsdocPdfFilePath,
        ] = FileHelper::getDocumentResourceFilePathList(
            'invoices',
            $company->user_id,
            $company->id,
            $invoice->id,
            $invoice->billed_date,
            $preview
        );
        Storage::disk('local')->put($bodyHtmlFilePath, $invoiceBodyHtml);
        Storage::disk('local')->put($footerHtmlFilePath, $invoiceFooterHtml);

        $pdfGenerated = PdfHelper::generatePdf(
            $bodyHtmlFilePath,
            $footerHtmlFilePath,
            $invoicePdfFilePath
        );

        /* not needed for previews */
        if ($preview === false && is_numeric($invoice->id) && $invoice->id > 0) {
            /* history */
            InvoiceHistory::create([
                'uuid' => Uuid::uuid4()->toString(),
                'invoice_id' => $invoice->id,
                'type' => InvoiceHistoryTypeEnum::Regenerated,
            ]);
        }

        if ($pdfGenerated) {
            /* isdoc + isdocPdf */
            try {
                $isdocPdfGenerated = PdfHelper::generateIsdocPdf(
                    $invoice,
                    $invoicePdfFilePath,
                    $invoiceIsdocFilePath,
                    $invoiceIsdocPdfFilePath
                );
            } catch (\Throwable $e) {
                Log::error('ISDOC - '.$e->getMessage(), $e->getTrace());
            }
        }

        return $pdfGenerated;
    }

    public function getQrCode(Invoice $invoice, QrCodeProvider $qrCodeProvider): string
    {
        $iban = $invoice->bank_transfer['iban'] ?? '';
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return '';
        }

        $invoicePrice = !empty($invoice->totalPrice_with_tax) && $invoice->totalPrice_with_tax !== $invoice->totalPrice ? $invoice->totalPrice_with_tax : $invoice->totalPrice;

        $variableSymbol = $invoice->variable_symbol ?? '';
        $variableSymbol = preg_replace('/\D/', '', $variableSymbol);
        $variableSymbol = Str::substr($variableSymbol, 0, 10);

        $constantSymbol = $invoice->constant_symbol ?? '';
        $constantSymbol = preg_replace('/\D/', '', $constantSymbol);

        $specificSymbol = $invoice->specific_symbol ?? '';
        $specificSymbol = preg_replace('/\D/', '', $specificSymbol);

        $swift = $invoice->bank_transfer['swift'] ?? '';

        $invoiceState = $invoice->billed_from_client['state'] ?? '';
        $invoiceState = strtolower($invoiceState);
        if (empty($invoiceState)) {
            return '';
        }

        try {
            $qrcode = QrCodeService::getQrCode(
                $qrCodeProvider,
                $invoicePrice,
                $iban,
                $swift,
                $variableSymbol,
                $constantSymbol,
                $specificSymbol,
                $invoice->number ?? '',
                $invoice->currency_3_code,
                new \DateTime($invoice->due_date)
            );
            return '<img src="'.$qrcode.'" alt="QR">';
        } catch (\Exception $e) {
            return '';
        }
    }


    private function getInvoiceModelFromCreateInvoiceRequest(Company $company, Client $billedClient, CreateInvoiceRequest $request): Invoice
    {
        $newInvoice = Invoice::make([
            'uuid' => Uuid::uuid4()->toString(),
            'company_id' => $company->id,

            'prefix' => $company->template['invoice']['numbering']['prefix'] ?? '',
            'number' => $request->get('number'),
            'billed_date' => $request->get('billed_date'),
            'due_date' => $request->get('due_date'),
            'send_date' => $request->get('send_date'),

            'variable_symbol' => $request->get('variable_symbol'),
            'constant_symbol' => $request->get('constant_symbol'),
            'specific_symbol' => $request->get('specific_symbol'),

            'order_id' => $request->get('order_id'),

            'billed_from_client' => json_encode($company, JSON_UNESCAPED_UNICODE), // json
            'billed_to_client' => json_encode($billedClient, JSON_UNESCAPED_UNICODE), // json

            'items' => json_encode($request->get('items')), // json
            'bank_transfer' => json_encode($request->get('bank_transfer')), // json
            'payment' => $request->get('payment'),
            'note' => $request->get('note'),

            'totalPrice' => $request->get('totalPrice'),
            'totalPrice_with_tax' => null, /* TODO */
            'totalPrice_tax' => 0,
            'cash_payment_rounding' => $request->get('cash_payment_rounding') ?? 0,
            'tax_data' => null,

            'currency_3_code' => $request->get('currency_3_code'),
            'language_2_code' => $request->get('language_2_code'),

            // TODO ability to change in create/edit invoice screen ?
            'template' => $company->template['invoice']['template'],
            'template_primary_color' => $company->template['invoice']['primary_color'],
            'template_date_format' => $company->template['invoice']['formats']['date'],
            'template_price_decimal_format' => $company->template['invoice']['formats']['decimal'],
            'template_price_thousands_format' => $company->template['invoice']['formats']['thousands'],

            'template_show_due_date' => $company->template['invoice']['visibility']['due_date'],
            'template_show_send_date' => $company->template['invoice']['visibility']['send_date'] ?? true, /* TODO remove when apple aprove old app */
            'template_show_quantity' => $company->template['invoice']['visibility']['quantity'],
            'template_show_payment' => $company->template['invoice']['visibility']['payment'],
            'template_show_qr_payment' => $company->template['invoice']['visibility']['qr_payment'],

            'qr_provider' => $company->template['invoice']['qr']['provider'] ?? QrCodeProvider::UNIVERSAL,

            'paid' => false,
            'sent' => false,
            'open' => false,
        ]);

        /* same clculations in edit */
        $recalculatedPrices = $this->getRecalculatedInvoicePrices($newInvoice->items);

        $newInvoice->totalPrice = $recalculatedPrices['totalPrice'];
        $newInvoice->totalPrice_with_tax = $recalculatedPrices['totalPrice_with_tax'];
        $newInvoice->totalPrice_tax = $recalculatedPrices['totalTax'];
        $newInvoice->tax_data = !empty($recalculatedPrices['taxData']) ? json_encode($recalculatedPrices['taxData'], JSON_UNESCAPED_UNICODE) : null;

        return $newInvoice;
    }

    private function getRecalculatedInvoicePrices(array $invoiceItems): array
    {
        $priceList = [
            'totalPrice' => 0, // without tax
            'totalPrice_with_tax' => 0, // with tax
            'totalTax' => 0,
            'taxData' => [], // [taxRate => taxValue]
        ];

        foreach ($invoiceItems as $item) {
            $taxRate = isset($item['taxRate']) ? (1 + $item['taxRate'] / 100) : 1;
            $itemPrice = $item['price'] * $item['quantity'];

            $priceList['totalPrice'] += $itemPrice;
            $priceList['totalPrice_with_tax'] += $itemPrice * $taxRate;
            if (isset($item['taxRate'])) {
                $priceList['taxData'][$item['taxRate']] = ($priceList['taxData'][$item['taxRate']] ?? 0) + ($itemPrice * ($taxRate - 1));
            }
        }

        $priceList['totalTax'] = array_sum($priceList['taxData']);

        return $priceList;
    }
}