<!doctype html>
@php($dateFormat = $template['formats']['date'] ?? 'd.m.Y')
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
        }

        #footer-space {
            height: 6px;
        }

        #footer {
            padding-top: 15px;
            margin-left: 63px;
            margin-right: 63px;
            font-weight: 400;
            font-size: 0;
            border-top: 1px solid #DDD
        }

        #footer > * {
            font-size: 12px;
            line-height: 1.2;
        }

        .footer_data span {
            display: inline-block;
            padding-right: 10px;
            white-space: nowrap;
        }
    </style>
    <script>
        function subst() {
            var vars = {};
            var query_strings_from_url = document.location.search.substring(1).split('&');
            for (var query_string in query_strings_from_url) {
                if (query_strings_from_url.hasOwnProperty(query_string)) {
                    var temp_var = query_strings_from_url[query_string].split('=', 2);
                    vars[temp_var[0]] = decodeURI(temp_var[1]);
                }
            }
            var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
            for (var css_class in css_selector_classes) {
                if (css_selector_classes.hasOwnProperty(css_class)) {
                    var element = document.getElementsByClassName(css_selector_classes[css_class]);
                    for (var j = 0; j < element.length; ++j) {
                        element[j].textContent = vars[css_selector_classes[css_class]].trim();
                    }
                }
            }
        }
    </script>
</head>
<body onload="subst()">
<div id="footer-space"></div>
<div id="footer">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td class="footer_data" style="vertical-align: bottom;">
                <span><strong>{{$billedFromCompany['name']}}</strong>, {{$billedFromCompany['street']}}, {{$billedFromCompany['zip']}} {{$billedFromCompany['city']}}, @lang("state.{$billedFromCompany['state']}")</span>
                <span><strong>@lang('invoice.identification_number'):</strong> {{$billedFromCompany['identification_number']}}</span>
                <span><strong>@lang('invoice.vat_identification_number'):</strong> {{$billedFromCompany['vat_identification_number']}}</span>
                <span>@if(!empty($billedFromCompany['vat_identification_number_sk']))@lang('invoice.vat_identification_number_sk')
                    :</span>{{$billedFromCompany['vat_identification_number_sk']}} @else
                    <strong>@lang('invoice.vat_number_missing')</strong> </span>
                @endif
                @if(!empty($invoice->billed_from_client['registry_info']))
                <br>
                <span><strong>@lang('invoice.registry_info'):</strong> {{$invoice->billed_from_client['registry_info']}}</span>
                @endif
                <span><strong>@lang('invoice.contactPerson'):</strong> {{$billedFromCompany['contact_name']}}</span>
                <span><strong>@lang('invoice.phone'):</strong> {{$billedFromCompany['contact_phone']}}</span>
                @if(!empty($billedFromCompany['contact_web']))
                    <span><strong>Web:</strong> {{$billedFromCompany['contact_web']}}</span>
                @endif
                @if(!empty($billedFromCompany['contact_email']))
                    <span><strong>E-mail:</strong> {{$billedFromCompany['contact_email']}}</span>
                @endif
                <br>
                @if(isset($billedFromCompany['payment_methods']['bank_transfer']['name']))
                    <span class="normal">@lang('invoice.bank'): <strong>{{$billedFromCompany['payment_methods']['bank_transfer']['name']}}</strong></span>
                @endif
                @if(isset($billedFromCompany['payment_methods']['bank_transfer']['code']))
                    <span class="normal">@lang('invoice.bankCode'): <strong>{{$billedFromCompany['payment_methods']['bank_transfer']['code']}}</strong></span>
                @endif
                @if(isset($billedFromCompany['payment_methods']['bank_transfer']['iban']))
                    <span class="normal">IBAN: <strong>{{$billedFromCompany['payment_methods']['bank_transfer']['iban']}}</strong></span>
                @endif
                @if(isset($billedFromCompany['payment_methods']['bank_transfer']['swift']))
                    <span class="normal">SWIFT: <strong>{{$billedFromCompany['payment_methods']['bank_transfer']['swift']}}</strong></span>
                @endif
            </td>
            <td style="text-align: right;vertical-align: bottom;white-space:nowrap;padding-left: 15px;">{{--@lang('invoice.page')--}}<span class="page"></span>/<span class="topage"></span></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
