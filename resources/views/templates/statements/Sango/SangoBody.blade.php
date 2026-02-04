<!doctype html>
@php($dateFormat = $template['formats']['date'] ?? 'd.m.Y')
@php($decimalFormat = $template['formats']['decimal'] ?? '.')
@php($thousandsFormat = $template['formats']['thousands'] ?? ',')
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
            /*max-width: 700px;*/
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
            /* padding: 0px 30px 20px; */
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
            /*color: #b23a5a;*/
            color: {{ $template['primary_color'] }};
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

        .qr_code {
            width: 150px;
            line-height: 0;
        }

        .qr_code img {
            width: 100%;
        }
    </style>
</head>
<body>
{{--{{dd($billedFromCompany)}}--}}
<div id="body">
    <h2 style="font-weight: 400; font-size: 27px; padding-top: 60px; padding-bottom: 10px;text-align: center;">@lang('statement.title')</h2>
    <div class="heading">
        <h1>{{$billedFromCompany['name']}}</h1>
        <p style="color: #7a7777"><b>{{$billedFromCompany['name']}},</b> {{$billedFromCompany['street']}}, {{$billedFromCompany['zip']}} {{$billedFromCompany['city']}}, @lang("state.{$billedFromCompany['state']}")</p>
    </div>
    <table class="table1">
        <thead>
        {{-- there should not be anything cuz this is showing on every page !!! --}}
        </thead>
        <tbody>
        <tr>
            <td style="text-align: left; font-size: 13px; font-weight: bold" colspan="7">{{mb_strtoupper(__('invoice.receiver'))}}</td>
        </tr>
        <tr>
            <td colspan="5" style="width: 460px;">{{$client['name']}}</td>
            <td colspan="2" style="font-weight: bold; text-align: right;white-space: nowrap"><span style="padding-right: 40px; display: inline-block;">@lang('statement.date'):</span><span style="display: inline-block">{{date($dateFormat)}}</span></td>
        </tr>
        <tr>
            <td colspan="4">{{$client['street']}}</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="4">{{$client['zip']}} {{$client['city']}}</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="7" style="padding-bottom: 20px; text-align: left;">@lang("state.{$client['state']}")</td>
        </tr>
        <tr>
            <td colspan="4"><span style="width: 120px;float: left;display: block;">@lang('invoice.identification_number'):</span>{{$client['identification_number']}}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="4"><span style="width: 120px;float: left;display: block;">@lang('invoice.vat_identification_number'):</span>{{$client['vat_identification_number']}}</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="4"><span style="width: 120px;float: left;display: block;">@lang('invoice.vat_identification_number_sk'):</span>{{$client['vat_identification_number_sk']}}</td>
            <td colspan="1"></td>
            <td colspan="2" style="font-weight: bold;"></td>
        </tr>
        <tr>
            <td colspan="7" style="padding-top: 20px;padding-bottom: 5px;text-align: left;"><i>{{str_replace(['#_FROM_DATE_#', '#_TO_DATE_#'], [date($dateFormat, strtotime($fromDate)), date($dateFormat, strtotime($toDate))], __('statement.allInvoicesFromDateToDate'))}}</i>:</td>
        </tr>

        @php($currencyIndex = 0)
        @foreach($invoiceList['data'] as $currencyCode => $invoices)

            @php($currencySymbol = '')
            @foreach($invoices as $invoice)
                @php($currencySymbol = $invoice->getCurrencySymbol()['symbol'] ?? '')
                @break
            @endforeach

            <tr>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.billDate')</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.activity')</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.variableSymbol')</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.dueDate')</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.sum') ({{$currencySymbol}})</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.paid') ({{$currencySymbol}})</td>
                <td colspan="1" style="border-bottom: 1px solid #000; @if($currencyIndex !== 0)padding-top: 30px;@endif padding-bottom: 7px; font-weight: bold;">@lang('statement.remaining') ({{$currencySymbol}})</td>
            </tr>

            @foreach($invoices as $invoice)
                <tr>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{date($dateFormat, strtotime($invoice['billed_date']))}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">@lang('statement.invoice') {{$invoice['number']}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['variable_symbol']}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{date($dateFormat, strtotime($invoice['due_date']))}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{number_format(round(!empty($invoice['totalPrice_with_tax']) ? $invoice['totalPrice_with_tax'] : $invoice['totalPrice'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['paid'] ? number_format(round(!empty($invoice['totalPrice_with_tax']) ? $invoice['totalPrice_with_tax'] : $invoice['totalPrice'], 2), 2, $decimalFormat, $thousandsFormat) : number_format(0, 2, $decimalFormat, $thousandsFormat)}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['paid'] ? number_format(0, 2, $decimalFormat, $thousandsFormat) : number_format(round(!empty($invoice['totalPrice_with_tax']) ? $invoice['totalPrice_with_tax'] : $invoice['totalPrice'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                </tr>
            @endforeach

            <tr>
                <td style="border-top: 1px solid #000; font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('statement.invoicedTotal')</td>
                <td class="cs-4" colspan="7" style="border-top: 1px solid #000; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{$currencySymbol}} {{number_format(round($invoiceList['metaData'][$currencyCode]['total'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid {{ $template['primary_color'] }}; font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('statement.paidTotal')</td>
                <td class="cs-4" colspan="7" style="border-bottom: 1px solid {{ $template['primary_color'] }}; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{$currencySymbol}} {{number_format(round($invoiceList['metaData'][$currencyCode]['paid'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>
            <tr>
                <td style="padding-top: 5px; font-size: 18px;">@lang('statement.sumToPay')</td>
                <td class="cs-4" colspan="7" style="padding-top: 5px; font-size: 18px;">{{$currencySymbol}} {{number_format(round($invoiceList['metaData'][$currencyCode]['unpaid'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>

            @php($currencyIndex++)
        @endforeach

        {{-- signature --}}
        <tr>
            <td colspan="7" style="padding-top: 5px; text-align: right; font-weight: bold; ">@lang('invoice.signature'):</td>
        </tr>
        <tr>
            <td colspan="7" style="padding-top: 20px; text-align: right;">
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($billedFromCompany->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px; /*-webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right;*/"/>
            </td>
        </tr>

        </tbody>
    </table>
</div>
</body>
</html>
