@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endpush

@extends('layouts.app')

@section('content')
<div class="attendance-list-container">
    <h2>勤怠一覧</h2>

    {{-- 月切り替え --}}
    <div class="month-selector">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}">←前月</a>
        <span>{{ $currentMonthFormatted }}</span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}">翌月→</a>
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
                    <td><a href="{{ route('attendance.detail', ['date' => $attendance['raw_date']]) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
