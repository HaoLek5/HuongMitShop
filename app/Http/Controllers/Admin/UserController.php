<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Danh sách người dùng
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = User::query();

        /*
        | Tìm theo tên / số điện thoại / email
        */
        if ($request->filled('search')) {

            $search = trim(
                $request->input('search')
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'phone',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'email',
                    'like',
                    '%' . $search . '%'
                );

            });
        }


        /*
        | Lọc vai trò
        */
        if ($request->filled('role')) {

            $query->where(
                'role',
                $request->input('role')
            );

        }


        /*
        | Lọc trạng thái
        */
        if (
            $request->filled('status')
            && in_array(
                $request->input('status'),
                ['1', '0'],
                true
            )
        ) {

            $query->where(
                'status',
                (int) $request->input('status')
            );

        }


        /*
        | Mới nhất lên trước
        */
        $users = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'admin.users.index',
            compact('users')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Tạo người dùng
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
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
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'staff',
                    'customer',
                ]),
            ],

        ]);


        User::create([

            'name' => $validated['name'],

            'phone' => $validated['phone'],

            'email' =>
                $validated['email']
                ?? null,

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role' =>
                $validated['role'],

            'status' => true,

        ]);


        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'Tạo tài khoản thành công.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Đổi vai trò
    |--------------------------------------------------------------------------
    */

    public function updateRole(
        Request $request,
        User $user
    ) {
        $validated = $request->validate([

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'staff',
                    'customer',
                ]),
            ],

        ]);


        /*
        | Không cho phép thay đổi vai trò
        | của tài khoản Admin.
        */
        if ($user->role === 'admin') {

            return back()->with(
                'error',
                'Không thể thay đổi vai trò của tài khoản Admin.'
            );
        }


        $user->update([
            'role' => $validated['role'],
        ]);


        return back()->with(
            'success',
            'Đã cập nhật vai trò người dùng.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Khóa / mở khóa
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(User $user)
    {
        /*
        | Không cho phép khóa tài khoản Admin.
        */
        if ($user->role === 'admin') {

            return back()->with(
                'error',
                'Tài khoản Admin không thể bị khóa.'
            );
        }


        $user->update([
            'status' => !((bool) $user->status),
        ]);


        $message = $user->status
            ? 'Đã mở khóa tài khoản.'
            : 'Đã khóa tài khoản.';


        return back()->with(
            'success',
            $message
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Xóa người dùng
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        /*
        | Không cho phép xóa Admin.
        */
        if ($user->role === 'admin') {

            return back()->with(
                'error',
                'Tài khoản Admin không thể bị xóa.'
            );
        }


        /*
        | Không cho phép tự xóa chính mình.
        */
        if ($user->id === Auth::id()) {

            return back()->with(
                'error',
                'Bạn không thể xóa tài khoản đang đăng nhập.'
            );
        }


        $user->delete();


        return back()->with(
            'success',
            'Đã xóa người dùng.'
        );
    }
}