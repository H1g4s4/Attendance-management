<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RequestLog;

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
}
