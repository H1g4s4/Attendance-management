@extends('layouts.app')

@section('title', '申請一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_request_list.css') }}">
@endpush

@section('content')
<div class="request-list-container">
    <h2>申請一覧</h2>

    <div class="tabs">
        <a href="?status=pending" class="{{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="?status=approved" class="{{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

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
            @forelse ($requests as $request)
                <tr>
                    <td>{{ $request->is_approved ? '承認済み' : '承認待ち' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->target_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                    <td><a href="{{ route('admin.requests.detail', $request->id) }}">詳細</a></td>
                </tr>
            @empty
                <tr><td colspan="6">データがありません。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
