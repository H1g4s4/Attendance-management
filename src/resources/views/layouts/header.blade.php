<header class="header">
    <div class="header-container">
        <div class="logo-container">
        @php
            if (Auth::guard('admin')->check()) {
                $logoRoute = route('admin.attendance.index');
            } elseif (Auth::check()) {
                $logoRoute = route('user.attendance');
            } else {
                $logoRoute = route('login');
            }
        @endphp

        <a href="{{ $logoRoute }}">
            <img src="{{ asset('images/CoachTech_White.png') }}" alt="COACHTECH" class="logo">
        </a>
    </div>

        @auth
        <!-- ナビゲーションメニュー（右側） -->
        <nav class="nav">
            <ul>
            @if(Auth::guard('admin')->check())
                <!-- 管理者用メニュー -->
                <li><a href="{{ route('admin.attendance.index') }}">勤怠一覧</a></li>
                <li><a href="{{ route('admin.staff.index') }}">スタッフ一覧</a></li>
                <li><a href="{{ route('admin.requests.index') }}">申請一覧</a></li>
            @elseif(Auth::guard('web')->check())
                <!-- 一般ユーザー用メニュー -->
                <li><a href="{{ route('user.attendance') }}">勤怠</a></li>
                <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
                <li><a href="{{ route('user.requests') }}">申請</a></li>
            @endif

            <!-- 共通ログアウト -->
            <li class="logout-item">
                <form method="POST"
                action="{{ Auth::guard('admin')->check() ? route('admin.logout') : route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">ログアウト</button>
                </form>
            </li>
            </ul>
        </nav>
        @endauth
    </div>
</header>
