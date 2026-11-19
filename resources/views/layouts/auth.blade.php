<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Кубики в ряд</title>
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
</head>
<body>
<main class="panel">
    <a class="brand" href="{{ url('/') }}">Кубики в ряд</a>
    <h1>@yield('title')</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @yield('content')
</main>
</body>
</html>
