@extends('layouts.app')

@section('content')
<div class="attendance-container">

    {{-- ステータス表示 --}}
    <div class="status-label">
        <span class="status-tag">{{ $statusLabel }}</span>
    </div>

    {{-- 日付表示 --}}
    <div class="date">{{ $todayFormatted }}</div>

    {{-- 時刻表示 --}}
    <div class="time">{{ $currentTime }}</div>

    {{-- ボタン表示（ステータスによって分岐） --}}
    <div class="button-group">
        @if ($status === 'off')
            {{-- 出勤前 --}}
            <form action="{{ route('attendance.startWork') }}" method="POST">
                @csrf
                <button class="btn black" type="submit">出勤</button>
            </form>

        @elseif ($status === 'working')
            {{-- 出勤中 --}}
            <form action="{{ route('attendance.endWork') }}" method="POST" style="display:inline;">
                @csrf
                <button class="btn black" type="submit">退勤</button>
            </form>
            <form action="{{ route('attendance.startBreak') }}" method="POST" style="display:inline;">
                @csrf
                <button class="btn white" type="submit">休憩入</button>
            </form>

        @elseif ($status === 'on_break')
            {{-- 休憩中 --}}
            <form action="{{ route('attendance.endBreak') }}" method="POST">
                @csrf
                <button class="btn black" type="submit">休憩戻</button>
            </form>

        @elseif ($status === 'done')
            {{-- 退勤済 --}}
            <p class="message">お疲れ様でした。</p>
        @endif
    </div>

</div>
@endsection
