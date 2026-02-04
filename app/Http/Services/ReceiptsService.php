<?php

namespace App\Http\Services;

use App\Exceptions\Api\ApiCompanyException;
use App\Exceptions\Api\ApiInvoiceException;
use App\Exceptions\Api\ApiReceiptException;
use App\Http\Requests\CreateReceiptRequest;
use App\Http\Requests\EditReceiptRequest;
use App\Http\Resources\Api\ReceiptCollectionResource;
use App\Http\Resources\Api\ReceiptResource;
use App\Http\Utils\FileHelper;
use App\Http\Utils\PdfHelper;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptsService
{
    public function __construct(
        private InvoiceService $invoiceService,
    ) {
    }
    /**
     * @return array<Company, Receipt|null>
     * @throws ApiCompanyException
     */
    public function getLoggedUserCompanyAndReceipt(string $companyUuid, ?string $receiptUuid = null): array
    {
        /** @var Company $company */
        $company = Auth::user()->companies()->where('uuid', $companyUuid)->first();
        if (!$company) {
            throw ApiCompanyException::companyNotFound();
        }

        if (!$receiptUuid) {
            return [$company, null];
        }

        /** @var Receipt $receipt */
        $receipt = Receipt::where('uuid', $receiptUuid)
            ->where('user_id', Auth::id())
            ->where('company_id', $company->id)
            ->first();
        if (!$receipt) {
            throw new ApiReceiptException('Receipt not found', 404);
        }

        return [$company, $receipt];
    }

    /**
     * @throws ApiCompanyException
     */
    public function listReceipts(string $companyUuid): ReceiptCollectionResource
    {
        [$company, $_] = $this->getLoggedUserCompanyAndReceipt($companyUuid);

        return ReceiptCollectionResource::make(
            Auth::user()->receipts()->where('company_id', $company->id)->get()
        );
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function getReceipt(string $companyUuid, string $receiptUuid): ReceiptResource
    {
        [$company, $receipt] = $this->getLoggedUserCompanyAndReceipt($companyUuid, $receiptUuid);

        return ReceiptResource::make($receipt);
    }

    /**
     * @throws ApiCompanyException
     */
    public function createReceipt(string $companyUuid, CreateReceiptRequest $request): ReceiptResource
    {
        [$company, $_] = $this->getLoggedUserCompanyAndReceipt($companyUuid);

        $receipt = new Receipt();
        $receipt->uuid = Uuid::uuid4()->toString();
        $receipt->user_id = Auth::id();

        // Set company_id from the company UUID parameter
        $receipt->company_id = $company->id;

        $receipt->billed_to_client_id = $request->billed_to_client_id;
        $receipt->language_2_code = $request->language_2_code;
        $receipt->total = $request->total;
        $receipt->currency_3_code = $request->currency_3_code;
        $receipt->purpose = $request->purpose;
        $receipt->made_by = $request->made_by;
        $receipt->approved_by = $request->approved_by;
        $receipt->journal_number = $request->journal_number;
        $receipt->billing_regulation = $request->has('billing_regulation') ? json_encode($request->billing_regulation) : null;
        $receipt->date = $request->date;
        $receipt->save();

        // Generate PDF for the receipt
        $this->generateReceiptPdf($companyUuid, $receipt);

        return ReceiptResource::make($receipt);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function updateReceipt(string $companyUuid, string $receiptUuid, EditReceiptRequest $request): JsonResponse
    {
        [$company, $receipt] = $this->getLoggedUserCompanyAndReceipt($companyUuid, $receiptUuid);

        // Set company_id from the company UUID parameter
        $receipt->company_id = $company->id;

        $receipt->billed_to_client_id = $request->billed_to_client_id;
        $receipt->language_2_code = $request->language_2_code;
        $receipt->total = $request->total;
        $receipt->currency_3_code = $request->currency_3_code;
        $receipt->purpose = $request->purpose;
        $receipt->made_by = $request->made_by;
        $receipt->approved_by = $request->approved_by;
        $receipt->journal_number = $request->journal_number;
        $receipt->billing_regulation = $request->has('billing_regulation') ? json_encode($request->billing_regulation) : null;
        $receipt->date = $request->date;
        $receipt->save();

        // Generate PDF for the receipt
        $this->generateReceiptPdf($companyUuid, $receipt);

        return response()->json(['message' => 'Receipt updated successfully']);
    }

    /**
     * @throws ApiReceiptException
     * @throws ApiCompanyException
     */
    public function deleteReceipt(string $companyUuid, string $receiptUuid): JsonResponse
    {
        [$company, $receipt] = $this->getLoggedUserCompanyAndReceipt($companyUuid, $receiptUuid);

        $receipt->delete();

        return response()->json(['message' => 'Receipt deleted successfully']);
    }

    /**
     * Generates a PDF for the receipt
     *
     * @throws ApiCompanyException
     * @throws ApiReceiptException
     */
    public function generateReceiptPdf(string $companyUuid, Receipt|string $receiptOrReceiptUuid, Invoice|null $invoice): bool
    {
        [$company, $_] = $this->getLoggedUserCompanyAndReceipt($companyUuid);

        if ($receiptOrReceiptUuid instanceof Receipt) {
            $receipt = $receiptOrReceiptUuid;
        } else {
            $receipt = Receipt::where('uuid', $receiptOrReceiptUuid)->first();
            if (!$receipt) {
                throw new ApiReceiptException('Receipt not found', 404);
            }
        }

        if ($company->id !== $receipt->company_id) {
            throw new ApiReceiptException('Receipt not found', 404);
        }

        // set document language based on receipt settings
        $language2Code = $receipt->language_2_code ?? 'en';
        App::setLocale(mb_strtolower($language2Code));

        // convert total to human format (e.g., 2 -> "two") for the view
        try {
            $formatter = new \NumberFormatter(mb_strtolower($language2Code) ?: 'en', \NumberFormatter::SPELLOUT);
            $toalInHumanFormat = $formatter->format((int) round((float) ($receipt->total ?? 0)));
        } catch (\Throwable $e) {
            $toalInHumanFormat = (string) ($receipt->total ?? '0');
        }

        $toalInHumanFormat = str_replace(' ', '=', $toalInHumanFormat);
        $toalInHumanFormat = "===$toalInHumanFormat=".strtolower($receipt->currency_3_code)."===";

        // generate HTML
        $receiptBodyHtml = view('templates.receipts.Body', [
            'company' => $company,
            'receipt' => $receipt,
            'invoice' => $invoice,
            'toalInHumanFormat' => $toalInHumanFormat,
        ]);

        // store HTML to storage path
        [
            'body' => $bodyHtmlFilePath,
            'pdf' => $receiptPdfFilePath,
        ] = FileHelper::getDocumentReceiptResourceFilePathList(
            $company->user_id,
            $company->id,
            $receipt->id,
            $receipt->date
        );
        Storage::disk('local')->put($bodyHtmlFilePath, $receiptBodyHtml);

        $pdfGenerated = PdfHelper::generatePurePdf(
            $bodyHtmlFilePath,
            $receiptPdfFilePath,
            'landscape',
            'A4'
        );

        return $pdfGenerated;
    }

    /**
     * Returns a streamed response with the receipt PDF
     *
     * @throws ApiCompanyException
     * @throws ApiReceiptException
     */
    public function getReceiptPdf(string $companyUuid, string $receiptUuid): StreamedResponse
    {
        [$company, $receipt] = $this->getLoggedUserCompanyAndReceipt($companyUuid, $receiptUuid);

        try {
            // Check if the PDF exists, if not generate it
            [
                'pdf' => $receiptPdfFilePath,
            ] = FileHelper::getDocumentReceiptResourceFilePathList(
                $company->user_id,
                $company->id,
                $receipt->id,
                $receipt->date
            );

            if (!Storage::disk('local')->exists($receiptPdfFilePath)) {
                $this->generateReceiptPdf($companyUuid, $receipt);
            }

            return Storage::disk('local')->response($receiptPdfFilePath);
        } catch (\Throwable $e) {
            throw new ApiReceiptException('Receipt PDF not found', 404);
        }
    }

    /**
     * Creates a receipt associated with an invoice
     *
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function createInvoiceReceipt(string $companyUuid, string $invoiceUuid): ReceiptResource
    {
        // Get company and invoice
        [$company, $invoice] = $this->invoiceService->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        // Try to find an existing receipt for this invoice (scoped to user and company)
        $receipt = Receipt::query()
            ->where('user_id', Auth::id())
            ->where('company_id', $company->id)
            ->where('invoice_id', $invoice->id)
            ->first();

        $isNew = false;
        if (!$receipt) {
            $receipt = new Receipt();
            $receipt->uuid = Uuid::uuid4()->toString();
            $receipt->user_id = Auth::id();
            $receipt->company_id = $company->id;
            $receipt->invoice_id = $invoice->id;
            $isNew = true;
        }

        // Populate from Invoice/Company defaults
        $receipt->billed_to_client_id = $invoice->billed_to_client_id;
        $receipt->language_2_code = $invoice->language_2_code ?? ($company->language_2_code ?? 'en');
        $receipt->currency_3_code = $invoice->currency_3_code ?? ($company->currency_3_code ?? 'EUR');
        $receipt->total = method_exists($invoice, 'getTotalPriceWithTaxAndCashPaymentRounding')
            ? $invoice->getTotalPriceWithTaxAndCashPaymentRounding()
            : ($invoice->totalPrice_with_tax ?? 0);
        $receipt->purpose = 'Payment for invoice ' . $invoice->number;
        $receipt->made_by = $company->name ?? null;
        $receipt->approved_by = null;
        $receipt->journal_number = $invoice->variable_symbol ?? null;
        $receipt->billing_regulation = isset($company->billing_regulation) && $company->billing_regulation
            ? json_encode($company->billing_regulation)
            : null;
        // Only set date to today if creating new; otherwise keep existing date unless invoice changed it is desired
        if ($isNew || empty($receipt->date)) {
            $receipt->date = now()->toDateString();
        }

        $receipt->save();

        // Generate or refresh PDF for the receipt
        $this->generateReceiptPdf($companyUuid, $receipt, $invoice);

        return ReceiptResource::make($receipt);
    }

    /**
     * @throws ApiCompanyException
     * @throws ApiInvoiceException
     */
    public function getInvoiceReceipts(string $companyUuid, string $invoiceUuid): ReceiptCollectionResource
    {
        [$company, $invoice] = $this->invoiceService->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        $receipts = Receipt::query()
            ->where('user_id', Auth::id())
            ->where('company_id', $company->id)
            ->where('invoice_id', $invoice->id)
            ->get();

        return ReceiptCollectionResource::make($receipts);
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
        // Get company and invoice
        [$company, $invoice] = $this->invoiceService->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        // Get receipt
        $receipt = Receipt::where('uuid', $receiptUuid)
            ->where('user_id', Auth::id())
            ->where('company_id', $company->id)
            ->where('invoice_id', $invoice->id)
            ->first();

        if (!$receipt) {
            throw new ApiReceiptException('Receipt not found', 404);
        }

        // Delete receipt
        $receipt->delete();

        return response()->json(['message' => 'Receipt deleted successfully']);
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
        // Get company and invoice
        [$company, $invoice] = $this->invoiceService->getLoggedUserCompanyAndInvoice($companyUuid, $invoiceUuid);

        // Get receipt
        $receipt = $invoice->receipts()->first();

        if (!$receipt) {
            throw new ApiReceiptException('Receipt not found', 404);
        }

        try {
            // Check if the PDF exists, if not generate it
            [
                'pdf' => $receiptPdfFilePath,
            ] = FileHelper::getDocumentReceiptResourceFilePathList(
                $company->user_id,
                $company->id,
                $receipt->id,
                $receipt->date
            );

            if (!Storage::disk('local')->exists($receiptPdfFilePath)) {
                $this->generateReceiptPdf($companyUuid, $receipt);
            }

            return Storage::disk('local')->response($receiptPdfFilePath);
        } catch (\Throwable $e) {
            throw new ApiReceiptException('Receipt PDF not found', 404);
        }
    }
}
