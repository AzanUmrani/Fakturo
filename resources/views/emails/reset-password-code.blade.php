<!DOCTYPE html>
<html>
<head>
    <title>{{ __('passwords.reset_title') }}</title>
</head>
<body>
<h1>{{ __('passwords.reset_title') }}</h1>
<p>{{ __('passwords.reset_requested') }}</p>
<h2 style="font-size: 24px; letter-spacing: 2px; text-align: center; padding: 10px; background-color: #f5f5f5; border-radius: 5px;">
    {{ $code }}
</h2>
<p>{{ __('passwords.code_expires') }}</p>
<p>{{ __('passwords.not_requested') }}</p>
</body>
</html>
