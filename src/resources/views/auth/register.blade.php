@extends('layouts.app')

@section('content')
<div class="register-container">
    <h2>会員登録</h2>

    @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">名前</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">パスワード確認</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit">登録する</button>
    </form>

    <a href="{{ route('login') }}">ログインはこちら</a>
</div>
@endsection
