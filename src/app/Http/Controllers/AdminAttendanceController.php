<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 表示対象の日付（指定がなければ今日）
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($date);

        // 勤怠データ（ユーザーと休憩も一緒に取得）
        $attendances = Attendance::whereDate('date', $targetDate)
            ->with(['user', 'breaks'])
            ->get()
            ->map(function ($attendance) {
                $start = optional($attendance->start_time)->format('H:i');
                $end = optional($attendance->end_time)->format('H:i');

                $breakMin = $attendance->breaks->reduce(function ($total, $break) {
                    if ($break->break_start && $break->break_end) {
                        return $total + Carbon::parse($break->break_start)->diffInMinutes(Carbon::parse($break->break_end));
                    }
                    return $total;
                }, 0);

                $workMin = ($attendance->start_time && $attendance->end_time)
                    ? Carbon::parse($attendance->start_time)->diffInMinutes(Carbon::parse($attendance->end_time)) - $breakMin
                    : null;

                return [
                    'user_id' => $attendance->user_id,
                    'name' => $attendance->user->name,
                    'start_time' => $start,
                    'end_time' => $end,
                    'break_time' => $breakMin ? floor($breakMin / 60) . ':' . str_pad($breakMin % 60, 2, '0', STR_PAD_LEFT) : '',
                    'total_time' => $workMin ? floor($workMin / 60) . ':' . str_pad($workMin % 60, 2, '0', STR_PAD_LEFT) : '',
                ];
            });

        return view('admin.attendance_list', [
            'attendances' => $attendances,
            'targetDate' => $targetDate->format('Y-m-d'),
        ]);
    }

    public function detail($user_id, $date)
    {
        $attendance = Attendance::with(['user', 'breaks'])
            ->where('user_id', $user_id)
            ->whereDate('date', $date)
            ->firstOrFail();

        return view('admin.attendance_detail', compact('attendance'));
    }

    public function update(AdminAttendanceUpdateRequest $request, $user_id, $date)
    {
        $attendance = Attendance::where('user_id', $user_id)
            ->whereDate('date', $date)
            ->firstOrFail();

        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'note' => $request->note,
        ]);

        $attendance->breaks()->delete();
        foreach ($request->breaks ?? [] as $break) {
            if (!empty($break['start']) && !empty($break['end'])) {
                $attendance->breaks()->create([
                    'break_start' => $break['start'],
                    'break_end' => $break['end'],
                ]);
            }
        }

        return redirect()->route('admin.attendance.detail', [$user_id, $date])
            ->with('message', '勤怠情報を更新しました。');
    }
}
