<?php

namespace App\Http\Utils;

use Ramsey\Uuid\Uuid;

class FileHelper
{

    /* COMPANY - DOCUMENT - INVOICE */
    public static function getDocumentResourceFilePathList(string $folderNameDocumentType, string $userId, string $companyId, int $invoiceId, string $invoiceBilledDate, bool $preview = false): array
    {
        $billedDateYear = date('Y', strtotime($invoiceBilledDate));
        $dirPath = 'user/'.$userId.'/company_'.$companyId.'/'.$folderNameDocumentType.'/'.$billedDateYear.'/';

        if ($preview) {
            $dirPath = 'user/'.$userId.'/company_'.$companyId.'/'.$folderNameDocumentType.'/';
            $invoiceId = 'preview';
        }

        return [
            'body' => $dirPath.$invoiceId.'_body.html',
            'footer' => $dirPath.$invoiceId.'_footer.html',
            'isdoc' => $dirPath.$invoiceId.'.isdoc',
            'pdf' => $dirPath.$invoiceId.'.pdf',
            'isdocPdf' => $dirPath.$invoiceId.'.isdoc.pdf',
        ];
    }

    public static function getFutureInvoicePdfPath(string $userId, string $companyId): string
    {
        return 'user/'.$userId.'/company_'.$companyId.'/invoices/preview.pdf';
    }

    public static function getInvoicesBulkZipFilePath(string $userId, string $companyId): array
    {
        $dirPath = 'tmp/'.$userId.'/company_'.$companyId.'/invoices/zip/';
        $fileName = date('Y-m-d_H-i-s').'_'.Uuid::uuid4()->toString().'.zip';

        return [
            'fullPath' => $dirPath.$fileName,
            'dirPath' => $dirPath,
            'filePath' => $dirPath.$fileName,
        ];
    }


    /* CLIENT - STATEMENT */
    public static function getClientStatementFilePathList(string $userId, string $companyId, int $clientId, string $fromDate, string $toDate, bool $onlyUnpaidInvoices): array
    {
        $dirPath = 'user/'.$userId.'/company_'.$companyId.'/statements/clients/'.$clientId.'/';
        $fileName = $fromDate.'_'.$toDate.'_'.($onlyUnpaidInvoices ? 'unpaid' : 'all');
        return [
            'body' => $dirPath.$fileName.'_body.html',
            'footer' => $dirPath.$fileName.'_footer.html',
            'pdf' => $dirPath.$fileName.'.pdf',
        ];
    }

    /* PRODUCTS - image */
    public static function getProductImageFilePath(string $userId, string $productUuid)
    {
        return 'user/'.$userId.'/products/'.$productUuid.'.png';
    }

    /* RECEIPTS */
    public static function getDocumentReceiptResourceFilePathList(string $userId, string $companyId, int $receiptId, string $receiptDate): array
    {
        $receiptDateYear = date('Y', strtotime($receiptDate));
        $dirPath = 'user/'.$userId.'/company_'.$companyId.'/receipts/'.$receiptDateYear.'/';

        return [
            'body' => $dirPath.$receiptId.'_body.html',
            'pdf' => $dirPath.$receiptId.'.pdf',
        ];
    }
}
