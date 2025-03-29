@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endpush

@extends('layouts.app')

@section('content')
<div class="attendance-detail-container">
    <h2>勤怠詳細</h2>

    <form action="{{ route('attendance.update', ['date' => $attendance->date->format('Y-m-d')]) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="attendance-detail-table">
            <tr>
                <th>名前</th>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $attendance->date->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="start_time" value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
                    〜
                    <input type="time" name="end_time" value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">
                    @error('start_time')<div class="error">{{ $message }}</div>@enderror
                    @error('end_time')<div class="error">{{ $message }}</div>@enderror
                </td>
            </tr>

            {{-- 休憩 --}}
            @foreach ($attendance->breaks as $index => $break)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
                <td>
                    <input type="time" name="breaks[{{ $index }}][start]" value="{{ optional($break->break_start)->format('H:i') }}">
                    〜
                    <input type="time" name="breaks[{{ $index }}][end]" value="{{ optional($break->break_end)->format('H:i') }}">
                </td>
            </tr>
            @endforeach

            {{-- 追加休憩欄 --}}
            <tr>
                <th>休憩{{ count($attendance->breaks) + 1 }}</th>
                <td>
                    <input type="time" name="breaks[{{ count($attendance->breaks) }}][start]">
                    〜
                    <input type="time" name="breaks[{{ count($attendance->breaks) }}][end]">
                </td>
            </tr>

            <tr>
                <th>備考</th>
                <td>
                    <input type="text" name="note" value="{{ old('note', $attendance->note) }}">
                    @error('note')<div class="error">{{ $message }}</div>@enderror
                </td>
            </tr>
        </table>

        {{-- 承認待ちの場合は修正不可 --}}
        @if ($attendance->is_pending)
            <p class="note-error">*承認待ちのため修正はできません。</p>
        @else
            <div class="submit-area">
                <button type="submit" class="btn black">修正</button>
            </div>
        @endif
    </form>
</div>
@endsection
