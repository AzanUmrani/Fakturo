<!doctype html>
@php($dateFormat = $preInvoice->template_date_format)
@php($decimalFormat = $preInvoice->template_price_decimal_format)
@php($thousandsFormat = $preInvoice->template_price_thousands_format)
@php($showTax = !empty($preInvoice->totalPrice_with_tax) && $preInvoice->totalPrice_with_tax !== $preInvoice->totalPrice)
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
            color: {{ $preInvoice->template_primary_color }};
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
    <h2 style="font-weight: 400; font-size: 27px; padding-top: 60px; padding-bottom: 10px;text-align: center;">@lang('preInvoice.title')</h2>
    <div class="heading">
        <h1>{{$preInvoice->billed_from_client['name']}}</h1>
        <p style="color: #7a7777"><b>{{$preInvoice->billed_from_client['name']}},</b> {{$preInvoice->billed_from_client['street']}}, {{$preInvoice->billed_from_client['zip']}} {{$preInvoice->billed_from_client['city']}}, @lang("state.{$preInvoice->billed_from_client['state']}")</p>
    </div>
    <table class="table1">
        <thead>
        <tr>
            <th style="text-align: left; font-size: 13px" colspan="5">{{mb_strtoupper(__('preInvoice.receiver'))}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif" style="width: 460px;">{{$preInvoice->billed_to_client['name']}}</td>
            <td colspan="1" >@lang('preInvoice.invoice_number'):</td>
            <td colspan="2" style="font-weight: bold;">{{$preInvoice->prefix}}{{$preInvoice->number}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif">{{$preInvoice->billed_to_client['street']}}</td>
            <td colspan="1">@lang('preInvoice.invoice_date'):</td>
            <td colspan="2" style="font-weight: bold;">{{date($dateFormat, strtotime($preInvoice->billed_date))}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif">{{$preInvoice->billed_to_client['zip']}} {{$preInvoice->billed_to_client['city']}}</td>
            <td colspan="1">@if($preInvoice->template_show_due_date)@lang('preInvoice.due_date'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($preInvoice->template_show_due_date){{date($dateFormat, strtotime($preInvoice->due_date))}}@endif</td>
        </tr>
        <tr>
            <td colspan="@if($preInvoice->order_id) 2 @else 5 @endif" style="padding-bottom: 20px; text-align: left;">@lang("state.{$preInvoice->billed_to_client['state']}")</td>
            @if($preInvoice->order_id)
                <td colspan="1">@lang('preInvoice.order_id'):</td>
                <td colspan="2" style="font-weight: bold;">{{$preInvoice->order_id}}
            @endif
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($preInvoice->billed_to_client['identification_number']))@lang('preInvoice.identification_number'):</span>{{$preInvoice->billed_to_client['identification_number']}}@endif</td>
            @if($preInvoice->variable_symbol)
                <td colspan="1">@lang('preInvoice.variable_symbol'):</td>
                <td colspan="2" style="font-weight: bold;">{{$preInvoice->variable_symbol}}</td>
            @else
                <td colspan="3"></td>
            @endif

        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($preInvoice->billed_to_client['vat_identification_number']))@lang('preInvoice.vat_identification_number'):</span>{{$preInvoice->billed_to_client['vat_identification_number']}}@endif</td>
            <td colspan="1">@if($preInvoice->template_show_send_date)@lang('preInvoice.delivery_date'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($preInvoice->template_show_send_date){{date($dateFormat, strtotime($preInvoice->send_date))}}@endif</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 4 @else 2 @endif"><span style="width: 120px;float: left;display: block;">@if(!empty($preInvoice->billed_to_client['vat_identification_number_sk']))@lang('preInvoice.vat_identification_number_sk'):</span>{{$preInvoice->billed_to_client['vat_identification_number_sk']}}@endif</td>
            <td colspan="1">@if($preInvoice->template_show_payment)@lang('preInvoice.payment_method'):@endif</td>
            <td colspan="2" style="font-weight: bold;">@if($preInvoice->template_show_payment)@lang($preInvoice->payment === 'CASH' ? 'invoice.cashPayment' : 'invoice.bankPayment')@endif</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top: 20px;padding-bottom: 5px;text-align: left;"><i>@lang('preInvoice.billing_you')</i>:</td>
        </tr>
        <tr>
            <td colspan="2" style=" border-bottom: 1px solid #000; padding-bottom: 7px; font-weight: bold;">@lang('preInvoice.description')</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@if($preInvoice->template_show_quantity)@lang('preInvoice.quantity')@endif</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@if($preInvoice->template_show_quantity)@lang('preInvoice.unitSum')@endif</td>
            @if ($showTax)
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('preInvoice.taxPercentage')</td>
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('preInvoice.tax')</td>
            @endif
            <td colspan="1" style=" border-bottom: 1px solid #000; padding-bottom: 7px;text-align: right; font-weight: bold;">@lang('preInvoice.sum')
                ({{$preInvoice->getCurrencySymbol()['symbol']}})
            </td>
        </tr>
        @if(count($preInvoice->items))
            @foreach($preInvoice->items as $index => $item)
                @php($itemTaxRate = $item['taxRate'] ?? 0)
                <tr>
                    <td colspan="2" style="padding: 7px 0; border-bottom: .5px solid #000">{{$item['name']}}</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($preInvoice->template_show_quantity){{$item['quantity']}}@endif</td>
                    <td colspan="1" style="padding: 7px 0; text-align: right; border-bottom: .5px solid #000">@if($preInvoice->template_show_quantity){{number_format(round($item['price'], 2), 2, $decimalFormat, $thousandsFormat)}}@endif</td>
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
                <td style="border-top: 1px solid #000; padding-top: 5px; font-size: 15px;">@lang('preInvoice.totalWithoutTax')
                    ({{$preInvoice->currency_3_code}}):
                </td>
                <td class="cs-4" colspan="6" style="border-top: 1px solid #000; text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($preInvoice->totalPrice, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</td>
            </tr>
            @php($tmpTaxDiff = 0)
            @if(!empty($preInvoice->tax_data))
                @foreach($preInvoice->tax_data as $taxRate => $taxValue)
                    @php($tmpTaxDiff += $taxValue)
                    @php($tmpTaxDiff -= number_format(round($taxValue, 2), 2))
                    <tr>
                        <td style="font-size: 15px;">@lang('preInvoice.tax') {{$taxRate}}%:</td>
                        <td class="cs-4" colspan="6" style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($taxValue, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</td>
                    </tr>
                @endforeach
            @endif
            @if(!empty(round($tmpTaxDiff, 2)))
                <tr>
                    <td style="padding-top: 5px; font-size: 15px;">@lang('preInvoice.roundingDifference'):
                    </td>
                    <td class="cs-4" colspan="6" style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($tmpTaxDiff, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</td>
                </tr>
            @endif
        @endif
        @if($preInvoice->payment === 'CASH' && $preInvoice->currency_3_code === 'EUR')
        <tr>
            <td style="@if(!$showTax) border-top: 1px solid #000;@endif padding-top: 5px; font-size: 15px;">@lang('preInvoice.cash_payment_rounding'):</td>
            <td class="cs-4" colspan="@if($showTax) 6 @else 4 @endif" style="@if(!$showTax) border-top: 1px solid #000;@endif font-weight: bold; text-align: right; padding-top: 5px; font-size: 15px;">{{number_format(round($preInvoice->cash_payment_rounding,2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</td>
        </tr>
        @endif
        <tr>
            <td style="@if(!$showTax) border-top: 1px solid #000;@endif font-weight: bold; padding-top: 5px; font-size: 15px;">@lang('preInvoice.sum_total')
                ({{$preInvoice->currency_3_code}}):
            </td>
            <td class="cs-4" colspan="@if($showTax) 6 @else 4 @endif" style="@if(!$showTax) border-top: 1px solid #000;@endif text-align: right; font-weight: bold; padding-top: 5px; font-size: 15px;">{{number_format(round($showTax ? $preInvoice->getTotalPriceWithTaxAndCashPaymentRounding() : $preInvoice->getTotalPriceWithCashPaymentRounding(),2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</td>
        </tr>
        <tr>
            <td colspan="@if($showTax) 7 @else 5 @endif" style="padding-top: 20px; text-align: right; font-weight: bold;">@lang('preInvoice.signature'):</td>
        </tr>
        <tr>
            @if($preInvoice->template_show_qr_payment)
                <td colspan="1"><div class="qr_code">{!! $qr !!}</div></td>
            @endif
            <td @if($preInvoice->template_show_qr_payment) colspan="@if($showTax) 6 @else 4 @endif" @else colspan="@if($showTax) 7 @else 5 @endif" @endif style="padding-top: 20px; text-align: right;">
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($preInvoice->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px; /*-webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right;*/"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
