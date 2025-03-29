<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserRequestController;

// ---------- 認証関連 ----------

// 一般ユーザーのログイン画面
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 管理者のログイン画面
Route::get('/admin/login', function () {
    return view('auth.admin_login');
})->name('admin.login');

// Fortifyのログイン処理
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);

// ログアウト処理
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

// 会員登録
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// ---------- 一般ユーザー（authミドルウェア） ----------

Route::middleware(['auth'])->group(function () {
    // 出勤登録（勤怠登録）画面
    Route::get('/attendance', [UserAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [UserAttendanceController::class, 'startWork'])->name('attendance.startWork');
    Route::post('/attendance/end', [UserAttendanceController::class, 'endWork'])->name('attendance.endWork');
    Route::post('/attendance/break/start', [UserAttendanceController::class, 'startBreak'])->name('attendance.startBreak');
    Route::post('/attendance/break/end', [UserAttendanceController::class, 'endBreak'])->name('attendance.endBreak');

    // 勤怠一覧画面・詳細画面
    Route::get('/attendance/list', [UserAttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{date}', [UserAttendanceController::class, 'detail'])->name('attendance.detail');

    // 申請一覧画面（承認待ち・承認済み）
    Route::get('/stamp_correction_request/list', [UserRequestController::class, 'index'])->name('user.requests');
    Route::get('/stamp_correction_request/{id}/detail', [UserRequestController::class, 'detail'])->name('user.requests.detail');
});

// ---------- 管理者 ----------

Route::middleware(['auth:admin'])->group(function () {
    // 勤怠一覧画面（管理者）
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    // 勤怠詳細画面（管理者）
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'detail'])->name('admin.attendance.detail');
    Route::put('/admin/attendance/update/{user_id}/{date}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
    // スタッフ一覧画面（管理者）
    Route::get('/admin/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    // スタッフ別勤怠一覧画面（管理者）
    Route::get('/admin/attendance/staff/{id}', [AdminStaffController::class, 'attendance'])->name('admin.staff.attendance');
    // CSV出力（スタッフ別月次）
    Route::get('/admin/attendance/staff/{id}/csv', [AdminStaffController::class, 'exportCsv'])->name('admin.staff.attendance.csv');
    // 申請一覧画面（管理者）
    Route::get('/stamp_correction_request/list', [AdminRequestController::class, 'index'])->name('admin.requests.index');
    // 修正申請承認画面（管理者）
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminRequestController::class, 'detail'])->name('admin.request.detail');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [AdminRequestController::class, 'approve'])->name('admin.request.approve');
});

// ---------- トップページ ----------
Route::get('/', function () {
    return view('welcome');
})->name('home');
