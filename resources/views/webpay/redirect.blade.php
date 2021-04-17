<?php /** @deprecated Use Laravel's Redirect instead, as it slightly faster */ ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('larabanker::redirect.title')</title>
</head>
<body>
    <form id="redirect" action="{{ $response->getUrl() }}" method="get">
        <input type="hidden" name="token_ws" value="{{ $response->getToken() }}">
    </form>
    <script>
        document.getElementById('redirect').submit();
    </script>
</body>
</html>
