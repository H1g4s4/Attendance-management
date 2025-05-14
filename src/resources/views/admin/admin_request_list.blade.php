@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_request_list.css') }}">
@endpush

@section('content')
<div class="request-container">
    {{-- 見出し --}}
    <h2>申請一覧</h2>

    {{-- タブ --}}
    <div class="tabs">
        <a href="{{ route('admin.requests.index', ['status' => 'pending']) }}"
            class="{{ $activeTab ?? '' === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'approved']) }}"
            class="{{ $activeTab ?? '' === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    {{-- テーブル部分だけ白いパネルで囲む --}}
    <div class="request-table-panel">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日付</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->is_pending ? '承認待ち' : '承認済み' }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('admin.request.detail', ['attendance_correct_request' => $request->id]) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">申請がありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
