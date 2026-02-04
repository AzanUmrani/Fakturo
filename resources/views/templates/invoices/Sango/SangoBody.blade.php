<!doctype html>
@php($dateFormat = $invoice->template_date_format)
@php($decimalFormat = $invoice->template_price_decimal_format)
@php($thousandsFormat = $invoice->template_price_thousands_format)
@php($showTax = !empty($invoice->totalPrice_with_tax) && $invoice->totalPrice_with_tax !== $invoice->totalPrice)
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
            <td colspan="@if($showTax) 4 @else 2 @endif" style="width: 460px;">{{$invoice->billed_to_client['name']}}</td>
            <td colspan="1" >@lang('invoice.invoice_number'):</td>
            <td colspan="2" style="font-weight: bold;">{{$invoice->prefix}}{{$invoice->number}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif">{{$invoice->billed_to_client['street']}}</td>
            <td colspan="1">@lang('invoice.invoice_date'):</td>
            <td colspan="2" style="font-weight: bold;">{{date($dateFormat, strtotime($invoice->billed_date))}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif">{{$invoice->billed_to_client['zip']}} {{$invoice->billed_to_client['city']}}</td>
            <td colspan="1">@if($invoice->template_show_due_date)@lang('invoice.due_date'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($invoice->template_show_due_date){{date($dateFormat, strtotime($invoice->due_date))}}@endif</td>
        </tr>
        <tr>
            <td colspan="@if($invoice->order_id) 2 @else 5 @endif" style="padding-bottom: 20px; text-align: left;">@lang("state.{$invoice->billed_to_client['state']}")</td>
            @if($invoice->order_id)
                <td colspan="1">@lang('invoice.order_id'):</td>
                <td colspan="2" style="font-weight: bold;">{{$invoice->order_id}}
            @endif
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($invoice->billed_to_client['identification_number']))@lang('invoice.identification_number'):</span>{{$invoice->billed_to_client['identification_number']}}@endif</td>
            @if($invoice->variable_symbol)
                <td colspan="1">@lang('invoice.variable_symbol'):</td>
                <td colspan="2" style="font-weight: bold;">{{$invoice->variable_symbol}}</td>
            @else
                <td colspan="3"></td>
            @endif

        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($invoice->billed_to_client['vat_identification_number']))@lang('invoice.vat_identification_number'):</span>{{$invoice->billed_to_client['vat_identification_number']}}@endif</td>
            <td colspan="1">@if($invoice->template_show_send_date)@lang('invoice.delivery_date'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($invoice->template_show_send_date){{date($dateFormat, strtotime($invoice->send_date))}}@endif</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($invoice->billed_to_client['vat_identification_number_sk']))@lang('invoice.vat_identification_number_sk'):</span>{{$invoice->billed_to_client['vat_identification_number_sk']}}@endif</td>
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
            @if ($showTax)
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('invoice.taxPercentage')</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('invoice.tax')</td>
            @endif
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('invoice.sum')
                ({{$invoice->getCurrencySymbol()['symbol']}})
            </td>
        </tr>
        @if(count($invoice->items))
            @foreach($invoice->items as $index => $item)
                @php($itemTaxRate = $item['taxRate'] ?? 0)
                <tr>
                    <td colspan="2" style="padding: 7px 0; border-bottom: .5px solid #000">{{$item['name']}}</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($invoice->template_show_quantity){{$item['quantity']}}@endif</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($invoice->template_show_quantity){{number_format(round($item['price'], 2), 2, $decimalFormat, $thousandsFormat)}}@endif</td>
                    @if ($showTax)
                        <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{$itemTaxRate}}</td>
                        <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{number_format(round($item['price'] * (1 + $itemTaxRate / 100) * $item['quantity'] - ($item['price'] * $item['quantity']), 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                        <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{number_format(round($item['price'] * $item['quantity'] * (1 + $itemTaxRate / 100), 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                    @else
                        <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">{{number_format(round($item['price'] * $item['quantity'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if($showTax)
            <tr>
                <td style="border-top: 1px solid #000; padding-top: 5px; font-size: 15px;">@lang('invoice.totalWithoutTax')
                    ({{$invoice->currency_3_code}}):
                </td>
                <td class="cs-4" colspan="6" style="border-top: 1px solid #000; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($invoice->totalPrice, 2), 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
            </tr>
            @php($tmpTaxDiff = 0)
            @if(!empty($invoice->tax_data))
                @foreach($invoice->tax_data as $taxRate => $taxValue)
                    @php($tmpTaxDiff += $taxValue)
                    @php($tmpTaxDiff -= number_format(round($taxValue, 2), 2))
                    <tr>
                        <td style="font-size: 15px;">@lang('invoice.tax') {{$taxRate}}%:</td>
                        <td class="cs-4" colspan="6" style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($taxValue, 2), 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
                    </tr>
                @endforeach
            @endif
            @if(!empty(round($tmpTaxDiff, 2)))
                <tr>
                    <td style="padding-top: 5px; font-size: 15px;">@lang('invoice.roundingDifference'):
                    </td>
                    <td class="cs-4" colspan="6" style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($tmpTaxDiff, 2), 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
                </tr>
            @endif
        @endif
        @if($invoice->payment === 'CASH' && $invoice->currency_3_code === 'EUR')
        <tr>
            <td style="@if(!$showTax) border-top: 1px solid #000;@endif padding-top: 5px; font-size: 15px;">@lang('invoice.cash_payment_rounding'):</td>
            <td class="cs-4" colspan="@if($showTax) 6 @else 4 @endif" style="@if(!$showTax) border-top: 1px solid #000;@endif font-weight: bold; text-align: right; padding-top: 5px; font-size: 15px;">{{number_format(round($invoice->cash_payment_rounding,2), 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
        </tr>
        @endif
        <tr>
            <td style="@if(!$showTax) border-top: 1px solid #000;@endif font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('invoice.sum_total')
                ({{$invoice->currency_3_code}}):
            </td>
            <td class="cs-4" colspan="@if($showTax) 6 @else 4 @endif" style="@if(!$showTax) border-top: 1px solid #000;@endif text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($showTax ? $invoice->getTotalPriceWithTaxAndCashPaymentRounding() : $invoice->getTotalPriceWithCashPaymentRounding(),2), 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 7 @else 5 @endif" style="padding-top: 20px; text-align: right; font-weight: bold;">@lang('invoice.signature'):</td>
        </tr>
        <tr>
            @if($invoice->template_show_qr_payment)
                <td colspan="1"><div class="qr_code">{!! $qr !!}</div></td>
            @endif
            <td @if($invoice->template_show_qr_payment) colspan="@if($showTax) 6 @else 4 @endif" @else colspan="@if($showTax) 7 @else 5 @endif" @endif style="padding-top: 20px; text-align: right;">
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($invoice->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px; /*-webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right;*/"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
