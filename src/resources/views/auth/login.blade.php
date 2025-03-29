@extends('layouts.app')

@section('content')
<div class="login-container">
    <h2>ログイン</h2>

    @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">ログインする</button>
    </form>

    <a href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection
