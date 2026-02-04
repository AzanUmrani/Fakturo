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
use App\Http\Resources\Api\PreInvoiceCollectionResource;
use App\Http\Resources\Api\PreInvoiceHistoryCollectionResource;
use App\Http\Resources\Api\PreInvoiceResource;
use App\Http\Utils\BankHelper;
use App\Http\Utils\FileHelper;
use App\Http\Utils\PdfHelper;
use App\Models\Client;
use App\Models\Company;
use App\Models\InvoiceHistory;
use App\Models\PreInvoice;
use App\Models\PreInvoiceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreInvoiceService
{
    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function getPreInvoicePreviewStreamedResource(string $companyUuid, string $preInvoiceUuid, string $type = 'pdf'): StreamedResponse
    {
        [$company, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        try {
            return Storage::disk('local')->response(
                FileHelper::getDocumentResourceFilePathList(
                    'pre-invoices',
                    $company->user_id,
                    $company->id,
                    $preInvoice->id,
                    $preInvoice->billed_date
                )[$type]
            );
        } catch (\Throwable $e) {
            throw ApiInvoiceException::invoicePdfNotFound($type);
        }
    }
    /**
     * @return array{0: Company, 1: PreInvoice|null}
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getLoggedUserCompanyAndPreInvoice(string $companyUuid, ?string $preInvoiceUuid = null): array
    {
        /** @var Company $company */
        $company = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        if (!$preInvoiceUuid) {
            return [$company, null];
        }

        /** @var PreInvoice $preInvoice */
        $preInvoice = PreInvoice::where('company_id', $company->id)->where('uuid', $preInvoiceUuid)->first();
        if (!$preInvoice) {
            throw ApiInvoiceException::invoiceNotFound();
        }

        return [$company, $preInvoice];
    }

    /**
     * @throws ApiCompanyException
     */
    public function listPreInvoices(string $companyUuid): PreInvoiceCollectionResource
    {
        /** @var Company $company */
        [$company, $_] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid);

        return PreInvoiceCollectionResource::make(
            PreInvoice::where('company_id', $company->id)
                ->orderBy('number', 'desc')
                ->get()
        );
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function createPreInvoice(string $companyUuid, CreateInvoiceRequest $request): PreInvoiceResource
    {
        [$company, $_] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid);

        // get billed client
        /** @var Client|null $billedClient */
        $billedClient = Auth::user()->clients()->where('id', $request->get('billed_client_id'))->first();
        if (!$billedClient) {
            throw ApiInvoiceException::billedClientNotFound();
        }

        // check number uniqueness within company
        $preInvoiceNumber = $request->get('number');
        $numberExists = PreInvoice::where('company_id', $company->id)->where('number', $preInvoiceNumber)->first();
        if ($numberExists) {
            throw ApiInvoiceException::invoiceNumberAlreadyExists();
        }

        $preInvoice = PreInvoice::make([
            'uuid' => Uuid::uuid4()->toString(),
            'company_id' => $company->id,

            'prefix' => $company->template['preInvoice']['numbering']['prefix'] ?? '',
            'number' => $request->get('number'),
            'billed_date' => $request->get('billed_date'),
            'due_date' => $request->get('due_date'),
            'send_date' => $request->get('send_date'),

            'variable_symbol' => $request->get('variable_symbol'),
            'constant_symbol' => $request->get('constant_symbol'),
            'specific_symbol' => $request->get('specific_symbol'),

            'order_id' => $request->get('order_id'),

            'billed_from_client' => json_encode($company, JSON_UNESCAPED_UNICODE),
            'billed_to_client' => json_encode($billedClient, JSON_UNESCAPED_UNICODE),

            'items' => json_encode($request->get('items')),
            'bank_transfer' => json_encode($request->get('bank_transfer')),
            'payment' => $request->get('payment'),
            'note' => $request->get('note'),

            'totalPrice' => $request->get('totalPrice'),
            'cash_payment_rounding' => $request->get('cash_payment_rounding') ?? 0,

            'currency_3_code' => $request->get('currency_3_code'),
            'language_2_code' => $request->get('language_2_code'),

            'template' => $company->template['preInvoice']['template'],
            'template_primary_color' => $company->template['preInvoice']['primary_color'],
            'template_date_format' => $company->template['preInvoice']['formats']['date'],
            'template_price_decimal_format' => $company->template['preInvoice']['formats']['decimal'],
            'template_price_thousands_format' => $company->template['preInvoice']['formats']['thousands'] ?? '',

            'template_show_due_date' => $company->template['preInvoice']['visibility']['due_date'],
            'template_show_send_date' => $company->template['preInvoice']['visibility']['send_date'] ?? true,
            'template_show_quantity' => $company->template['preInvoice']['visibility']['quantity'],
            'template_show_payment' => $company->template['preInvoice']['visibility']['payment'],
            'template_show_qr_payment' => $company->template['preInvoice']['visibility']['qr_payment'],

            'qr_provider' => $company->template['preInvoice']['qr']['provider'] ?? QrCodeProvider::UNIVERSAL,

            'paid' => false,
            'sent' => false,
            'open' => false,
        ]);
        $preInvoice->save();

        // increment upcoming_number
        (new CompaniesService())->incrementNextPreInvoiceNumber($company, $preInvoice);

        // increment user's invoice count
        Auth::user()->increment('invoice_count');

        PreInvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'pre_invoice_id' => $preInvoice->id,
            'type' => InvoiceHistoryTypeEnum::Created,
        ]);

        /* generate new invoice */
        $this->generatePreInvoicePdf($companyUuid, $preInvoice, qrCodeProvider: QrCodeProvider::from($preInvoice->qr_provider));

        return PreInvoiceResource::make($preInvoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function getPreInvoice(string $companyUuid, string $preInvoiceUuid): PreInvoiceResource
    {
        [$_, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);
        return PreInvoiceResource::make($preInvoice);
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function editPreInvoice(string $companyUuid, string $preInvoiceUuid, EditInvoiceRequest $request): PreInvoiceResource
    {
        [$company, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        $billedClient = Auth::user()->clients()->where('id', $request->get('billed_client_id'))->first();
        if (!$billedClient) {
            throw ApiInvoiceException::billedClientNotFound();
        }

        $newData = [
            'prefix' => $company->template['preInvoice']['numbering']['prefix'] ?? '',
            'number' => $request->get('number'),
            'billed_date' => $request->get('billed_date'),
            'due_date' => $request->get('due_date'),
            'send_date' => $request->get('send_date'),

            'variable_symbol' => $request->get('variable_symbol'),
            'constant_symbol' => $request->get('constant_symbol'),
            'specific_symbol' => $request->get('specific_symbol'),

            'order_id' => $request->get('order_id'),

            'billed_from_client' => json_encode($company, JSON_UNESCAPED_UNICODE),
            'billed_to_client' => json_encode($billedClient, JSON_UNESCAPED_UNICODE),

            'items' => json_encode($request->get('items')),
            'bank_transfer' => json_encode($request->get('bank_transfer')),
            'payment' => $request->get('payment'),
            'note' => $request->get('note'),

            'totalPrice' => $request->get('totalPrice'),
            'cash_payment_rounding' => $request->get('cash_payment_rounding') ?? 0,

            'currency_3_code' => $request->get('currency_3_code'),
            'language_2_code' => $request->get('language_2_code'),

            'template' => $company->template['preInvoice']['template'],
            'template_primary_color' => $company->template['preInvoice']['primary_color'],
            'template_date_format' => $company->template['preInvoice']['formats']['date'],
            'template_price_decimal_format' => $company->template['preInvoice']['formats']['decimal'],
            'template_price_thousands_format' => $company->template['preInvoice']['formats']['thousands'],

            'template_show_due_date' => $company->template['preInvoice']['visibility']['due_date'],
            'template_show_send_date' => $company->template['preInvoice']['visibility']['send_date'] ?? true,
            'template_show_quantity' => $company->template['preInvoice']['visibility']['quantity'],
            'template_show_payment' => $company->template['preInvoice']['visibility']['payment'],
            'template_show_qr_payment' => $company->template['preInvoice']['visibility']['qr_payment'],

            'qr_provider' => $company->template['preInvoice']['qr']['provider'] ?? QrCodeProvider::UNIVERSAL,
        ];

        $preInvoice->update($newData);

        PreInvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'pre_invoice_id' => $preInvoice->id,
            'type' => InvoiceHistoryTypeEnum::Updated,
        ]);

        return PreInvoiceResource::make($preInvoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function deletePreInvoice(string $companyUuid, string $preInvoiceUuid): JsonResponse
    {
        [$_, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        $preInvoice->delete();

        return response()->json([
            'message' => 'COMPANY_PREINVOICE_DELETED',
        ]);
    }

    public function getPreInvoiceHistory(string $companyUuid, string $preInvoiceUuid): PreInvoiceHistoryCollectionResource
    {
        [$_, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        return PreInvoiceHistoryCollectionResource::make(
            $preInvoice->getHistory()->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changePaidStatus(string $companyUuid, string $preInvoiceUuid, ChangePaidStatusInvoiceRequest $request): PreInvoiceResource
    {
        [$_, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        $preInvoice->paid = $request->get('paid');
        $preInvoice->save();

        PreInvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'pre_invoice_id' => $preInvoice->id,
            'type' => $preInvoice->paid ? InvoiceHistoryTypeEnum::Paid : InvoiceHistoryTypeEnum::Unpaid,
        ]);

        return PreInvoiceResource::make($preInvoice);
    }

    /**
     * @throws ApiInvoiceException
     * @throws ApiCompanyException
     */
    public function changeSentStatus(string $companyUuid, string $preInvoiceUuid, ChangeSentStatusInvoiceRequest $request): PreInvoiceResource
    {
        [$_, $preInvoice] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid, $preInvoiceUuid);

        $preInvoice->sent = $request->get('sent');
        $preInvoice->save();

        PreInvoiceHistory::create([
            'uuid' => Uuid::uuid4()->toString(),
            'pre_invoice_id' => $preInvoice->id,
            'type' => InvoiceHistoryTypeEnum::Sent,
        ]);

        return PreInvoiceResource::make($preInvoice);
    }

    public function generatePreInvoicePdf(string $companyUuid, PreInvoice|string $preInvoiceOrInvoiceUuid, bool $preview = false, QrCodeProvider $qrCodeProvider = QrCodeProvider::UNIVERSAL): bool
    {
        [$company, $_] = $this->getLoggedUserCompanyAndPreInvoice($companyUuid);

        if ($preInvoiceOrInvoiceUuid instanceof PreInvoice) {
            $preInvoice = $preInvoiceOrInvoiceUuid;
        } else {
            $preInvoice = PreInvoice::where('uuid', $preInvoiceOrInvoiceUuid)->first();
            if (!$preInvoice) {
                throw ApiInvoiceException::invoiceNotFound();
            }
        }

        if ($company->id !== $preInvoice->company_id) {
            throw ApiInvoiceException::invoiceNotFound();
        }

        // set document language based on preInvoice settings
        App::setLocale(mb_strtolower($preInvoice->language_2_code ?? 'en'));

        // generate HTML
        $preInvoiceBodyHtml = view('templates.pre-invoices.'.$preInvoice->template.'.'.$preInvoice->template.'Body', [
            'preInvoice' => $preInvoice,
            'nationalBankNumber' => BankHelper::ibanToNationalAccount($preInvoice->bank_transfer['iban'] ?? ''),
            'qr' => $this->getQrCode($preInvoice, $qrCodeProvider),
        ]);
        $preInvoiceFooterHtml = view('templates.pre-invoices.'.$preInvoice->template.'.'.$preInvoice->template.'Footer', [
            'preInvoice' => $preInvoice,
            'nationalBankNumber' => BankHelper::ibanToNationalAccount($preInvoice->bank_transfer['iban'] ?? ''),
        ]);

        // store HTML to storage path
        [
            'body' => $bodyHtmlFilePath,
            'footer' => $footerHtmlFilePath,
            'isdoc' => $preInvoiceIsdocFilePath,
            'pdf' => $preInvoicePdfFilePath,
            'isdocPdf' => $preInvoiceIsdocPdfFilePath,
        ] = FileHelper::getDocumentResourceFilePathList(
            'pre-invoices',
            $company->user_id,
            $company->id,
            $preInvoice->id,
            $preInvoice->billed_date,
            $preview
        );
        Storage::disk('local')->put($bodyHtmlFilePath, $preInvoiceBodyHtml);
        Storage::disk('local')->put($footerHtmlFilePath, $preInvoiceFooterHtml);

        $pdfGenerated = PdfHelper::generatePdf(
            $bodyHtmlFilePath,
            $footerHtmlFilePath,
            $preInvoicePdfFilePath
        );

        /* not needed for previews */
        if ($preview === false && is_numeric($preInvoice->id) && $preInvoice->id > 0) {
            /* history */
            PreInvoiceHistory::create([
                'uuid' => Uuid::uuid4()->toString(),
                'pre_invoice_id' => $preInvoice->id,
                'type' => InvoiceHistoryTypeEnum::Regenerated,
            ]);
        }

        if ($pdfGenerated) {
            /* isdoc + isdocPdf */
            try {
                $isdocPdfGenerated = PdfHelper::generateIsdocPdf(
                    $preInvoice,
                    $preInvoicePdfFilePath,
                    $preInvoiceIsdocFilePath,
                    $preInvoiceIsdocPdfFilePath
                );
            } catch (\Throwable $e) {
                Log::error('ISDOC - '.$e->getMessage(), $e->getTrace());
            }
        }

        return $pdfGenerated;
    }

    public function getQrCode(PreInvoice $preInvoice, QrCodeProvider $qrCodeProvider): string
    {
        $iban = $preInvoice->bank_transfer['iban'] ?? '';
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return '';
        }

        $preInvoicePrice = !empty($preInvoice->totalPrice_with_tax) && $preInvoice->totalPrice_with_tax !== $preInvoice->totalPrice ? $preInvoice->totalPrice_with_tax : $preInvoice->totalPrice;

        $variableSymbol = $preInvoice->variable_symbol ?? '';
        $variableSymbol = preg_replace('/\D/', '', $variableSymbol);
        $variableSymbol = Str::substr($variableSymbol, 0, 10);

        $constantSymbol = $preInvoice->constant_symbol ?? '';
        $constantSymbol = preg_replace('/\D/', '', $constantSymbol);

        $specificSymbol = $preInvoice->specific_symbol ?? '';
        $specificSymbol = preg_replace('/\D/', '', $specificSymbol);

        $swift = $preInvoice->bank_transfer['swift'] ?? '';

        $preInvoiceState = $preInvoice->billed_from_client['state'] ?? '';
        $preInvoiceState = strtolower($preInvoiceState);
        if (empty($preInvoiceState)) {
            return '';
        }

        try {
            $qrcode = QrCodeService::getQrCode(
                $qrCodeProvider,
                $preInvoicePrice,
                $iban,
                $swift,
                $variableSymbol,
                $constantSymbol,
                $specificSymbol,
                $preInvoice->number ?? '',
                $preInvoice->currency_3_code,
                new \DateTime($preInvoice->due_date)
            );
            return '<img src="'.$qrcode.'" alt="QR">';
        } catch (\Exception $e) {
            return '';
        }
    }
}