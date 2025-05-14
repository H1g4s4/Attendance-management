@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@section('content')
<div class="login-container">
    <h2>ログイン</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}">
        {{-- フィールド単位のエラー --}}
        @error('email')<div class="error">{{ $message }}</div>@enderror

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        @error('password')<div class="error">{{ $message }}</div>@enderror

        <button type="submit">ログインする</button>
    </form>

    <a href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection
