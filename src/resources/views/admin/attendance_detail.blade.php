@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-attendance-detail.css') }}">
@endpush

@section('content')
<div class="admin-detail-container">
    {{-- 見出し（パネル外） --}}
    <h2>勤怠詳細</h2>

    {{-- フォーム開始 --}}
    <form method="POST"
            action="{{ route('admin.attendance.update', ['user_id' => $attendance->user_id, 'date' => $attendance->date->format('Y-m-d')]) }}">
        @csrf
        @method('PUT')

        {{-- 白い背景パネル：テーブルだけを囲む --}}
        <div class="detail-table-container">
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

            <table class="attendance-detail-table">
                <tr>
                    <th class="col-label">名前</th>
                    <td colspan="2" class="col-value">{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th class="col-label">日付</th>
                    <td colspan="2" class="col-value">{{ $attendance->date->format('Y年n月j日') }}</td>
                </tr>
                <tr>
                    <th class="col-label">出勤・退勤</th>
                    <td class="col-value time-group">
                        <input type="time" name="start_time"
                                value="{{ old('start_time', $attendance->start_time ? $attendance->start_time->format('H:i') : '') }}"
                                class="time-input">
                            <span class="tilde">〜</span>
                        <input type="time" name="end_time"
                                value="{{ old('end_time', $attendance->end_time ? $attendance->end_time->format('H:i') : '') }}"
                                class="time-input">
                    </td>
                </tr>

                {{-- 休憩１ --}}
                <tr>
                    <th class="col-label">休憩1</th>
                    <td class="col-value time-group">
                        <input type="time" name="breaks[0][start]"
                                value="{{ old('breaks.0.start', optional($attendance->breaks->get(0))->break_start ? optional($attendance->breaks->get(0))->break_start->format('H:i') : '') }}"
                                class="time-input">
                            <span class="tilde">〜</span>
                        <input type="time" name="breaks[0][end]"
                                value="{{ old('breaks.0.end', optional($attendance->breaks->get(0))->break_end ? optional($attendance->breaks->get(0))->break_end->format('H:i') : '') }}"
                                class="time-input">
                    </td>
                </tr>

                {{-- 休憩２ --}}
                <tr>
                    <th class="col-label">休憩2</th>
                    <td class="col-value time-group">
                        <input type="time" name="breaks[1][start]"
                                value="{{ old('breaks.1.start', optional($attendance->breaks->get(1))->break_start ? optional($attendance->breaks->get(1))->break_start->format('H:i') : '') }}"
                                class="time-input">
                            <span class="tilde">〜</span>
                        <input type="time" name="breaks[1][end]"
                                value="{{ old('breaks.1.end', optional($attendance->breaks->get(1))->break_end ? optional($attendance->breaks->get(1))->break_end->format('H:i') : '') }}"
                                class="time-input">
                    </td>
                </tr>

                <tr>
                    <th class="col-label">備考</th>
                    <td colspan="2" class="col-value">
                        <input type="text"
                            name="note"
                            value="{{ old('note', $attendance->note) }}"
                            class="note-input">
                    </td>
                </tr>
            </table>
        </div>
        {{-- detail-table-container 終了 --}}

        {{-- 「修正」ボタン：パネル外に移動 --}}
        <div class="submit-area">
            <button type="submit" class="update-button">修正</button>
        </div>
    </form>
    {{-- フォーム終了 --}}
</div>
@endsection
