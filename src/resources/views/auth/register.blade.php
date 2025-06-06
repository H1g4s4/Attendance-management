@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush
@section('content')
<div class="register-container">
    <h2>会員登録</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">名前</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}">
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_confirmation">パスワード確認</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
        @error('password_confirmation')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">登録する</button>
    </form>

    <a href="{{ route('login') }}">ログインはこちら</a>
</div>
@endsection
