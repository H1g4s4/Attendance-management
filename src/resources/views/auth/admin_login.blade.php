@extends('layouts.app')

@section('content')
<div class="login-container">
    <h2>管理者ログイン</h2>

    @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">管理者ログインする</button>
    </form>
</div>
@endsection
