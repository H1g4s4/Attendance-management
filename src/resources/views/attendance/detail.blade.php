@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endpush

@section('content')
<div class="attendance-detail-container">
    <!-- 見出し：左に黒い縦線 -->
    <h2>勤怠詳細</h2>

    <!-- フォーム全体 -->
    <form action="{{ route('user.requests.store') }}" method="POST">
        @csrf

        <!-- どの日の申請か伝える隠しフィールド -->
        <input type="hidden" name="attendance_date" value="{{ $attendance->date->format('Y-m-d') }}">

        <!-- 白枠で囲む部分をラップするコンテナ -->
        <div class="detail-table-container">
            <table class="attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>
                        <span class="year">{{ $attendance->date->format('Y年') }}</span>
                        <span class="month-day">{{ $attendance->date->format('n月j日') }}</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-group">
                        <input
                            type="time"
                            name="start_time"
                            class="time-input"
                            value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}"
                        >
                        <span class="tilde">〜</span>
                        <input
                            type="time"
                            name="end_time"
                            class="time-input"
                            value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}"
                        >
                        @error('start_time')<div class="error">{{ $message }}</div>@enderror
                        @error('end_time')<div class="error">{{ $message }}</div>@enderror
                    </td>
                </tr>

                {{-- 休憩1 --}}
                <tr>
                    <th>休憩1</th>
                    <td class="time-group">
                        <input
                            type="time"
                            name="breaks[0][start]"
                            class="time-input"
                            value="{{ old('breaks.0.start', optional($attendance->breaks->get(0))->break_start
                                ? optional($attendance->breaks->get(0))->break_start->format('H:i')
                                : '') }}"
                        >
                        <span class="tilde">〜</span>
                        <input
                            type="time"
                            name="breaks[0][end]"
                            class="time-input"
                            value="{{ old('breaks.0.end', optional($attendance->breaks->get(0))->break_end
                                ? optional($attendance->breaks->get(0))->break_end->format('H:i')
                                : '') }}"
                        >
                    </td>
                </tr>

                {{-- 休憩2 --}}
                <tr>
                    <th>休憩2</th>
                    <td class="time-group">
                        <input
                            type="time"
                            name="breaks[1][start]"
                            class="time-input"
                            value="{{ old('breaks.1.start', optional($attendance->breaks->get(1))->break_start
                                ? optional($attendance->breaks->get(1))->break_start->format('H:i')
                                : '') }}"
                        >
                        <span class="tilde">〜</span>
                        <input
                            type="time"
                            name="breaks[1][end]"
                            class="time-input"
                            value="{{ old('breaks.1.end', optional($attendance->breaks->get(1))->break_end
                                ? optional($attendance->breaks->get(1))->break_end->format('H:i')
                                : '') }}"
                        >
                    </td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td>
                        <input
                            type="text"
                            name="note"
                            class="note-input"
                            value="{{ old('note', $attendance->note) }}"
                        >
                        {{-- FormRequest のバリデーションエラーをここで出す --}}
                        @error('note')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </table>
        </div>
        {{-- 承認待ち中はボタンを無効化 --}}
        @if($attendance->is_pending)
            <p class="note-error">*承認待ちのため再申請できません。</p>
        @else
            <div class="submit-area">
                <button type="submit" class="btn black">申請</button>
            </div>
        @endif
    </form>
</div>
@endsection
