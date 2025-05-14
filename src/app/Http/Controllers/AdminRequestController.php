<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestLog;
use Illuminate\Support\Facades\DB;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = RequestLog::with(['user','attendance'])
            ->when($status === 'pending', fn($q) => $q->where('is_pending', true))
            ->when($status === 'approved', fn($q) => $q->where('is_pending', false))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.admin_request_list', [
            'requests' => $requests,
            'activeTab' => $status,
        ]);
    }

    public function detail($id)
    {
        $requestLog = RequestLog::with(['user', 'attendance', 'breakTimes'])->findOrFail($id);
        return view('admin.request_detail', compact('requestLog'));
    }

    public function approve($id)
    {
        DB::transaction(function() use ($id) {
            // 1) 申請ログ取得
            $requestLog = RequestLog::with(['attendance', 'breakTimes'])->findOrFail($id);

            // 2) 勤怠本体を更新
            $attendance = $requestLog->attendance;
            $attendance->update([
                'start_time'  => $requestLog->start_time,
                'end_time'    => $requestLog->end_time,
                'note'        => $requestLog->note,
                'is_pending'  => false,  // 申請フラグを下ろす
            ]);

            // 3) 休憩データを差し替え
            $attendance->breaks()->delete();
            foreach ($requestLog->breakTimes as $break) {
                $attendance->breaks()->create([
                    'break_start' => $break->break_start,
                    'break_end'   => $break->break_end,
                ]);
            }

            // 4) 申請ログ側ステータス更新
            // もし is_pending カラムならこちら
            $requestLog->update([
                'is_pending'  => false,
                'approved_at' => now(),
            ]);
            // → もし status カラムを使うなら 'status' => 'approved' に
        });

        return redirect()
            ->route('admin.requests.index', ['status' => 'pending'])
            ->with('message', '申請を承認しました。');
    }
}