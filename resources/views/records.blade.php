<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Рекорды — Кубики в ряд</title>
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
</head>
<body>
<main class="panel wide">
    <a class="brand" href="{{ url('/') }}">Кубики в ряд</a>
    <h1>Рекорды</h1>

    @if ($records->isEmpty())
        <p class="empty">Пока никто не сохранил рекорд. Сыграйте партию под своим аккаунтом.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Игрок</th>
                <th>Рекорд</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($records as $record)
                <tr>
                    <td class="rank">{{ $loop->iteration }}</td>
                    <td>{{ $record->user->name }}</td>
                    <td class="score">{{ $record->score }}</td>
                    <td>{{ $record->created_at->format('d.m.Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <p class="alt"><a href="{{ url('/') }}">← Вернуться к игре</a></p>
</main>
</body>
</html>
