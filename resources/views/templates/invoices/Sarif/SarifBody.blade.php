<!doctype html>
@php($dateFormat = $invoice->template_date_format)
@php($decimalFormat = $invoice->template_price_decimal_format)
@php($thousandsFormat = $invoice->template_price_thousands_format)
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
            color: {{ $invoice->template_primary_color }};
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
<div id="body">
    <h2 style="font-weight: 400; font-size: 27px; padding-top: 60px; padding-bottom: 10px;text-align: center;">@lang('invoice.title')</h2>
    <div class="heading">
        <h1>{{$invoice->billed_from_client['name']}}</h1>
        <p style="color: #7a7777"><b>{{$invoice->billed_from_client['name']}},</b> {{$invoice->billed_from_client['street']}}, {{$invoice->billed_from_client['zip']}} {{$invoice->billed_from_client['city']}}, @lang("state.{$invoice->billed_from_client['state']}")</p>
    </div>
    <table class="table1">
        <thead>
        <tr>
            <th style="text-align: left; font-size: 13px" colspan="5">{{mb_strtoupper(__('invoice.receiver'))}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2" style="width: 460px;">{{$invoice->billed_to_client['name']}}</td>
            <td colspan="1" >@lang('invoice.invoice_number'):</td>
            <td colspan="2" style="font-weight: bold;">{{$invoice->prefix}}{{$invoice->number}}</td>
        </tr>
        <tr>
            <td colspan="2">{{$invoice->billed_to_client['street']}}</td>
            <td colspan="1">@lang('invoice.invoice_date'):</td>
            <td colspan="2" style="font-weight: bold;">{{date($dateFormat, strtotime($invoice->billed_date))}}</td>
        </tr>
        <tr>
            <td colspan="2">{{$invoice->billed_to_client['zip']}} {{$invoice->billed_to_client['city']}}</td>
            <td colspan="1">@if($invoice->template_show_due_date)@lang('invoice.due_date'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($invoice->template_show_due_date){{date($dateFormat, strtotime($invoice->due_date))}}@endif</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-bottom: 20px; text-align: left;">@lang("state.{$invoice->billed_to_client['state']}")</td>
        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@lang('invoice.identification_number'):</span>{{$invoice->billed_to_client['identification_number']}}</td>
            @if($invoice->variable_symbol)
                <td colspan="1">@lang('invoice.variable_symbol'):</td>
                <td colspan="2" style="font-weight: bold;">{{$invoice->variable_symbol}}</td>
            @else
                <td colspan="3"></td>
            @endif

        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@lang('invoice.vat_identification_number'):</span>{{$invoice->billed_to_client['vat_identification_number']}}</td>
            <td colspan="1">@lang('invoice.delivery_date'):</td>
            <td colspan="2" style="font-weight: bold;">{{date($dateFormat, strtotime($invoice->send_date))}}</td>
        </tr>
        <tr>
            <td colspan="2"><span style="width: 120px;float: left;display: block;">@lang('invoice.vat_identification_number_sk'):</span>{{$invoice->billed_to_client['vat_identification_number_sk']}}</td>
            <td colspan="1">@if($invoice->template_show_payment)@lang('invoice.payment_method'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($invoice->template_show_payment)@lang($invoice->payment === 'CASH' ? 'invoice.cashPayment' : 'invoice.bankPayment')@endif</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px;padding-bottom: 5px;text-align: left;"><i>@lang('invoice.billing_you')</i>:</td>
        </tr>
        <tr>
            <td colspan="2" style=" border-bottom: 1px solid #000; padding-bottom: 7px; font-weight: bold;">@lang('invoice.description')</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@if($invoice->template_show_quantity)@lang('invoice.quantity')@endif</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@if($invoice->template_show_quantity)@lang('invoice.unitSum')@endif</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('invoice.sum')
                ({{$invoice->getCurrencySymbol()['symbol']}})
            </td>
        </tr>
        @if(count($invoice->items))
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td colspan="2" style="padding: 7px 0; border-bottom: .5px solid #000">{{$item['name']}}</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($invoice->template_show_quantity){{$item['quantity']}}@endif</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($invoice->template_show_quantity){{number_format($item['price'], 2, $decimalFormat, $thousandsFormat)}}@endif</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{number_format($item['price'] * $item['quantity'], 2, $decimalFormat, $thousandsFormat)}}</td>
                </tr>
            @endforeach
        @endif
        <tr>
            <td style="border-top: 1px solid #000; font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('invoice.sum_total')
                ({{$invoice->currency_3_code}}):
            </td>
            <td class="cs-4" colspan="4" style="border-top: 1px solid #000; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format($invoice->totalPrice, 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px; text-align: right; font-weight: bold;">@lang('invoice.signature'):</td>
        </tr>
        <tr>
            @if($invoice->template_show_qr_payment)
                <td colspan="1"><div class="qr_code">{!! $qr !!}</div></td>
            @endif
            <td @if($invoice->template_show_qr_payment) colspan="4" @else colspan="5" @endif style="padding-top: 20px; text-align: right;">
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($invoice->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px; /*-webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right;*/"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
