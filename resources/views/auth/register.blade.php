@extends('layouts.auth')

@section('title', 'Регистрация')

@section('content')
    <form class="form" method="POST" action="{{ route('register') }}">
        @csrf
        <label class="field">
            <span>Имя</span>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="nickname" maxlength="50">
        </label>
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" name="password" required autocomplete="new-password" minlength="8">
        </label>
        <label class="field">
            <span>Пароль ещё раз</span>
            <input type="password" name="password_confirmation" required autocomplete="new-password" minlength="8">
        </label>
        <button class="btn" type="submit">Создать аккаунт</button>
    </form>

    <p class="alt">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
@endsection
