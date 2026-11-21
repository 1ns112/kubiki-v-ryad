<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Админка — Кубики в ряд</title>
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
</head>
<body>
<main class="panel xwide">
    <a class="brand" href="{{ url('/') }}">Кубики в ряд</a>
    <h1>Админка</h1>

    @if (session('status'))
        <p class="notice">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="notice error">{{ session('error') }}</p>
    @endif

    <h2>Пользователи</h2>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Игрок</th>
                <th>Роль</th>
                <th>Рекорд</th>
                <th>Партий</th>
                <th>Регистрация</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="rank">{{ $user->id }}</td>
                    <td>
                        {{ $user->name }}
                        <div class="email">{{ $user->email }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $user->isAdmin() ? 'admin' : 'player' }}">
                            {{ $user->isAdmin() ? 'админ' : 'игрок' }}
                        </span>
                    </td>
                    <td class="score">{{ $user->records_max_score ?? '—' }}</td>
                    <td>{{ $user->records_count }}</td>
                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                    <td>
                        <div class="actions">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn-mini" type="submit">
                                    {{ $user->isAdmin() ? 'Снять админа' : 'Сделать админом' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Удалить пользователя {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-mini danger" type="submit">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h2>Последние рекорды</h2>
    @if ($records->isEmpty())
        <p class="empty">Рекордов пока нет.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Игрок</th>
                    <th>Рекорд</th>
                    <th>Дата</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->user->name }}</td>
                        <td class="score">{{ $record->score }}</td>
                        <td>{{ $record->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <p class="alt"><a href="{{ url('/') }}">← Вернуться к игре</a></p>
</main>
</body>
</html>
