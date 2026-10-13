<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập bằng số điện thoại + password.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => [
                'required',
                'string',
                'max:20',
            ],
            'password' => [
                'required',
                'string',
            ],
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (
            Auth::attempt([
                'phone' => $credentials['phone'],
                'password' => $credentials['password'],
                'status' => true,
            ])
        ) {
            // Chống session fixation sau khi đăng nhập.
            $request->session()->regenerate();

            // Cập nhật thời gian đăng nhập gần nhất.
            $request->user()->update([
                'last_login_at' => now(),
            ]);

            if (in_array($request->user()->role, ['admin', 'staff'], true)) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([
                'phone' => 'Số điện thoại hoặc mật khẩu không chính xác.',
            ])
            ->withInput($request->only('phone'));
    }

    /**
     * Đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
 * Hiển thị form đăng ký.
 */
public function showRegister()
{
    return view('auth.register');
}

/**
 * Xử lý đăng ký Customer.
 */
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:100',
        ],

        'phone' => [
            'required',
            'string',
            'max:20',
            'unique:users,phone',
        ],

        'email' => [
            'nullable',
            'email',
            'max:255',
            'unique:users,email',
        ],

        'password' => [
            'required',
            'confirmed',
            \Illuminate\Validation\Rules\Password::min(8),
        ],

        'pin_code' => [
            'nullable',
            'digits:6',
            'confirmed',
        ],
    ], [
        'name.required' => 'Vui lòng nhập họ tên.',

        'phone.required' => 'Vui lòng nhập số điện thoại.',
        'phone.unique' => 'Số điện thoại đã được đăng ký.',

        'email.email' => 'Email không hợp lệ.',
        'email.unique' => 'Email đã được sử dụng.',

        'password.confirmed' => 'Mật khẩu xác nhận không khớp.',

        'pin_code.digits' => 'PIN code phải gồm đúng 6 chữ số.',
        'pin_code.confirmed' => 'PIN code xác nhận không khớp.',
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'] ?? null,
        'password' => $validated['password'],
        'pin_code' => $validated['pin_code'] ?? null,
        'role' => 'customer',
        'status' => true,
    ]);

    \Illuminate\Support\Facades\Auth::login($user);

    $request->session()->regenerate();

    return redirect()
        ->route('dashboard')
        ->with('success', 'Đăng ký tài khoản thành công.');
}
}