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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica';
            line-height: 1.33;
            margin-left: 63px;
            margin-right: 63px;
        }

        .heading {
            width: 100%;
            text-align: right;
            padding: 60px 0 10px 0;
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .heading h1 {
            font-weight: 600;
            font-size: 24px;
            margin: 0;
            padding: 0 0 10px;
            color: #000;
        }


        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
        }

        table td {
            font-size: 14px;
            padding: 0;
            vertical-align: top;
        }

        .table_main td {
            text-align: left;
        }

        .half {
            width: 50%;
        }

        td .line {
            display: block;
            padding: 2px 0;
        }



        .table_main .td_title {
            font-weight: 600;
            text-transform: uppercase;
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .dates {
            width: auto;
        }

        .dates td {
            width: auto;
            padding: 2px 0;
            text-align: left;
            vertical-align: top;
        }

        .dates td:first-child {
            padding-right: 35px;
        }

        .table_gray {
            margin: 20px 0 0;
            background: #F2F2F2;
            box-shadow: 0 0 0 10px #F2F2F2, 0 0 0 12px #CCCCCC;
        }

        .gray {
            padding: 15px;
        }

        .items .head td {
            border-bottom: 2px solid #ccc;
            padding: 7px;
            margin-top: 20px;
            text-align: right;
            font-weight: 600;
            text-transform: uppercase;
        }

        .items .item td {
            padding: 7px;
            text-align: right;
        }

        .items .item td.to_left,
        .items .head td.to_left {
            text-align: left !important;
        }

        .table_sum_total {
        }

        .table_sum_total .sum_total {
            font-size: 20px;
            font-weight: 600;
            background: #F2F2F2;
            box-shadow: 0 0 0 12px #F2F2F2;
            line-height: 1;
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
<div id="body">
    <div class="heading">
        <h1 style="color: {{ $template['primary_color'] }};">@lang('statement.title')</h1>
        <p></p>
    </div>
    <table class="table_main">
        <tbody>
        <td class="half">
            <span class="line td_title">@lang('invoice.supplier')</span>
            <span class="line name">{{$billedFromCompany['name']}}</span>
            <span class="line street">{{$billedFromCompany['street']}}</span>
            <span class="line city">{{$billedFromCompany['zip']}} {{$billedFromCompany['city']}}</span>
            <span class="line state">@lang("state.{$billedFromCompany['state']}")</span>
            <span class="line ico_dic"><span class="ico" style="margin-right: 15px;">@lang('invoice.identification_number'): {{$billedFromCompany['identification_number']}}</span><span class="dic">@lang('invoice.vat_identification_number'): {{$billedFromCompany['vat_identification_number']}}</span></span>
            <span class="line dph">@if(!empty($billedFromCompany['vat_identification_number_sk']))
                    @lang('invoice.vat_identification_number_sk')
                    {{$billedFromCompany['vat_identification_number_sk']}}
                @else
                    @lang('invoice.vat_number_missing')
                @endif
            </span>
        </td>
        <td class="half">
            <span class="line td_title">{{mb_strtoupper(__('invoice.receiver'))}}</span>
            <span class="line name">{{$client['name']}}</span>
            <span class="line street">{{$client['street']}}</span>
            <span class="line city">{{$client['zip']}} {{$client['city']}}</span>
            <span class="line state">@lang("state.{$client['state']}")</span>
        </td>
        </tr>
        <tr class="space">
            <td colspan="2" style="padding-bottom: 20px;"></td>
        </tr>
        <tr>
            <td>
                <table class="dates">
                    <tbody>
                    <tr>
                        <td>@lang('statement.date'):</td>
                        <td>{{date($dateFormat)}}</td>
                    </tr>
                    <tr>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <span class="line">
                    <span class="ico"
                          style="margin-right: 20px;">@lang('invoice.identification_number'): {{$client['identification_number']}}</span>
                <span class="dic"
                      style="margin-right: 20px;">@lang('invoice.vat_identification_number'): {{$client['vat_identification_number']}}</span>
               <span class="ic_dph">@lang('invoice.vat_identification_number_sk'): {{$client['vat_identification_number_sk']}}</span>
                </span>
            </td>
        </tr>
        </tbody>
    </table>
    @if(!empty($billedFromCompany['payment_methods']['bank_transfer']['iban']) && !empty($billedFromCompany['payment_methods']['bank_transfer']['swift']))
        <table class="table_gray">
            <tbody>
            <tr>
                <td class="gray">
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <span class="line">IBAN: <strong>{{$billedFromCompany['payment_methods']['bank_transfer']['iban'] ?? ''}}</strong></span>
                                <span class="line">SWIFT: {{$billedFromCompany['payment_methods']['bank_transfer']['swift'] ?? ''}}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    @endif
    <table class="items">
        <tbody>
        <tr>
            <td colspan="5" style="padding-top: 20px;padding-bottom: 5px;text-align: left;">{{str_replace(['#_FROM_DATE_#', '#_TO_DATE_#'], [date($dateFormat, strtotime($fromDate)), date($dateFormat, strtotime($toDate))], __('statement.allInvoicesFromDateToDate'))}}</td>
        </tr>

        @php($currencyIndex = 0)
        @foreach($invoiceList['data'] as $currencyCode => $invoices)

            @php($currencySymbol = '')
            @foreach($invoices as $invoice)
                @php($currencySymbol = $invoice->getCurrencySymbol()['symbol'] ?? '')
                @break
            @endforeach

            <tr class="head">
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.billDate')</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.activity')</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.variableSymbol')</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.dueDate')</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.sum') ({{$currencySymbol}})</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.paid') ({{$currencySymbol}})</td>
                <td colspan="1" class="to_left" style="@if($currencyIndex !== 0)padding-top: 30px;@endif">@lang('statement.remaining') ({{$currencySymbol}})</td>
            </tr>

            @foreach($invoices as $invoice)
                <tr class="item">
                    <td colspan="1" class="to_left" style="padding: 7px 0; border-bottom: .5px solid #000">{{date($dateFormat, strtotime($invoice['billed_date']))}}</td>
                    <td colspan="1" class="to_left" style="padding: 7px 0; border-bottom: .5px solid #000">@lang('statement.invoice') {{$invoice['number']}}</td>
                    <td colspan="1" class="to_left" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['variable_symbol']}}</td>
                    <td colspan="1" class="to_left" style="padding: 7px 0; border-bottom: .5px solid #000">{{date($dateFormat, strtotime($invoice['due_date']))}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{number_format($invoice['totalPrice'], 2, $decimalFormat, $thousandsFormat)}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['paid'] ? number_format($invoice['totalPrice'], 2, $decimalFormat, $thousandsFormat) : number_format(0, 2, $decimalFormat, $thousandsFormat)}}</td>
                    <td colspan="1" style="padding: 7px 0; border-bottom: .5px solid #000">{{$invoice['paid'] ? number_format(0, 2, $decimalFormat, $thousandsFormat) : number_format($invoice['totalPrice'], 2, $decimalFormat, $thousandsFormat)}}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3" style="border-top: 1px solid #000; font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('statement.invoicedTotal')</td>
                <td colspan="4" class="cs-4" style="border-top: 1px solid #000; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{$currencySymbol}} {{number_format($invoiceList['metaData'][$currencyCode]['total'], 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>
            <tr>
                <td colspan="3" style="border-bottom: 1px solid {{ $template['primary_color'] }}; font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('statement.paidTotal')</td>
                <td colspan="4" class="cs-4" style="border-bottom: 1px solid {{ $template['primary_color'] }}; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{$currencySymbol}} {{number_format($invoiceList['metaData'][$currencyCode]['paid'], 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>
            <tr>
                <td colspan="5" style="padding-top: 5px; font-size: 18px;">@lang('statement.sumToPay')</td>
                <td class="cs-4" colspan="2" style="padding-top: 5px; font-size: 18px; text-align: right; font-weight: 600;">{{$currencySymbol}} {{number_format($invoiceList['metaData'][$currencyCode]['unpaid'], 2, $decimalFormat, $thousandsFormat)}}</td>
            </tr>

            @php($currencyIndex++)
        @endforeach

        </tbody>
    </table>
    <table class="table_sum_total">
        <tbody>
        <tr>
            <td class="half"></td>
            <td style="padding-top: 40px; font-weight: bold; text-align: right;">@lang('invoice.signature'):<br>
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($billedFromCompany->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px;/* -webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right; */ float: right;"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
