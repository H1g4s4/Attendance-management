@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/request-list.css') }}">
@endpush

@section('content')
<div class="request-container">
    <h2>申請一覧</h2>

    {{-- タブ --}}
    <div class="tabs">
        <a href="{{ route('user.requests', ['status' => 'pending']) }}" class="{{ $activeTab === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('user.requests', ['status' => 'approved']) }}" class="{{ $activeTab === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    {{-- 一覧テーブル --}}
    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日付</th>
                <th>申請理由</th>
                <th>申請日</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $request)
                <tr>
                    <td>{{ $request->is_pending ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td><a href="{{ route('user.requests.detail', ['id' => $request->id]) }}">詳細</a></td>
                </tr>
            @empty
                <tr><td colspan="6">申請がありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
