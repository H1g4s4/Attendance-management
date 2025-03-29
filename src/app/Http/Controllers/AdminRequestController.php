<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestLog;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = RequestLog::with('user')
            ->when($status === 'pending', fn($q) => $q->where('is_approved', false))
            ->when($status === 'approved', fn($q) => $q->where('is_approved', true))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.request_list', [
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    public function detail($id)
    {
        $requestLog = RequestLog::with(['user', 'attendance', 'breakTimes'])->findOrFail($id);
        return view('admin.request_detail', compact('requestLog'));
    }

    public function approve($id)
    {
        $requestLog = RequestLog::with('attendance')->findOrFail($id);

        // 勤怠データ更新
        $attendance = $requestLog->attendance;
        $attendance->update([
            'start_time' => $requestLog->start_time,
            'end_time' => $requestLog->end_time,
            'note' => $requestLog->note,
            'is_pending' => false, // 承認完了としてフラグを下げる
        ]);

        // 既存の休憩削除 → 申請されたものに置き換え
        $attendance->breaks()->delete();
        foreach ($requestLog->breakTimes as $break) {
            $attendance->breaks()->create([
                'break_start' => $break->break_start,
                'break_end' => $break->break_end,
            ]);
        }

        // 修正申請自体のステータス更新
        $requestLog->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('message', '申請を承認しました。');
    }
}