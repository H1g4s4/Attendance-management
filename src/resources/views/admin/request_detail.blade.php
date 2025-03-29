@extends('layouts.app')

@section('content')
<div class="request-detail-container">
    <h2>勤怠詳細</h2>

    <table class="request-detail-table">
        <tr>
            <th>名前</th>
            <td>{{ $requestLog->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>{{ \Carbon\Carbon::parse($requestLog->date)->format('Y年n月j日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>{{ \Carbon\Carbon::parse($requestLog->start_time)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($requestLog->end_time)->format('H:i') }}</td>
        </tr>

        @foreach($requestLog->breakTimes as $i => $break)
        <tr>
            <th>休憩{{ $i + 1 }}</th>
            <td>{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}</td>
        </tr>
        @endforeach

        <tr>
            <th>備考</th>
            <td>{{ $requestLog->note }}</td>
        </tr>
    </table>

    <form action="{{ route('admin.request.approve', $requestLog->id) }}" method="POST">
        @csrf
        <button type="submit" class="approve-button">承認</button>
    </form>
</div>
@endsection
