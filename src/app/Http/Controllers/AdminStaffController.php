<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStaffController extends Controller
{
    public function index()
    {
        $users = User::all(); // 全ユーザー取得
        return view('admin.staff_list', compact('users'));
    }

    public function attendance(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $targetMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : Carbon::now();

        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        $attendanceData = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$start, $end])
            ->with('breaks')
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $daysInMonth = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $att = $attendanceData->get($date->format('Y-m-d'));

            $startTime = $att?->start_time ? Carbon::parse($att->start_time)->format('H:i') : null;
            $endTime = $att?->end_time ? Carbon::parse($att->end_time)->format('H:i') : null;

            $breakTotal = $att?->breaks->reduce(function ($carry, $break) {
                $start = Carbon::parse($break->break_start);
                $end = $break->break_end ? Carbon::parse($break->break_end) : Carbon::now();
                return $carry + $start->diffInMinutes($end);
            }, 0);

            $workTime = null;
            if ($att && $att->start_time && $att->end_time) {
                $workTimeMin = Carbon::parse($att->start_time)->diffInMinutes(Carbon::parse($att->end_time)) - $breakTotal;
                $workTime = floor($workTimeMin / 60) . ':' . str_pad($workTimeMin % 60, 2, '0', STR_PAD_LEFT);
            }

            $daysInMonth[] = [
                'raw_date' => $date->format('Y-m-d'),
                'date' => $date->format('m/d(D)'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_duration' => $breakTotal ? floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT) : '',
                'total_work_time' => $workTime,
            ];
        }

        return view('admin.staff_attendance_list', [
            'user' => $user,
            'attendances' => $daysInMonth,
            'currentMonthFormatted' => $targetMonth->format('Y/m'),
            'previousMonth' => $targetMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $targetMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function exportCsv(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $targetMonth = $request->input('month') 
            ? Carbon::parse($request->input('month')) 
            : Carbon::now();

        $start = $targetMonth->copy()->startOfMonth();
        $end = $targetMonth->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$start, $end])
            ->with('breaks')
            ->get();

        $response = new StreamedResponse(function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['日付', '出勤', '退勤', '休憩合計', '合計']);

            foreach ($attendances as $att) {
                $breakTotal = $att->breaks->reduce(function ($carry, $break) {
                    $start = Carbon::parse($break->break_start);
                    $end = $break->break_end ? Carbon::parse($break->break_end) : Carbon::now();
                    return $carry + $start->diffInMinutes($end);
                }, 0);

                $workTime = '';
                if ($att->start_time && $att->end_time) {
                    $workTimeMin = Carbon::parse($att->start_time)->diffInMinutes(Carbon::parse($att->end_time)) - $breakTotal;
                    $workTime = floor($workTimeMin / 60) . ':' . str_pad($workTimeMin % 60, 2, '0', STR_PAD_LEFT);
                }

                fputcsv($handle, [
                    $att->date->format('Y-m-d'),
                    optional($att->start_time)->format('H:i'),
                    optional($att->end_time)->format('H:i'),
                    $breakTotal ? floor($breakTotal / 60) . ':' . str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT) : '',
                    $workTime
                ]);
            }

            fclose($handle);
        });

        $filename = $user->name . '_勤怠_' . $targetMonth->format('Y_m') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
