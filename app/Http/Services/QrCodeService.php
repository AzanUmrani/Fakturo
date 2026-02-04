<?php

namespace App\Http\Services;

use App\Enums\QrCodeProvider;
use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Gray;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Defr\QRPlatba\QRPlatba;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Trinetus\PayBySquareGenerator\PayBySquareGenerator;

class QrCodeService
{

    /* TODO use this */
    public static function getQrCode(
        QrCodeProvider $provider,
        float $invoicePrice,
        string $iban,
        string $swift,
        string $variableSymbol,
        string $constantSymbol,
        string $specificSymbol,
        string $invoiceNumber,
        string $currency,
        \DateTime $dueDate
    ) {
        if ($provider === QrCodeProvider::PAY_BY_SQUARE) {
            return self::getPayBySquare(...array_slice(func_get_args(), 1));
        }

        if ($provider === QrCodeProvider::UNIVERSAL) {
            return self::payByQrPlatba(...array_slice(func_get_args(), 1));
        }

        return '';
    }

    public static function getPayBySquare(
        float $invoicePrice,
        string $iban,
        string $swift,
        string $variableSymbol,
        string $constantSymbol,
        string $specificSymbol,
        string $invoiceNumber,
        string $currency,
        \DateTime $dueDate
    ) {
        $outputString = (new PayBySquareGenerator())
            ->setAmount($invoicePrice)
            ->setIban($iban)
            ->setBic($swift)
            ->setBeneficaryName('')
            ->setVariableSymbol($variableSymbol)
            ->setConstantSymbol($constantSymbol)
            ->setSpecificSymbol($specificSymbol)
            ->setNote($invoiceNumber)
            ->setCurrency($currency)
            ->getOutput();

        $renderer = new ImageRenderer(
            new RendererStyle(
                size: 400,
                fill: Fill::uniformColor(
                    new Alpha(0, new Rgb(0, 0, 0)), // transparent background
                    new Gray(0)
                )
            ),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrcode = $writer->writeString($outputString);

        return $qrcode;
    }

    public static function payByQrPlatba(
        float $invoicePrice,
        string $iban,
        string $swift,
        string $variableSymbol,
        string $constantSymbol,
        string $specificSymbol,
        string $invoiceNumber,
        string $currency,
        \DateTime $dueDate
    ) {
        $qrPlatba = new QRPlatba();


        $qrPlatba
            ->setIBAN($iban) // nastavení č. účtu
            ->setVariableSymbol($variableSymbol)
            ->setMessage($invoiceNumber)
//            ->setConstantSymbol($constantSymbol)
//            ->setSpecificSymbol($specificSymbol)
            ->setAmount($invoicePrice)
            ->setCurrency($currency); // Výchozí je CZK, lze zadat jakýkoli ISO kód měny
//            ->setDueDate($dueDate); // problem ze chodia neskore platby

        $qrCodeInstance = $qrPlatba->getQRCodeInstance()->setBackgroundColor(new Color(255, 255, 255, 127));

        $writer = new PngWriter();
        return $writer->write($qrCodeInstance, null, null)->getDataUri();

        return $qrPlatba->getDataUri();
    }
}
