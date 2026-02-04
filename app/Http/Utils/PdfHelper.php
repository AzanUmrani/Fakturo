<?php

namespace App\Http\Utils;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Isdoc\Enums\DocumentType;
use Isdoc\Enums\PaymentMeansCode;
use Isdoc\Enums\TaxScheme;
use Isdoc\Models\BankAccount;
use Isdoc\Models\ClassifiedTaxCategory;
use Isdoc\Models\Country;
use Isdoc\Models\InvoiceLine;
use Isdoc\Models\Item;
use Isdoc\Models\PartyContact;
use Isdoc\Models\PartyIdentification;
use Isdoc\Models\Payment;
use Isdoc\Models\PaymentDetailBankTransaction;
use Isdoc\Models\PaymentDetailCash;
use Isdoc\Models\PaymentDetails;
use Isdoc\Models\PaymentMeans;
use Isdoc\Models\PostalAddress;
use Ramsey\Uuid\Uuid;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfHelper
{
    public static function generatePdf(string $bodyHtmlContentFilePath, string $footerHtmlContentFilePath, string $pdfDestinationFilePath): bool
    {
        /* wkhtmltopdf wrapper */
        $pdf = new \mikehaertl\wkhtmlto\Pdf([
            'load-error-handling' => 'ignore',
            'ignoreWarnings' => true,
            'enable-javascript',
            'dpi' => '300',
            'no-outline',
            'margin-bottom' => '24',
            'margin-top' => '0',
            'margin-left' => '0',
            'margin-right' => '0',

            'footer-html' => Storage::path($footerHtmlContentFilePath),
        ]);
        $pdf->addpage(Storage::path($bodyHtmlContentFilePath));
        $generatedPdf = $pdf->saveAs(Storage::path($pdfDestinationFilePath));

        if ($generatedPdf) {
            Storage::delete($bodyHtmlContentFilePath);
            Storage::delete($footerHtmlContentFilePath);
        }

        return $generatedPdf;
    }

    public static function generatePurePdf(string $bodyHtmlContentFilePath, string $pdfDestinationFilePath, string $orientation = 'portrait', string $pageSize = 'A4')
    {
        /* wkhtmltopdf wrapper */
        $pdf = new \mikehaertl\wkhtmlto\Pdf([
            'load-error-handling' => 'ignore',
            'ignoreWarnings' => true,
            'enable-javascript',
            'dpi' => '300',
            'no-outline',
            'margin-bottom' => '0',
            'margin-top' => '0',
            'margin-left' => '0',
            'margin-right' => '0',
            'orientation' => $orientation,
            'page-size' => $pageSize,
            'disable-smart-shrinking',
        ]);
        $pdf->addpage(Storage::path($bodyHtmlContentFilePath));
        $generatedPdf = $pdf->saveAs(Storage::path($pdfDestinationFilePath));

        if ($generatedPdf) {
            Storage::delete($bodyHtmlContentFilePath);
        }

        return $generatedPdf;
    }

    public static function generateIsdoc(Invoice $invoice, string $isdocDestinationFilePath): bool
    {
        $taxDocument = new \Isdoc\Models\Invoice();

        $correctDate = function ($date) {
            $dateTime = new \DateTime($date);
            return $dateTime->format('Y-m-d');
        };

        /* basic data */
        $taxDocument->setId($invoice->number);
        $taxDocument->setDocumentType(DocumentType::INVOICE);
        $taxDocument->setUuid($invoice->uuid);
        $taxDocument->setIssuingSystem('Fakturo.app '.date('Y-m-d'));
        $taxDocument->setIssueDate($correctDate($invoice->billed_date));
        $taxDocument->setTaxPointDate($correctDate($invoice->due_date));
        $taxDocument->setVATApplicable(!empty($invoice->totalPrice_tax));
        $taxDocument->setNote($invoice->note);
        $taxDocument->setExchangeRate(
            (new \Isdoc\Models\ExchangeRate($invoice->currency_3_code))
        );

        /* billed from */
        $billedFromClientCountryCode = defined("\Isdoc\Enums\CountryCode::{$invoice->billed_from_client['state']}") ? constant("\Isdoc\Enums\CountryCode::{$invoice->billed_from_client['state']}") : null;
        if (null === $billedFromClientCountryCode) {
            return false;
        }
        $billedFromVatIdentification = $invoice->billed_from_client['vat_identification_number'] ?? '';
        if (in_array($billedFromClientCountryCode, ['SK', 'HU'])) {
            $billedFromVatIdentification = !empty($invoice->billed_from_client['vat_identification_number_sk']) ? $invoice->billed_from_client['vat_identification_number_sk'] : $billedFromVatIdentification;
        }
        $billedFromPartyContactBt = (new PartyContact())
            ->setPartyIdentification(
                (new PartyIdentification())
                    ->setUserId($invoice->billed_from_client['identification_number'])
                    ->setId($invoice->billed_from_client['identification_number'])
            )
            ->addPartyTaxScheme(
                (new \Isdoc\Models\PartyTaxScheme($billedFromVatIdentification, TaxScheme::VAT)) // DIC / IC DPH
            )
            ->setName($invoice->billed_from_client['name'])
            ->setPostalAddress(
                (new PostalAddress)
                    ->setStreetName($invoice->billed_from_client['street'])
                    ->setBuildingNumber('')
                    ->setCityName($invoice->billed_from_client['city'])
                    ->setPostalZone($invoice->billed_from_client['zip'])
                    ->setCountry(
                        (new Country)
                            ->setIdentificationCode($billedFromClientCountryCode)
                    )
            )
            ->setContact(
                (new \Isdoc\Models\Contact())
                    ->setName($invoice->billed_from_client['contact_name'])
                    ->setTelephone($invoice->billed_from_client['contact_phone'])
                    ->setElectronicMail($invoice->billed_from_client['contact_email'])
            );
        if (in_array($billedFromClientCountryCode, ['SK', 'HU'])) {
            $billedFromPartyContactBt->addPartyTaxScheme(
                (new \Isdoc\Models\PartyTaxScheme($invoice->billed_from_client['vat_identification_number'] ?? '', TaxScheme::TIN)) // TIN only for SK
            );
        }
        $taxDocument->setAccountingSupplierParty($billedFromPartyContactBt);

        /* billed to */
        $billedToClientCountryCode = defined("\Isdoc\Enums\CountryCode::{$invoice->billed_to_client['state']}") ? constant("\Isdoc\Enums\CountryCode::{$invoice->billed_to_client['state']}") : null;
        if (null === $billedToClientCountryCode) {
            return false;
        }
        $billedToVatIdentification = $invoice->billed_to_client['vat_identification_number'] ?? '';
        if (in_array($billedToClientCountryCode, ['SK', 'HU'])) {
            $billedToVatIdentification = !empty($invoice->billed_to_client['vat_identification_number_sk']) ? $invoice->billed_to_client['vat_identification_number_sk'] : $billedToVatIdentification;
        }
        $billedToPartyContact = (new PartyContact())
            ->setPartyIdentification(
                (new PartyIdentification())
                    ->setUserId($invoice->billed_to_client['identification_number'])
                    ->setId($invoice->billed_to_client['identification_number'])
            )
            ->addPartyTaxScheme(
                (new \Isdoc\Models\PartyTaxScheme($billedToVatIdentification, TaxScheme::VAT)) // DIC / IC DPH
            )
            ->setName($invoice->billed_to_client['name'])
            ->setPostalAddress(
                (new PostalAddress)
                    ->setStreetName($invoice->billed_to_client['street'])
                    ->setBuildingNumber('')
                    ->setCityName($invoice->billed_to_client['city'])
                    ->setPostalZone($invoice->billed_to_client['zip'])
                    ->setCountry(
                        (new Country)
                            ->setIdentificationCode($billedToClientCountryCode)
                    )
            )
            ->setContact(
                (new \Isdoc\Models\Contact())
                    ->setName($invoice->billed_to_client['contact_name'])
                    ->setTelephone($invoice->billed_to_client['contact_phone'])
                    ->setElectronicMail($invoice->billed_to_client['contact_email'])
            );
        if (in_array($billedToClientCountryCode, ['SK', 'HU'])) {
            $billedToPartyContact->addPartyTaxScheme(
                (new \Isdoc\Models\PartyTaxScheme($invoice->billed_to_client['vat_identification_number'] ?? '', TaxScheme::TIN)) // TIN only for SK
            );
        }
        $taxDocument->setAccountingCustomerParty($billedToPartyContact);

        /* order reference */
        /*if ($invoice->order_id) {
            $taxDocument->addOrderReference(
                (new OrderReference())
                    ->setSalesOrderId($invoice->order_id)
                ->setIssueDate($invoice->billed_date)
                ->setIssueDate($invoice->billed_date)
            );
        }*/

        /* items */
        if (count($invoice->items)) {
            foreach ($invoice->items as $item) {
                $itemTaxRate = $item['taxRate'] ?? 0;
                $totalWithoutTax = round($item['price'] * $item['quantity'], 2);
                $totalWithTax = round($item['price'] * $item['quantity'] * (1 + $itemTaxRate / 100), 2);

                $taxDocument->addInvoiceLine(
                    (new InvoiceLine())
                        ->setId(Uuid::uuid4()->toString())
                        ->setClassifiedTaxCategory(
                            (new ClassifiedTaxCategory())
                                ->setVatCalculationMethod(0)
                                ->setPercent($itemTaxRate)
                        )
                        ->setInvoicedQuantity($item['quantity'])
                        ->setLineExtensionAmount($totalWithoutTax) // without tax
                        ->setLineExtensionAmountTaxInclusive($totalWithTax) // with tax
                        ->setLineExtensionTaxAmount($totalWithTax ? $totalWithTax - $totalWithoutTax : 0) // tax
                        ->setUnitPrice($item['price'])
                        ->setUnitPriceTaxInclusive($item['price'] * (1 + $itemTaxRate / 100))
//                        ->setInvoiceQuantityUnitCode('KS') // TODO
                        ->setItem(
                            (new Item())
                                ->setDescription($item['name'])
                                ->setSellersItemIdentification($item['name'])

                        )
                );
            }
        }



        /* payment - paid */
        $paymentDetail = null;
        $bankTransferData = $invoice->billed_from_client['payment_methods']['bank_transfer'];
        if ($invoice->payment === 'BANK' && !empty($bankTransferData['iban']) && !empty($bankTransferData['swift'])) {
            $accountNumber = substr(str_replace(' ', '', $bankTransferData['iban']), 8);
            $accountNumber = ltrim($accountNumber, '0');

            $accountCode = $bankTransferData['code'];
            if (empty($accountCode)) {
                $accountCode = substr(str_replace(' ', '', $bankTransferData['iban']), 4,4);
            }

            $paymentDetail = (new PaymentDetailBankTransaction())
                ->setBankAccount(
                    (new BankAccount())
                        ->setName($bankTransferData['name'] ?? '')
                        ->setId($accountNumber)
                        ->setBic($bankTransferData['swift'])
                        ->setIban($bankTransferData['iban'])
                        ->setBankCode($accountCode)
                )
                ->setPaymentDueDate($correctDate($invoice->due_date));
        } else {
            $paymentDetail = (new PaymentDetailCash())
                ->setIssueDate($correctDate($invoice->billed_date));
        }

        $taxDocument->setPaymentMeans(
            (new PaymentMeans())
                ->addPayment(
                    (new Payment())
                        ->setPaidAmount(round(!empty($invoice->totalPrice_with_tax) && $invoice->totalPrice_with_tax !== $invoice->totalPrice ? $invoice->totalPrice_with_tax : $invoice->totalPrice,2))
                        ->setHasPartialPayment(false)
                        ->setDetails($paymentDetail)
                        ->setPaymentMeansCode($invoice->payment === 'BANK' ? PaymentMeansCode::BANK_TRANSFER : PaymentMeansCode::CASH)
                )
        );

        /* create file - isdoc */
        $taxDocument->toIsdocFile(Storage::path($isdocDestinationFilePath));

        return true;
    }

    public static function generateIsdocPdf(Invoice $invoice, string $pdfFilePath, string $isdocDestinationFilePath, string $isdocPdfDestinationFilePath): bool
    {
        /* create file - isdoc */
        $isdocCreated = self::generateIsdoc($invoice, $isdocDestinationFilePath);

        if (!$isdocCreated) {
            return false;
        }

        try {
            $CustomPDF = new class extends Fpdi {
                public function Header() {
                }
                public function Footer() {
                }
            };

            $pdf = new $CustomPDF();
            $pageCount = $pdf->setSourceFile(Storage::disk('local')->readStream($pdfFilePath));

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplId);
            }

            $pdf->Annotation(10, 10, 3, 3, 'ISDOC', [
                'Subtype' => 'FileAttachment',
                'Name' => 'PushPin',
                'FS' => Storage::path($isdocDestinationFilePath),
                'Rect' => [10, 10 , 90, 32],
//                'Contents' => 'Click to open the attached file.'
            ]);

            $pdf->Output(Storage::path($isdocPdfDestinationFilePath), 'F');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
