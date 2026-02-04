<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Príjmový Pokladničný Doklad</title>
    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
            color: #A000A0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        table.page {
            width: 1122px;
            height: 793px;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        .receipt1,
        .receipt2,
        .receipt3,
        .receipt4 {
            padding: 18px;
            padding-bottom: 0;
            width: 50%;
            height: 50%;
        }

        td.receipt1,
        td.receipt3 {
            border-right: 1pt solid #dbdbdb;
        }

        td.receipt1,
        td.receipt2 {
            border-bottom: 1pt solid #dbdbdb;
        }

        .receiptTable {
            width: 100%;
            /* height: 350px; */
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            border: 1pt solid #A000A0;
        }

        .receipt-header-left,
        .receipt-header-right {
            width: 50%;
            height: 70px;
            border-bottom: 1pt solid #A000A0;
            padding: 6px;
            font-size: 8.973pt;
        }

        .receipt-header-left {
            border-right: 1pt solid #A000A0;
            position: relative;
        }

        .receipt-header-right {
            padding-right: 0;
            position: relative;
        }

        .receipt-header-right .title {
            position: absolute;
            top: 6px;
            font-size: 15.951pt;
        }

        .receipt-header-right .receipt-no {
            font-size: 8.973pt;
            padding-top: 22px;
        }

        .receipt-header-right .day {
            font-size: 7.976pt;
            padding-top: 6px;
        }

        .sum-table {
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 6.979pt;
            width: 100%;
            border: 1pt solid #A000A0;
        }

        .sum-table td{
            padding: 6px;
            padding-top: 3px;
            padding-bottom: 3px;
            border: 1pt solid #A000A0;
        }

        .footer-table-l {
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            width: 100%;
        }

        .footer-table-r {
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            width: 100%;
            border: 1pt solid #A000A0;
            width: 100%;
            font-size: 6.979pt;
        }

        .footer-table-r td {
            text-align: center;
            padding: 4px;
            padding-bottom: 0;
            border: 1pt solid #A000A0;
        }

        .cta {
            text-decoration: none;
            color: #000;
            font-size: 9.3px;
            font-weight: bold;
        }

        /* Utilities */
        .p-6 {
            padding: 6px;
        }
        .p-3 {
            padding: 3px;
        }
        .pr-0 {
            padding-right: 0;
        }
        .fz-7pt {
            font-size: 7.976pt;
        }
        .p-relative {
            position: relative;
        }
        .p-absolute {
            position: absolute;
        }
        .top-6 {
            top: 6px;
        }
        .bottom-6 {
            bottom: 6px;
        }
        .d-inline-block {
            display: inline-block;
        }
        .border-none {
            border: none;
        }
        .border-b-1 {
            border-bottom: 1pt solid #A000A0;
        }
        .border-1 {
            border: 1pt solid #A000A0;
        }
        .text-black {
            color: #000;
        }
    </style>
</head>
<body>

<table cellspacing="0" cellpadding="0" border="0" class="page" style="background-color:white; margin-inline: auto; overflow: hidden;">
    <tr>
        <td class="receipt1">
            <table class="receiptTable">
                <tr>
                    <td class="receipt-header-left">
                        <div class="p-absolute top-6" style="width: 95%;">
                            <p style="float: left; padding-right: 6px;">Firma</p>
                            <p class="text-black" style="width: 70%; margin-left: auto; margin-right: auto; text-align: center;">{{$company->name}}
                                {{$company->street}} {{$company->city}} {{$company->zip}}</p>
                        </div>
                        <!-- consequuntur consectetur adipisicing -->

                        <div class="p-absolute bottom-6">
                            <p style="white-space: nowrap;">DIČ / IČDPH: <span class="text-black">{{$company->vat_identification_number}}</span></p>

                            <p>IČO: <span class="text-black">{{$company->identification_number}}</span></p>
                        </div>
                    </td>

                    <td class="receipt-header-right">
                        <p class="title">PRÍJMOVÝ</p>

                        <p class="receipt-no">
                            pokladničný doklad číslo: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;">{{$invoice->order_id}}</span>
                  .................................
                </span>
                        </p>

                        <p class="day">zo dňa: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;">{{$invoice->billed_date}}</span>
                  .....................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0 fz-7pt" colspan="2">
                        <p>Prijaté od: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;">{{$invoice->billed_to_client['name']}}, {{$invoice->billed_to_client['street']}} {{$invoice->billed_to_client['city']}} {{$invoice->billed_to_client['zip']}}</span>
                  ......................................................................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0" colspan="2" style="padding-top: 0; padding-bottom: 0;">
                        <p class="fz-7pt d-inline-block">
                            IČO:
                            <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;">{{$invoice->billed_to_client['identification_number']}}</span>
                  ....................................
                </span>
                        </p>
                        <p class="fz-7pt d-inline-block">
                            DIČ / IČDPH: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;">{{$invoice->billed_to_client['vat_identification_number']}}</span>
                  ...................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="p-6">
                        <table class="sum-table fz-7pt">
                            <tr>
                                <td style="width: 25%;">Suma bez DPH</td>
                                <td style="width: 20%;">DPH ..........%</td>
                                <td style="width: 25%;">Nepodlieha DPH</td>
                                <td style="width: 30%;">Celkom k úhrade</td>
                            </tr>

                            <tr>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">
                                    {{$receipt->total}} {{$receipt->currency_3_code}}
                                </td>
                            </tr>

                            <tr>
                                <td class="border-none" colspan="4">
                                    Suma slovom
                                    <span class="text-black" style="padding-left: 12px;">{{$toalInHumanFormat}}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-1" style="padding-bottom: 6px;" colspan="2">
                        <table class="footer-table-l fz-7pt">
                            <tr>
                                <td class="p-6 pr-0" style="width: 54%;">
                                    <p style="white-space: nowrap;">
                                        Účel platby: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ......................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Vyhotovil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;">{{$receipt->made_by}}</span>
                        .........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Schválil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;">{{$receipt->approved_by}}</span>
                        ...........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Podpis príjemcu: &nbsp; <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ............................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Zaúčtované v denníku pod por. č.: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...................................
                      </span>
                                    </p>
                                </td>

                                <td class="p-6" style="width: 46%;">
                                    <table class="footer-table-r">
                                        <tr class="border-1">
                                            <td colspan="2" class="border-none">ÚČTOVACÍ PREDPIS</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td style="width: 50%;">Dal - účet</td>

                                            <td>Suma</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr>
                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Dátum</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>

                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Podpis</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <a href="https://fakturo.app/" class="cta">Fakturo for iOS/Android</a>
        </td>

        <td class="receipt2">
            <table class="receiptTable">
                <tr>
                    <td class="receipt-header-left">
                        <div class="p-absolute top-6" style="width: 95%;">
                            <p style="float: left; padding-right: 6px;">Firma</p>

                            <p class="text-black" style="width: 70%; margin-left: auto; margin-right: auto; text-align: center;"></p>
                        </div>
                        <!-- consequuntur consectetur adipisicing -->

                        <div class="p-absolute bottom-6">
                            <p style="white-space: nowrap;">DIČ / IČDPH: <span class="text-black"></span></p>

                            <p>IČO: <span class="text-black"></span></p>
                        </div>
                    </td>

                    <td class="receipt-header-right">
                        <p class="title">PRÍJMOVÝ</p>

                        <p class="receipt-no">
                            pokladničný doklad číslo: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .................................
                </span>
                        </p>

                        <p class="day">zo dňa: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .....................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0 fz-7pt" colspan="2">
                        <p>Prijaté od: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ......................................................................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0" colspan="2" style="padding-top: 0; padding-bottom: 0;">
                        <p class="fz-7pt d-inline-block">
                            IČO:
                            <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ....................................
                </span>
                        </p>
                        <p class="fz-7pt d-inline-block">
                            DIČ / IČDPH: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ...................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="p-6">
                        <table class="sum-table fz-7pt">
                            <tr>
                                <td style="width: 25%;">Suma bez DPH</td>
                                <td style="width: 20%;">DPH ................ %</td>
                                <td style="width: 25%;">Nepodlieha DPH</td>
                                <td style="width: 30%;">Celkom k úhrade</td>
                            </tr>

                            <tr>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                            </tr>

                            <tr>
                                <td class="border-none" colspan="4">
                                    Suma slovom
                                    <span class="text-black" style="padding-left: 12px;"> </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-1" style="padding-bottom: 6px;" colspan="2">
                        <table class="footer-table-l fz-7pt">
                            <tr>
                                <td class="p-6 pr-0" style="width: 54%;">
                                    <p style="white-space: nowrap;">
                                        Účel platby: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ......................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Vyhotovil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        .........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Schválil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Podpis príjemcu: &nbsp; <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ............................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Zaúčtované v denníku pod por. č.: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...................................
                      </span>
                                    </p>
                                </td>

                                <td class="p-6" style="width: 46%;">
                                    <table class="footer-table-r">
                                        <tr class="border-1">
                                            <td colspan="2" class="border-none">ÚČTOVACÍ PREDPIS</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td style="width: 50%;">Dal - účet</td>

                                            <td>Suma</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr>
                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Dátum</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>

                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Podpis</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <a href="https://fakturo.app/" class="cta">Fakturo for iOS/Android</a>
        </td>
    </tr>
    <tr>
        <td class="receipt3">
            <table class="receiptTable">
                <tr>
                    <td class="receipt-header-left">
                        <div class="p-absolute top-6" style="width: 95%;">
                            <p style="float: left; padding-right: 6px;">Firma</p>

                            <p class="text-black" style="width: 70%; margin-left: auto; margin-right: auto; text-align: center;"></p>
                        </div>
                        <!-- consequuntur consectetur adipisicing -->

                        <div class="p-absolute bottom-6">
                            <p style="white-space: nowrap;">DIČ / IČDPH: <span class="text-black"></span></p>

                            <p>IČO: <span class="text-black"></span></p>
                        </div>
                    </td>

                    <td class="receipt-header-right">
                        <p class="title">PRÍJMOVÝ</p>

                        <p class="receipt-no">
                            pokladničný doklad číslo: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .................................
                </span>
                        </p>

                        <p class="day">zo dňa: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .....................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0 fz-7pt" colspan="2">
                        <p>Prijaté od: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ......................................................................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0" colspan="2" style="padding-top: 0; padding-bottom: 0;">
                        <p class="fz-7pt d-inline-block">
                            IČO:
                            <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ....................................
                </span>
                        </p>
                        <p class="fz-7pt d-inline-block">
                            DIČ / IČDPH: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ...................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="p-6">
                        <table class="sum-table fz-7pt">
                            <tr>
                                <td style="width: 25%;">Suma bez DPH</td>
                                <td style="width: 20%;">DPH ................ %</td>
                                <td style="width: 25%;">Nepodlieha DPH</td>
                                <td style="width: 30%;">Celkom k úhrade</td>
                            </tr>

                            <tr>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                            </tr>

                            <tr>
                                <td class="border-none" colspan="4">
                                    Suma slovom
                                    <span class="text-black" style="padding-left: 12px;"> </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-1" style="padding-bottom: 6px;" colspan="2">
                        <table class="footer-table-l fz-7pt">
                            <tr>
                                <td class="p-6 pr-0" style="width: 54%;">
                                    <p style="white-space: nowrap;">
                                        Účel platby: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ......................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Vyhotovil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        .........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Schválil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Podpis príjemcu: &nbsp; <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ............................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Zaúčtované v denníku pod por. č.: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...................................
                      </span>
                                    </p>
                                </td>

                                <td class="p-6" style="width: 46%;">
                                    <table class="footer-table-r">
                                        <tr class="border-1">
                                            <td colspan="2" class="border-none">ÚČTOVACÍ PREDPIS</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td style="width: 50%;">Dal - účet</td>

                                            <td>Suma</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr>
                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Dátum</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>

                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Podpis</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <a href="https://fakturo.app/" class="cta">Fakturo for iOS/Android</a>
        </td>
        <td class="receipt4">
            <table class="receiptTable">
                <tr>
                    <td class="receipt-header-left">
                        <div class="p-absolute top-6" style="width: 95%;">
                            <p style="float: left; padding-right: 6px;">Firma</p>

                            <p class="text-black" style="width: 70%; margin-left: auto; margin-right: auto; text-align: center;"></p>
                        </div>
                        <!-- consequuntur consectetur adipisicing -->

                        <div class="p-absolute bottom-6">
                            <p style="white-space: nowrap;">DIČ / IČDPH: <span class="text-black"></span></p>

                            <p>IČO: <span class="text-black"></span></p>
                        </div>
                    </td>

                    <td class="receipt-header-right">
                        <p class="title">PRÍJMOVÝ</p>

                        <p class="receipt-no">
                            pokladničný doklad číslo: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .................................
                </span>
                        </p>

                        <p class="day">zo dňa: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  .....................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0 fz-7pt" colspan="2">
                        <p>Prijaté od: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ......................................................................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td class="p-6 pr-0" colspan="2" style="padding-top: 0; padding-bottom: 0;">
                        <p class="fz-7pt d-inline-block">
                            IČO:
                            <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ....................................
                </span>
                        </p>
                        <p class="fz-7pt d-inline-block">
                            DIČ / IČDPH: <span class="p-relative">
                  <span class="text-black" style="position: absolute; bottom: 3px; width: 100%;"></span>
                  ...................................................................................................
                </span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="p-6">
                        <table class="sum-table fz-7pt">
                            <tr>
                                <td style="width: 25%;">Suma bez DPH</td>
                                <td style="width: 20%;">DPH ................ %</td>
                                <td style="width: 25%;">Nepodlieha DPH</td>
                                <td style="width: 30%;">Celkom k úhrade</td>
                            </tr>

                            <tr>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                                <td class="text-black" style="height: 12px; text-align: center;">

                                </td>
                            </tr>

                            <tr>
                                <td class="border-none" colspan="4">
                                    Suma slovom
                                    <span class="text-black" style="padding-left: 12px;"> </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-1" style="padding-bottom: 6px;" colspan="2">
                        <table class="footer-table-l fz-7pt">
                            <tr>
                                <td class="p-6 pr-0" style="width: 54%;">
                                    <p style="white-space: nowrap;">
                                        Účel platby: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ......................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Vyhotovil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        .........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Schválil: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...........................................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Podpis príjemcu: &nbsp; <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ............................................................
                      </span>
                                    </p>

                                    <p style="padding-top: 14px; white-space: nowrap;">
                                        Zaúčtované v denníku pod por. č.: <span class="p-relative">
                        <span class="text-black" style="position: absolute; bottom: 3px; width: 100%; text-align: center;"></span>
                        ...................................
                      </span>
                                    </p>
                                </td>

                                <td class="p-6" style="width: 46%;">
                                    <table class="footer-table-r">
                                        <tr class="border-1">
                                            <td colspan="2" class="border-none">ÚČTOVACÍ PREDPIS</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td style="width: 50%;">Dal - účet</td>

                                            <td>Suma</td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr class="border-1">
                                            <td class="text-black" style="width: 50%; height: 10.67px;"></td>

                                            <td class="text-black" style="height: 10.67px;"></td>
                                        </tr>

                                        <tr>
                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Dátum</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>

                                            <td class="border-none p-relative" style="text-align: center; height: 20px; width: 50%;">
                                                <span style="position: absolute;left: 4px; top: 4px">Podpis</span>
                                                <span class="text-black" style="padding-left: 30px;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <a href="https://fakturo.app/" class="cta">Fakturo for iOS/Android</a>
        </td>
    </tr>
</table>

</body>
</html>
