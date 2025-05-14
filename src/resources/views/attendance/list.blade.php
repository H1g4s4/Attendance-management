@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endpush

@section('content')
<div class="attendance-list-container">
    <h2>勤怠一覧</h2>

    {{-- 月切り替え --}}
    <div class="month-selector">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}">←前月</a>
        <div class="month-display">
            <!-- カレンダーアイコンと月表示をひとまとめ -->
            <a href="#" id="monthPickerLink">
                <img src="{{ asset('images/calendar.png') }}" alt="Calendar Icon" class="calendar-icon">
            </a>
            <span id="currentMonthText">{{ $currentMonthFormatted }}</span>
            <!-- 隠しの月選択インプット。valueは"YYYY-MM"形式 -->
            <input type="month" id="monthPicker" value="{{ date('Y-m', strtotime(str_replace('/', '-', $currentMonthFormatted))) }}">
        </div>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}">翌月→</a>
    </div>

    <script>
        // カレンダーアイコンクリックで隠しインプットを起動
        document.getElementById('monthPickerLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('monthPicker').click();
        });

        // インプット値変更時に画面リダイレクト
        document.getElementById('monthPicker').addEventListener('change', function(e) {
            var selectedMonth = e.target.value; // "YYYY-MM" 形式の文字列
            // リダイレクト先のルート（例：/attendance/list?month=YYYY-MM）
            window.location.href = "{{ route('attendance.list') }}?month=" + selectedMonth;
        });
    </script>


    {{-- 勤怠テーブル --}}
    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance['date'] }}</td>
                    <td>{{ $attendance['start_time'] ?? '' }}</td>
                    <td>{{ $attendance['end_time'] ?? '' }}</td>
                    <td>{{ $attendance['break_duration'] ?? '' }}</td>
                    <td>{{ $attendance['total_work_time'] ?? '' }}</td>
                    <td><a href="{{ route('attendance.detail', ['date' => $attendance['raw_date']]) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
