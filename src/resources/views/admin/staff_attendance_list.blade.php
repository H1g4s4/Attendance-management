@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-staff-attendance-list.css') }}">
@endpush

@section('content')
<div class="staff-attendance-container">
    <h2>{{ $user->name }}さんの勤怠</h2>

    {{-- 月切り替え --}}
    <div class="month-selector">
        <a href="{{ route('admin.staff.attendance', ['user_id' => $user->id, 'month' => $previousMonth]) }}">← 前月</a>
        <span>{{ $currentMonthFormatted }}</span>
        <a href="{{ route('admin.staff.attendance', ['user_id' => $user->id, 'month' => $nextMonth]) }}">翌月 →</a>
    </div>

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
                    <td>
                        <a href="{{ route('admin.attendance.detail', ['user_id' => $user->id, 'date' => $attendance['raw_date']]) }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form method="GET" action="{{ route('admin.staff.attendance.csv', ['user_id' => $user->id]) }}">
        <input type="hidden" name="month" value="{{ request('month', \Carbon\Carbon::now()->format('Y-m')) }}">
        <button type="submit" class="csv-button">CSV出力</button>
    </form>
</div>
@endsection
