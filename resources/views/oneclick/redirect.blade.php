<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('larabanker::redirect.title')</title>
</head>
<body>
    <form id="redirect" action="{{ $response->getUrl() }}" method="post">
        <input type="hidden" name="TBK_TOKEN" value="{{ $response->getToken() }}">
    </form>
    <script>
        document.getElementById('redirect').submit();
    </script>
</body>
</html>
