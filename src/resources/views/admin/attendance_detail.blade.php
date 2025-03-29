@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-attendance-detail.css') }}">
@endpush

@section('content')
<div class="admin-detail-container">
    <h2>勤怠詳細</h2>

    @if(session('message'))
        <p class="success-message">{{ session('message') }}</p>
    @endif

    @if($errors->any())
        <div class="error-messages">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attendance.update', ['user_id' => $attendance->user_id, 'date' => $attendance->date->format('Y-m-d')]) }}">
        @csrf
        @method('PUT')

        <table class="attendance-detail-table">
            <tr>
                <th>名前</th>
                <td colspan="2">{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td colspan="2">{{ $attendance->date->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td><input type="time" name="start_time" value="{{ old('start_time', $attendance->start_time ? $attendance->start_time->format('H:i') : '') }}"></td>
                <td><input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time ? $attendance->end_time->format('H:i') : '') }}"></td>
            </tr>
            @foreach ($attendance->breaks as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td>
                        <input type="time" name="breaks[{{ $i }}][start]"
                            value='{{ old("breaks.$i.start", optional($break->break_start)->format("H:i")) }}'>
                    </td>
                    <td>
                        <input type="time" name="breaks[{{ $i }}][end]"
                            value='{{ old("breaks.$i.end", optional($break->break_end)->format("H:i")) }}'>
                    </td>
                </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td colspan="2"><input type="text" name="note" value="{{ old('note', $attendance->note) }}"></td>
            </tr>
        </table>

        <button type="submit" class="update-button">修正</button>
    </form>
</div>
@endsection
