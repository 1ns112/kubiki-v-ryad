@extends('layouts.auth')

@section('title', 'Вход')

@section('content')
    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <label class="checkbox">
            <input type="checkbox" name="remember" value="1">
            <span>Запомнить меня</span>
        </label>
        <button class="btn" type="submit">Войти</button>
    </form>

    <p class="alt">Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
@endsection
