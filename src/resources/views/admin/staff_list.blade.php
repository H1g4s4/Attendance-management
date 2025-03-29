@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-staff-list.css') }}">
@endpush

@section('content')
<div class="staff-list-container">
    <h2>スタッフ一覧</h2>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><a href="{{ route('admin.staff.attendance', ['user_id' => $user->id]) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
