<!doctype html>
@php($dateFormat = $invoice->template_date_format)
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
        #footer table {
            width: 100%;
        }
        #footer td {
            vertical-align: middle;
            color: #6C757D;
            font-size: 11px;
            width: 120px;
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
                        element[j].textContent = vars[css_selector_classes[css_class]];
                    }
                }
            }
        }
    </script>
</head>
<body onload="subst()">
<div id="footer">
    <table>
        <tbody>
        <tr>
            <td>{{$invoice->prefix}}{{$invoice->number}}</td>
            <td style="color: #000;width: auto;text-align: center;"></td>
            <td style="text-align: right;">@lang('invoice.page') <span class="page"></span>/<span class="topage"></span></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
