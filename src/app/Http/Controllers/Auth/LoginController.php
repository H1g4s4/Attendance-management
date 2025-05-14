<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('user.attendance');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();             // ログアウト
        $request->session()->invalidate();         // セッション破棄
        $request->session()->regenerateToken();    // CSRF トークン再生成

        // ログイン画面へリダイレクト
        return redirect()->route('login');
    }
}
