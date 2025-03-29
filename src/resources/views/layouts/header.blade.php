
<header class="header">
    <div class="header-container">
        <!-- ロゴ部分 -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/CoachTech_White.png') }}" alt="COACHTECH" class="logo">
        </a>

        <!-- ナビゲーションメニュー -->
        <nav class="nav">
            <ul>
                @auth
                    @if(Auth::guard('admin')->check())
                        <!-- 管理者用メニュー -->
                        <li><a href="{{ route('admin.attendance.index') }}">勤怠一覧</a></li>
                        <li><a href="{{ route('admin.staff.index') }}">スタッフ一覧</a></li>
                        <li><a href="{{ route('admin.requests.index') }}">申請一覧</a></li>
                    @else
                        <!-- 一般ユーザー用メニュー -->
                        <li><a href="{{ route('user.attendance') }}">勤怠</a></li>
                        <li><a href="{{ route('user.attendance.index') }}">勤怠一覧</a></li>
                        <li><a href="{{ route('user.requests') }}">申請</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route(Auth::guard('admin')->check() ? 'admin.logout' : 'logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">ログアウト</button>
                        </form>
                    </li>
                @else
                    <!-- 未ログイン時（ログイン & 会員登録画面用ヘッダー） -->
                @endif
            </ul>
        </nav>
    </div>
</header>
