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

        .table_main td .line {
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
            margin: 20px 0;
            background: #F2F2F2;
            box-shadow: 0 0 0 10px #F2F2F2, 0 0 0 12px #CCCCCC;
        }

        .gray {
            padding: 15px;
        }

        .items .head td {
            border-bottom: 2px solid #ccc;
            padding: 7px;
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
            margin: 20px 0 40px;
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
        <h1 style="color: {{ $preInvoice->template_primary_color }};">@lang('preInvoice.title') {{$preInvoice->prefix}}{{$preInvoice->number}}</h1>
        <p>@if($preInvoice->order_id)@lang('preInvoice.order_id'): {{$preInvoice->order_id}}@endif</p>
    </div>
    <table class="table_main">
        <tbody>
        <td class="half">
            <span class="line td_title">@lang('preInvoice.supplier')</span>
            <span class="line name">{{$preInvoice->billed_from_client['name']}}</span>
            <span class="line street">{{$preInvoice->billed_from_client['street']}}</span>
            <span class="line city">{{$preInvoice->billed_from_client['zip']}} {{$preInvoice->billed_from_client['city']}}</span>
            <span class="line state">@lang("state.{$preInvoice->billed_from_client['state']}")</span>
            <span class="line ico_dic"><span class="ico" style="margin-right: 15px;">@lang('preInvoice.identification_number'): {{$preInvoice->billed_from_client['identification_number']}}</span><span class="dic">@lang('preInvoice.vat_identification_number'): {{$preInvoice->billed_from_client['vat_identification_number']}}</span></span>
            <span class="line dph">@if(!empty($preInvoice->billed_from_client['vat_identification_number_sk']))@lang('preInvoice.vat_identification_number_sk')
                {{$preInvoice->billed_from_client['vat_identification_number_sk']}} @else @lang('preInvoice.vat_number_missing')@endif
            </span>
        </td>
        <td class="half">
            <span class="line td_title">{{mb_strtoupper(__('preInvoice.receiver'))}}</span>
            <span class="line name">{{$preInvoice->billed_to_client['name']}}</span>
            <span class="line street">{{$preInvoice->billed_to_client['street']}}</span>
            <span class="line city">{{$preInvoice->billed_to_client['zip']}} {{$preInvoice->billed_to_client['city']}}</span>
            <span class="line state">@lang("state.{$preInvoice->billed_to_client['state']}")</span>
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
                        <td>@lang('preInvoice.invoice_date'):</td>
                        <td>{{date($dateFormat, strtotime($preInvoice->billed_date))}}</td>
                    </tr>
                    <tr>
                        <td>@if($preInvoice->template_show_due_date)@lang('preInvoice.due_date'):@endif</td>
                        <td><strong>@if($preInvoice->template_show_due_date){{date($dateFormat, strtotime($preInvoice->due_date))}}@endif</strong></td>
                    </tr>
                    @if($preInvoice->template_show_send_date)
                        <tr>
                            <td>@lang('preInvoice.delivery_date'):</td>
                            <td>{{date($dateFormat, strtotime($preInvoice->send_date))}}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </td>
            <td>
                <span class="line">
                    <span class="ico"
                          style="margin-right: 20px;">@if(!empty($preInvoice->billed_to_client['identification_number']))@lang('preInvoice.identification_number'): {{$preInvoice->billed_to_client['identification_number']}}@endif</span>
                <span class="dic"
                      style="margin-right: 20px;">@if(!empty($preInvoice->billed_to_client['vat_identification_number']))@lang('preInvoice.vat_identification_number'): {{$preInvoice->billed_to_client['vat_identification_number']}}@endif</span>
               <span class="ic_dph">
                   @if(!empty($preInvoice->billed_to_client['vat_identification_number_sk']))
                       @lang('preInvoice.vat_identification_number_sk'): {{$preInvoice->billed_to_client['vat_identification_number_sk']}}
                   @endif
               </span>
                </span>
                <table class="table_gray">
                    <tbody>
                    <tr>
                        <td class="gray">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <span class="line">@if($preInvoice->template_show_payment)@lang('preInvoice.payment_method'): @lang($preInvoice->payment === 'CASH' ? 'invoice.cashPayment' : 'invoice.bankPayment')@endif</span>
                                        <span class="line strong">@lang('preInvoice.sum'): <strong>{{number_format(round($showTax ? $preInvoice->totalPrice_with_tax : $preInvoice->totalPrice,2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</strong></span>
                                        <span class="line">@lang('preInvoice.variable_symbol'): <strong>{{$preInvoice->variable_symbol}}</strong></span>
                                        <span class="line">@if(!empty($preInvoice->bank_transfer['iban']))IBAN: <strong>{{$preInvoice->bank_transfer['iban'] ?? ''}}@endif</strong></span>
                                        <span class="line">@if(!empty($preInvoice->bank_transfer['swift']))SWIFT: {{$preInvoice->bank_transfer['swift'] ?? ''}}@endif</span>
                                    </td>
                                    @if($preInvoice->template_show_qr_payment)<td class="qr_code">{!! $qr !!}</td>@endif
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
    <table class="items">
        <tbody>
        <tr>
            <td colspan="5" style="padding-top: 20px;padding-bottom: 5px;text-align: left;">
                @lang('preInvoice.billing_you'):
            </td>
        </tr>
        <tr class="head">
            <td class="to_left">@lang('preInvoice.no_number')</td>
            <td class="to_left">@lang('preInvoice.description')</td>
            <td>@if($preInvoice->template_show_quantity)@lang('preInvoice.quantity')@endif</td>
            <td>@if($preInvoice->template_show_quantity)@lang('preInvoice.unitSum')@endif</td>
            @if ($showTax)
                <td>@if($preInvoice->template_show_quantity)@lang('preInvoice.taxPercentage')@endif</td>
                <td>@if($preInvoice->template_show_quantity)@lang('preInvoice.tax')@endif</td>
            @endif
            <td>@lang('preInvoice.sum')
                ({{$preInvoice->getCurrencySymbol()['symbol']}})
            </td>
        </tr>
        @if(count($preInvoice->items))
            @foreach($preInvoice->items as $index => $item)
                @php($itemTaxRate = $item['taxRate'] ?? 0)
                <tr class="item">
                    <td class="to_left">{{$index+1}}.</td>
                    <td class="to_left">{{$item['name']}}</td>
                    <td>@if($preInvoice->template_show_quantity){{$item['quantity']}}@endif</td>
                    <td>@if($preInvoice->template_show_quantity){{number_format($item['price'], 2, $decimalFormat, $thousandsFormat)}}@endif</td>
                    @if ($showTax)
                        <td>{{$itemTaxRate}}</td>
                        <td>{{number_format(round($item['price'] * (1 + $itemTaxRate / 100) * $item['quantity'] - ($item['price'] * $item['quantity']), 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                        <td>{{number_format(round($item['price'] * $item['quantity'] * (1 + $itemTaxRate / 100), 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                    @else
                        <td>{{number_format(round($item['price'] * $item['quantity'], 2), 2, $decimalFormat, $thousandsFormat)}}</td>
                    @endif
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <table class="table_sum_total">
        <tbody>
        @if($showTax)
            <tr>
                <td class="half"></td>
                <td class="half">@lang('preInvoice.totalWithoutTax'): <span style="float:right;line-height: 1;font-size: 16px;">{{number_format(round($preInvoice->totalPrice, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</span></td>
            </tr>
            @php($tmpTaxDiff = 0)
            @if(!empty($preInvoice->tax_data))
                @foreach($preInvoice->tax_data as $taxRate => $taxValue)
                    @php($tmpTaxDiff += $taxValue)
                    @php($tmpTaxDiff -= number_format(round($taxValue, 2), 2))
                    <tr>
                        <td class="half"></td>
                        <td class="half">@lang('preInvoice.tax') {{$taxRate}}%: <span style="float:right;line-height: 1;font-size: 16px;">{{number_format(round($taxValue, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</span></td>
                    </tr>
                @endforeach
            @endif
            @if(!empty(round($tmpTaxDiff, 2)))
                <tr>
                    <td class="half"></td>
                    <td class="half">@lang('preInvoice.roundingDifference'): <span style="float:right;line-height: 1;font-size: 16px;">{{number_format(round($tmpTaxDiff, 2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</span></td>
                </tr>
            @endif
        @endif
        @if($preInvoice->payment === 'CASH' && $preInvoice->currency_3_code === 'EUR')
            <tr>
                <td class="half"></td>
                <td class="half">@lang('preInvoice.cash_payment_rounding'): <span style="float:right;line-height: 1;font-size: 16px;">{{number_format(round($preInvoice->cash_payment_rounding,2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</span></td>
            </tr>
        @endif
        @if($showTax || $preInvoice->payment === 'CASH' && $preInvoice->currency_3_code === 'EUR')
            <tr style="">
                <td class="half"></td>
                <td class="half" style="color: transparent">-</td>
            </tr>
        @endif
        <tr>
            <td class="half" ></td>
            <td class="half sum_total">
                @lang('preInvoice.sum_total'): <span style="float:right;">{{number_format(round($showTax ? $preInvoice->getTotalPriceWithTaxAndCashPaymentRounding() : $preInvoice->getTotalPriceWithCashPaymentRounding(),2), 2, $decimalFormat, $thousandsFormat)}} {{$preInvoice->getCurrencySymbol()['symbol']}}</span></td>
        </tr>
        <tr>
            <td class="half"></td>
            <td style="padding-top: 40px; font-weight: bold;">@lang('preInvoice.signature'):<br>
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($preInvoice->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px;/* -webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right; */ float: right;"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
