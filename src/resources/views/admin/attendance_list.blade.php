@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-attendance-list.css') }}">
@endpush

@section('content')
<div class="admin-attendance-container">
    <h2>{{ \Carbon\Carbon::parse($targetDate)->format('Y年n月j日') }}の勤怠</h2>

    {{-- 日付ナビゲーション --}}
    <div class="date-navigation">
        <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($targetDate)->subDay()->format('Y-m-d')]) }}">← 前日</a>
        <span>{{ \Carbon\Carbon::parse($targetDate)->format('Y/m/d') }}</span>
        <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($targetDate)->addDay()->format('Y-m-d')]) }}">翌日 →</a>
    </div>

    {{-- 勤怠一覧テーブル --}}
    <table class="admin-attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance['name'] }}</td>
                    <td>{{ $attendance['start_time'] ?? '' }}</td>
                    <td>{{ $attendance['end_time'] ?? '' }}</td>
                    <td>{{ $attendance['break_time'] ?? '' }}</td>
                    <td>{{ $attendance['total_time'] ?? '' }}</td>
                    <td><a href="{{ route('admin.attendance.detail', ['user_id' => $attendance['user_id'], 'date' => $targetDate]) }}">詳細</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">勤怠データがありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
