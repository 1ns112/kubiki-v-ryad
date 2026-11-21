<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Кубики в ряд</title>
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
</head>
<body>
<main class="game">
    <nav class="nav">
        <a href="{{ route('records') }}">Рекорды</a>
        @if (auth()->user()?->isAdmin())
            <a href="{{ route('admin.index') }}">Админка</a>
        @endif
        <span class="spacer"></span>
        @auth
            <span class="who">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Выйти</button>
            </form>
        @else
            <a href="{{ route('login') }}">Войти</a>
            <a href="{{ route('register') }}">Регистрация</a>
        @endauth
    </nav>

    <header class="hud">
        <div class="stat">
            <span class="label">Счёт</span>
            <strong id="score">0</strong>
        </div>
        <h1 class="title">Кубики <br><span>в ряд</span></h1>
        <div class="stat right">
            <span class="label">Рекорд</span>
            <strong id="best">0</strong>
        </div>
    </header>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <div class="board-wrap">
        <div id="board" class="board" role="group" aria-label="Игровое поле 8 на 8"></div>
        <div id="overlay" class="overlay hidden">
            <p class="over-title">Игра окончена</p>
            <p class="over-score">Счёт: <span id="finalScore">0</span></p>
            <button id="restart" class="btn" type="button">Играть снова</button>
        </div>
    </div>

    <div id="tray" class="tray" aria-label="Доступные фигуры"></div>

    <p class="hint">Выберите фигуру и нажмите на поле. Полные линии исчезают.</p>
</main>

<script>
    window.APP_AUTH = @json(auth()->check());
    window.APP_BEST = @json((int) $userBest);
</script>
<script src="{{ asset('js/block-blast.js') }}"></script>
</body>
</html>
