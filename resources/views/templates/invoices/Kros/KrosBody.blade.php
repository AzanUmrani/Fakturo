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
        <h1 style="color: {{ $invoice->template_primary_color }};">@lang('invoice.title') {{$invoice->prefix}}{{$invoice->number}}</h1>
        <p>@if($invoice->order_id)@lang('invoice.order_id'): {{$invoice->order_id}}@endif</p>
    </div>
    <table class="table_main">
        <tbody>
        <td class="half">
            <span class="line td_title">@lang('invoice.supplier')</span>
            <span class="line name">{{$invoice->billed_from_client['name']}}</span>
            <span class="line street">{{$invoice->billed_from_client['street']}}</span>
            <span class="line city">{{$invoice->billed_from_client['zip']}} {{$invoice->billed_from_client['city']}}</span>
            <span class="line state">@lang("state.{$invoice->billed_from_client['state']}")</span>
            <span class="line ico_dic"><span class="ico" style="margin-right: 15px;">@lang('invoice.identification_number'): {{$invoice->billed_from_client['identification_number']}}</span><span class="dic">@lang('invoice.vat_identification_number'): {{$invoice->billed_from_client['vat_identification_number']}}</span></span>
            <span class="line dph">@if(!empty($invoice->billed_from_client['vat_identification_number_sk']))@lang('invoice.vat_identification_number_sk')
                {{$invoice->billed_from_client['vat_identification_number_sk']}} @else @lang('invoice.vat_number_missing')@endif
            </span>
        </td>
        <td class="half">
            <span class="line td_title">{{mb_strtoupper(__('invoice.receiver'))}}</span>
            <span class="line name">{{$invoice->billed_to_client['name']}}</span>
            <span class="line street">{{$invoice->billed_to_client['street']}}</span>
            <span class="line city">{{$invoice->billed_to_client['zip']}} {{$invoice->billed_to_client['city']}}</span>
            <span class="line state">@lang("state.{$invoice->billed_to_client['state']}")</span>
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
                        <td>@lang('invoice.invoice_date'):</td>
                        <td>{{date($dateFormat, strtotime($invoice->billed_date))}}</td>
                    </tr>
                    <tr>
                        <td>@if($invoice->template_show_due_date)@lang('invoice.due_date'):@endif</td>
                        <td><strong>@if($invoice->template_show_due_date){{date($dateFormat, strtotime($invoice->due_date))}}@endif</strong></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <span class="line">
                    <span class="ico"
                          style="margin-right: 20px;">@lang('invoice.identification_number'): {{$invoice->billed_to_client['identification_number']}}</span>
                <span class="dic"
                      style="margin-right: 20px;">@lang('invoice.vat_identification_number'): {{$invoice->billed_to_client['vat_identification_number']}}</span>
               <span class="ic_dph">@lang('invoice.vat_identification_number_sk'): {{$invoice->billed_to_client['vat_identification_number_sk']}}</span>
                </span>
                <table class="table_gray">
                    <tbody>
                    <tr>
                        <td class="gray">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <span class="line">@if($invoice->template_show_payment)@lang('invoice.payment_method'): @lang($invoice->payment === 'CASH' ? 'invoice.cashPayment' : 'invoice.bankPayment')@endif</span>
                                        <span class="line strong">@lang('invoice.sum'): <strong>{{number_format($invoice->totalPrice, 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</strong></span>
                                        <span class="line">@lang('invoice.variable_symbol'): <strong>{{$invoice->variable_symbol}}</strong></span>
                                        <span class="line">IBAN: <strong>{{$invoice->bank_transfer['iban'] ?? ''}}</strong></span>
                                        <span class="line">SWIFT: {{$invoice->bank_transfer['swift'] ?? ''}}</span>
                                    </td>
                                    @if($invoice->template_show_qr_payment)<td class="qr_code">{!! $qr !!}</td>@endif
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
                @lang('invoice.billing_you'):
            </td>
        </tr>
        <tr class="head">
            <td class="to_left">@lang('invoice.no_number')</td>
            <td class="to_left">@lang('invoice.description')</td>
            <td>@if($invoice->template_show_quantity)@lang('invoice.quantity')@endif</td>
            <td>@if($invoice->template_show_quantity)@lang('invoice.unitSum')@endif</td>
            <td>@lang('invoice.sum')
                ({{$invoice->getCurrencySymbol()['symbol']}})
            </td>
        </tr>
        @if(count($invoice->items))
            @foreach($invoice->items as $index => $item)
                <tr class="item">
                    <td class="to_left">{{$index+1}}.</td>
                    <td class="to_left">{{$item['name']}}</td>
                    <td>@if($invoice->template_show_quantity){{$item['quantity']}}@endif</td>
                    <td>@if($invoice->template_show_quantity){{number_format($item['price'], 2, $decimalFormat, $thousandsFormat)}}@endif</td>
                    <td>{{number_format($item['price'] * $item['quantity'], 2, $decimalFormat, $thousandsFormat)}}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <table class="table_sum_total">
        <tbody>
        <tr>
            <td class="half" ></td>
            <td class="half sum_total">
                @lang('invoice.sum_total'): <span style="float:right;">{{number_format($invoice->totalPrice, 2, $decimalFormat, $thousandsFormat)}} {{$invoice->getCurrencySymbol()['symbol']}}</span></td>
        </tr>
        <tr>
            <td class="half"></td>
            <td style="padding-top: 40px; font-weight: bold;">@lang('invoice.signature'):<br>
                <img src="data:image/png;base64, {{base64_encode(Storage::disk('local')->get($invoice->company->getSignaturePath()))}}"
                     style="display:inline-block; max-width: 200px; max-height: 250px;/* -webkit-transform: rotate(90deg) translateX(100%); transform: rotate(90deg) translateX(100%);  -webkit-transform-origin: top right; transform-origin: top right; */ float: right;"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
