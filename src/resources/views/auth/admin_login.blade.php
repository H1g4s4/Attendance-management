@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endpush
@section('content')
<div class="login-container">
    <h2>管理者ログイン</h2>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

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

        <button type="submit">管理者ログインする</button>
    </form>
</div>
@endsection
