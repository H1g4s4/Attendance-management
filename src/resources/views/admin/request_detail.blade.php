@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/request_detail.css') }}">
@endpush

@section('content')
<div class="request-detail-container">
    <h2>勤怠詳細</h2>

    <div class="request-detail-panel">
        <table class="request-detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $requestLog->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse($requestLog->attendance_date)->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ \Carbon\Carbon::parse($requestLog->start_time)->format('H:i') }} 〜
                    {{ \Carbon\Carbon::parse($requestLog->end_time)->format('H:i') }}
                </td>
            </tr>

            {{-- 休憩を必ず２行出す --}}
            @for ($i = 0; $i < 2; $i++)
            <tr>
                <th>{{ $i === 0 ? '休憩' : '休憩2' }}</th>
                <td>
                    @if (isset($requestLog->breakTimes[$i]) && $requestLog->breakTimes[$i]->break_start)
                        {{ \Carbon\Carbon::parse($requestLog->breakTimes[$i]->break_start)->format('H:i') }}
                        〜
                        {{ \Carbon\Carbon::parse($requestLog->breakTimes[$i]->break_end)->format('H:i') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endfor

            <tr>
                <th>備考</th>
                <td>{{ $requestLog->note }}</td>
            </tr>
        </table>
    </div>

    <div class="submit-area">
        <form action="{{ route('admin.request.approve', $requestLog->id) }}" method="POST">
            @csrf
            <button type="submit" class="approve-button">承認</button>
        </form>
    </div>
</div>
@endsection
