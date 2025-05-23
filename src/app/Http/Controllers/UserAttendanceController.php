<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Models\BreakTime; // ← 休憩用モデル（あとで作る）

class UserAttendanceController extends Controller
{
    // 勤怠登録画面表示
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 今日の勤怠データを取得
        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();

        // ステータス判定
        if (!$attendance) {
            $status = 'off';
            $statusLabel = '勤務外';
        } elseif ($attendance->end_time) {
            $status = 'done';
            $statusLabel = '退勤済';
        } elseif ($attendance->is_on_break) {
            $status = 'on_break';
            $statusLabel = '休憩中';
        } else {
            $status = 'working';
            $statusLabel = '出勤中';
        }

        return view('attendance.index', [
            'status' => $status,
            'statusLabel' => $statusLabel,
            'todayFormatted' => now()->isoFormat('YYYY年M月D日(ddd)'),
            'currentTime' => now()->format('H:i'),
        ]);
    }

    // 出勤処理
    public function startWork()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $exists = Attendance::where('user_id', $user->id)->whereDate('date', $today)->exists();
        if ($exists) {
            return redirect()->route('user.attendance');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'start_time' => now(),
            'is_on_break' => false,
        ]);

        return redirect()->route('user.attendance');
    }

    // 退勤処理
    public function endWork()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->first();

        if ($attendance && !$attendance->end_time) {
            $attendance->update([
                'end_time' => now(),
            ]);
        }

        return redirect()->route('user.attendance');
    }

    // 休憩開始処理
    public function startBreak()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->first();

        if ($attendance && !$attendance->is_on_break) {
            $attendance->update(['is_on_break' => true]);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now(),
            ]);
        }

        return redirect()->route('user.attendance');
    }

    // 休憩終了処理
    public function endBreak()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->first();

        if ($attendance && $attendance->is_on_break) {
            $attendance->update(['is_on_break' => false]);

            $break = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if ($break) {
                $break->update(['break_end' => now()]);
            }
        }

        return redirect()->route('user.attendance');
    }

    public function list(Request $request)
    {
        Carbon::setLocale('ja');

        $user = Auth::user();

        $targetMonth = $request->input('month') 
            ? Carbon::parse($request->input('month')) 
            : Carbon::now();

        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();

        // 出勤データ取得
        $attendanceData = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('breaks')
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        // その月の全日リスト
        $daysInMonth = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            // 各日の勤怠データを取得
            $att = $attendanceData->get($date->format('Y-m-d'));

            // 出勤・退勤時刻のフォーマット（存在すれば）
            $start = isset($att->start_time) ? Carbon::parse($att->start_time)->format('H:i') : null;
            $end = isset($att->end_time) ? Carbon::parse($att->end_time)->format('H:i') : null;

            // 曜日の取得（日本語表記）
            $weekday = $date->isoFormat('ddd');  // localeがjaなら "日", "月", etc.

            // 休憩時間の合計を計算（$breakTotal を先に計算）
            if ($att && isset($att->breaks)) {
                $breakTotal = $att->breaks->reduce(function ($carry, $break) {
                    $startBreak = Carbon::parse($break->break_start);
                    $endBreak   = $break->break_end ? Carbon::parse($break->break_end) : Carbon::now();
                    return $carry + $startBreak->diffInMinutes($endBreak);
                }, 0);
            } else {
                $breakTotal = 0;
            }

            // 勤務時間の計算
            $workTime = null;
            if ($att && $att->start_time && $att->end_time) {
                $workTimeMin = Carbon::parse($att->start_time)->diffInMinutes(Carbon::parse($att->end_time)) - $breakTotal;
                $workTime = floor($workTimeMin / 60) . ':' . str_pad($workTimeMin % 60, 2, '0', STR_PAD_LEFT);
            }

            // 日付と勤怠情報を配列に追加
            $daysInMonth[] = [
                'raw_date'         => $date->format('Y-m-d'),
                'date'             => $date->format('n/j') . '(' . $weekday . ')', // 例: 4/6(日)
                'start_time'       => $start,
                'end_time'         => $end,
                'break_duration'   => $breakTotal ? floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT) : '',
                'total_work_time'  => $workTime,
            ];
        }

        return view('attendance.list', [
            'attendances' => $daysInMonth,
            'currentMonthFormatted' => $targetMonth->format('Y/m'),
            'previousMonth' => $targetMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $targetMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function detail($date)
    {
        $user = Auth::user();
        // 該当日のレコードを取得（なければ null）
        $attendance = Attendance::with('breaks')->where('user_id', $user->id)->whereDate('date',$date)->first();

        // レコードがなければ、画面表示用のダミーオブジェクトを用意
        if (! $attendance) {
        $attendance = new Attendance([
            'user_id'    => $user->id,
            'date'       => $date,
            'start_time' => null,
            'end_time'   => null,
            'note'       => null,
        ]);
        // ブレイク情報も空コレクションで
        $attendance->setRelation('breaks', collect());
    }

        return view('attendance.detail', compact('attendance'));
    }

    public function update(AttendanceUpdateRequest $request, $date)
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $date)->with('breaks')->firstOrFail();

        if ($attendance->is_pending) {
            return redirect()->route('attendance.detail', ['date' => $date])->with('error', '承認待ちのため修正できません。');
        }

        $attendance->update([
            'start_time' => Carbon::parse($request->start_time),
            'end_time' => Carbon::parse($request->end_time),
            'note' => $request->note,
            'is_pending' => true, // 修正申請フラグを立てる
        ]);

        // 古い休憩削除・再登録（ここは後で工夫可能）
        $attendance->breaks()->delete();
        foreach ($request->breaks ?? [] as $break) {
            if (!empty($break['start']) && !empty($break['end'])) {
                $attendance->breaks()->create([
                    'break_start' => Carbon::parse($break['start']),
                    'break_end' => Carbon::parse($break['end']),
                ]);
            }
        }

        return redirect()->route('attendance.detail', ['date' => $date])->with('message', '修正申請を送信しました。');
    }


}
