<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8">
    <title>PRÍJMOVÝ PDF</title>
    <style>
        /* root styles */

        html {
            font-size: 12px;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #a000a0;
            background-color: #dedede;
            line-height: 1.4;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            font-weight: normal;
        }

        main {
            display: block;
            position: relative;
            margin: auto;
            height: 210mm;
            width: 297mm;
            background-color: white;
        }

        * {
            box-sizing: border-box;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        hr {
            border: 1px solid #1a1b1b;
        }

        table td {
            vertical-align: top;
        }

        /* page styles */

        .w-half {
            width: 50%;
        }

        .h-half {
            height: 104.5mm;
        }

        .theme-table {
            border: 1.5px solid #a000a0;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .theme-table td {
            border: 1.5px solid #a000a0;
            padding: 5px;
        }

        .table-shrink td {
            padding: 0;
        }

        .no-border {
            border: none !important;
        }

        .px-0 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .mb-2 {
            margin-bottom: 2px;
        }

        .text-black {
            color: black;
        }

        .h-24 {
            height: 24px
        }

        .p-4 {
            padding: 12px
        }

        .py-2 {
            padding-top: 5px;
        }

        .text-center {
            text-align: center
        }

        .text-xs {
            font-size: 12px;
            line-height: 16px
        }

        .font-bold {
            font-weight: 700
        }

        @media print {
            @page {
                size: A4 landscape;
            }

            .no-print {
                display: none;
            }

            main {
                width: auto;
            }
        }
    </style>
</head>

<body>
<main>

    <table style="width: 100%; height: 100%; border-collapse: collapse; border-color: rgb(210, 210, 210);" border="1">
        <tbody>
        <tr>
            <td class="w-half h-half p-4">
                <div class="">
                    <table class="theme-table">
                        <tbody>
                        <tr>
                            <td style="width: 50%;">
                                Firma
                                <span>
                                    <div>{{$receipt->billedToClient->name}}</div>
                                    <div>{{$receipt->billedToClient->street}}</div>
                                    <div>{{$receipt->billedToClient->zip}} {{$receipt->billedToClient->city}}</div>
                                    <div>@lang("state.{$receipt->billedToClient->state}")</div>
                                </span>
                                <br><br>
                                DIČ / IČDPH: <span>@if(!empty($receipt->billedToClient->vat_identification_number_sk)){{$receipt->billedToClient->vat_identification_number_sk}}@else{{$receipt->billedToClient->vat_identification_number}}@endif</span><br>
                                IČO: <span>{{$receipt->billedToClient->identification_number}}</span>
                            </td>
                            <td style="width: 50%;">
                                <h1>PRÍJMOVÝ</h1>
                                <div>pokladničný doklad číslo: ....................................</div>
                                <div>zo dňa: ...............................................................................</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="mb-2">
                                    Prijaté od: .................................................................................................................................................................................
                                </div>
                                <div class="mb-2">
                                    IČO: ....................................... DIČ / IČDPH: .........................................................................................................................
                                </div>
                                <table class="theme-table">
                                    <tbody>
                                    <tr>
                                        <td>Suma bez DPH </td>
                                        <td>DPH ................ %</td>
                                        <td>Nepodlieha DPH</td>
                                        <td>Celkom k úhrade</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">Suma slovom</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="mb-2"></div>
                                <table class="no-border">
                                    <tbody>
                                    <tr>
                                        <td class="w-half no-border px-0">
                                            <div class="mb-2">Účel platby: ....................................................................</div>
                                            <div class="mb-2">
                                                Vyhotovil: .........................................................................
                                            </div>
                                            <div class="mb-2">
                                                Schválil: .............................................................................
                                            </div>
                                            <div class="mb-2">
                                                Podpis príjemcu: .........................................................
                                            </div>
                                            <div class="">
                                                Zaúčtované v denníku pod por. č.: .................................................................................................
                                            </div>
                                        </td>
                                        <td class="no-border px-0">
                                            <table class="theme-table table-shrink text-center">
                                                <tbody>
                                                <tr>
                                                    <td colspan="2">ÚČTOVACÍ PREDPIS</td>
                                                </tr>
                                                <tr>
                                                    <td class="w-half">Dal - účet </td>
                                                    <td class="w-half">Suma</td>
                                                </tr>

                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <td>Dátum</td>
                                                    <td>Podpis</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

</main>


</body>

</html>


{{--<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tinos', serif;
            line-height: 1.33;
            margin-left: 63px;
            margin-right: 63px;
        }

        .wrapper {
            background-color: #ffffff;
            padding: 0;
            height: 100%;
        }

        .container {
            position: relative;
            width: 88%;
            margin: 0 auto;
            height: 100%;
            min-height: 1370px;
            display: block;
        }

        .wrapper .container h2 {
            font-size: 20px;
            color: #000;
            text-align: center;
            font-weight: 700;
            padding-bottom: 10px;
        }

        .heading {
            width: 100%;
            text-align: center;
            padding: 0px 0px 10px 0;
            border-top: 1px solid #DDD;
            border-bottom: 1px solid #DDD;
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .heading h1 {
            font-size: 32px;
            font-weight: bold;
            color: #b23a5a;
        }

        table {
            border-collapse: collapse;
        }

        table td {
            font-size: 13px;
            padding: 2px 0;
        }

        table td:last-child {
            max-width: 100px;
            text-align: right;
        }

        table td[colspan="4"].cs-4 {
            max-width: none;
            text-align: left;
        }

        .table1 {
            width: 98%;
            margin: 0 10px;
        }
    </style>
</head>
<body>
<div id="body">
    <h2 style="font-weight: 400; font-size: 27px; padding-top: 60px; padding-bottom: 10px;text-align: center;">Receipt</h2>
    <div class="heading">
        <h1>{{$receipt->company->name}}</h1>
        <p style="color: #7a7777"><b>{{$receipt->company->name}},</b> {{$receipt->company->street}}, {{$receipt->company->zip}} {{$receipt->company->city}}, @lang("state.{$receipt->company->state}")</p>
    </div>
    <table class="table1">
        <thead>
        <tr>
            <th style="text-align: left; font-size: 13px" colspan="5">RECEIVER</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2" style="width: 460px;">{{$receipt->billedToClient->name}}</td>
            <td colspan="1">Receipt Number:</td>
            <td colspan="2" style="font-weight: bold;">{{$receipt->id}}</td>
        </tr>
        <tr>
            <td colspan="2">{{$receipt->billedToClient->street}}</td>
            <td colspan="1">Date:</td>
            <td colspan="2" style="font-weight: bold;">{{date('d.m.Y', strtotime($receipt->date))}}</td>
        </tr>
        <tr>
            <td colspan="2">{{$receipt->billedToClient->zip}} {{$receipt->billedToClient->city}}</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="5" style="padding-bottom: 20px; text-align: left;">@lang("state.{$receipt->billedToClient->state}")</td>
        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@if(!empty($receipt->billedToClient->identification_number))Identification Number:</span>{{$receipt->billedToClient->identification_number}}@endif</td>
            <td colspan="1">Journal Number:</td>
            <td colspan="2" style="font-weight: bold;">{{$receipt->journal_number}}</td>
        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@if(!empty($receipt->billedToClient->vat_identification_number))VAT Identification Number:</span>{{$receipt->billedToClient->vat_identification_number}}@endif</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@if(!empty($receipt->billedToClient->vat_identification_number_sk))VAT Identification Number SK:</span>{{$receipt->billedToClient->vat_identification_number_sk}}@endif</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px;padding-bottom: 5px;text-align: left;"><i>Purpose:</i></td>
        </tr>
        <tr>
            <td colspan="5" style="padding-bottom: 20px; text-align: left;">{{$receipt->purpose}}</td>
        </tr>
        @if($receipt->billing_regulation && count($receipt->billing_regulation) > 0)
        <tr>
            <td colspan="2" style="border-bottom: 1px solid #000; padding-bottom: 7px; font-weight: bold;">Account</td>
            <td colspan="3" style="border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">Total ({{$receipt->getCurrencySymbol()['symbol']}})</td>
        </tr>
        @foreach($receipt->billing_regulation as $regulation)
        <tr>
            <td colspan="2" style="padding: 7px 0; border-bottom: .5px solid #000">{{$regulation['account']}}</td>
            <td colspan="3" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{number_format(round($regulation['total'], 2), 2, '.', ',')}}</td>
        </tr>
        @endforeach
        @endif
        <tr>
            <td style="font-weight: bold; padding-top: 5px; font-size: 15px;">Total ({{$receipt->currency_3_code}}):</td>
            <td class="cs-4" colspan="4" style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($receipt->total, 2), 2, '.', ',')}} {{$receipt->getCurrencySymbol()['symbol']}}</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 40px; text-align: left;">Made by: {{$receipt->made_by}}</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 10px; text-align: left;">Approved by: {{$receipt->approved_by}}</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px; text-align: right; font-weight: bold;">Signature:</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px; text-align: right;">
                @if($receipt->company->getSignaturePath() && Storage::disk('local')->exists($receipt->company->getSignaturePath()))
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($receipt->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px;"/>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>--}}
