<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理アプリ')</title>
    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- 勤怠一覧、詳細画面のCSS --}}
    @stack('styles')
</head>
<body>

    @include('layouts.header') <!-- ここで共通ヘッダーを読み込む -->

    <main class="content">
        @yield('content')
    </main>

</body>
</html>
