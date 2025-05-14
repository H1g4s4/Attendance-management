<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RequestLog;
use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceCorrectionRequest;

class UserRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルトは承認待ち
        $user = Auth::user();

        $query = RequestLog::where('user_id', $user->id)->with(['user', 'attendance']);

        if ($status === 'approved') {
            $query->where('is_pending', false);
        } else {
            $query->where('is_pending', true);
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('request.request_list', [
            'requests' => $requests,
            'activeTab' => $status,
        ]);
    }

    public function detail($id)
    {
        $requestLog = RequestLog::with(['user', 'attendance'])->findOrFail($id);

        return view('request.request_detail', compact('requestLog'));
    }

    public function store(StoreAttendanceCorrectionRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();  // バリデーション済みデータ

        $attendance = Attendance::firstOrNew(
            ['user_id' => $user->id, 'date' => $data['attendance_date']],
            ['is_on_break' => false]
        );
        $attendance->is_pending = true;
        $attendance->save();

        $log = RequestLog::create([
            'user_id'         => $user->id,
            'attendance_id'   => $attendance->id,
            'attendance_date' => $data['attendance_date'],
            'start_time'      => $data['start_time'] ? "{$data['attendance_date']} {$data['start_time']}:00" : null,
            'end_time'        => $data['end_time']   ? "{$data['attendance_date']} {$data['end_time']}:00"   : null,
            'note'            => $data['note'],
            'is_pending'      => true,
        ]);

        foreach ($data['breaks'] as $br) {
            if (!empty($br['start'])) {
                $log->breakTimes()->create([
                    'break_start' => "{$data['attendance_date']} {$br['start']}:00",
                    'break_end'   => !empty($br['end'])
                                    ? "{$data['attendance_date']} {$br['end']}:00"
                                    : null,
                ]);
            }
        }

        return redirect()
            ->route('attendance.detail', ['date' => $data['attendance_date']])
            ->with('message', '修正申請を送信しました。管理者の承認をお待ちください。');
    }

}
