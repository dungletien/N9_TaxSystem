<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cccd' => 'required|string|size:12',
            'password' => 'required|string',
            'user_type' => 'required|in:nhan-vien,truong-phong,ke-toan',
        ]);

        $user = User::where('cccd', $request->cccd)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['cccd' => 'Sai CCCD hoặc mật khẩu!']);
        }

        // Kiểm tra quyền truy cập với vai trò đã chọn
        $hasRole = UserRole::where('user_id', $user->id)
                          ->where('user_type', $request->user_type)
                          ->exists();

        if (!$hasRole) {
            return back()->withErrors(['user_type' => 'Bạn không có quyền truy cập với vai trò này!']);
        }

        // Lưu thông tin vào session
        Session::put('user', [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'user_type' => $request->user_type,
            'department' => $user->department,
        ]);

        // Điều hướng theo vai trò
        switch ($request->user_type) {
            case 'ke-toan':
                return redirect()->route('accountant.dashboard');
            case 'truong-phong':
                return redirect()->route('manager.dashboard');
            case 'nhan-vien':
                return redirect()->route('employee.dashboard');
            default:
                return back()->withErrors(['user_type' => 'Vai trò không hợp lệ!']);
        }
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
