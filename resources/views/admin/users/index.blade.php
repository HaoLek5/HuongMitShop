@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('page-title', 'Người dùng')

@section('content')

    <div class="space-y-6">


        {{-- ===================================================== --}}
        {{-- HEADER                                                  --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h1 class="text-2xl font-semibold tracking-tight text-[#0F172A]">
                    Quản lý người dùng
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Quản lý tài khoản Admin, Staff và Customer.
                </p>

            </div>


            <button
                type="button"
                onclick="openCreateUserModal()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
            >

                <span class="text-lg leading-none">
                    +
                </span>

                Thêm người dùng

            </button>

        </div>



        {{-- ===================================================== --}}
        {{-- SUCCESS                                                 --}}
        {{-- ===================================================== --}}

        @if(session('success'))

            <div
                class="rounded-xl border border-green-200 bg-[#F0FDF4] px-4 py-3 text-sm text-green-700"
            >
                {{ session('success') }}
            </div>

        @endif



        {{-- ===================================================== --}}
        {{-- ERROR                                                   --}}
        {{-- ===================================================== --}}

        @if(session('error'))

            <div
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ session('error') }}
            </div>

        @endif



        {{-- ===================================================== --}}
        {{-- VALIDATION                                              --}}
        {{-- ===================================================== --}}

        @if($errors->any())

            <div
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >

                {{ $errors->first() }}

            </div>

        @endif



        {{-- ===================================================== --}}
        {{-- FILTER                                                  --}}
        {{-- ===================================================== --}}

        <div
            class="rounded-xl border border-[#E2E8F0] bg-white p-4 shadow-sm"
        >

            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1 gap-3 lg:grid-cols-4"
            >


                {{-- SEARCH --}}
                <div class="lg:col-span-2">

                    <label
                        class="mb-1.5 block text-xs font-medium text-slate-500"
                    >
                        Tìm kiếm
                    </label>


                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tên, số điện thoại hoặc email..."
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] bg-white px-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                    >

                </div>



                {{-- ROLE --}}
                <div>

                    <label
                        class="mb-1.5 block text-xs font-medium text-slate-500"
                    >
                        Vai trò
                    </label>


                    <select
                        name="role"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] bg-white px-3 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                    >

                        <option value="">
                            Tất cả vai trò
                        </option>

                        <option
                            value="admin"
                            @selected(request('role') === 'admin')
                        >
                            Admin
                        </option>

                        <option
                            value="staff"
                            @selected(request('role') === 'staff')
                        >
                            Staff
                        </option>

                        <option
                            value="customer"
                            @selected(request('role') === 'customer')
                        >
                            Customer
                        </option>

                    </select>

                </div>



                {{-- STATUS --}}
                <div>

                    <label
                        class="mb-1.5 block text-xs font-medium text-slate-500"
                    >
                        Trạng thái
                    </label>


                    <select
                        name="status"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] bg-white px-3 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                    >

                        <option value="">
                            Tất cả trạng thái
                        </option>

                        <option
                            value="1"
                            @selected(request('status') === '1')
                        >
                            Hoạt động
                        </option>

                        <option
                            value="0"
                            @selected(request('status') === '0')
                        >
                            Đã khóa
                        </option>

                    </select>

                </div>



                {{-- BUTTONS --}}
                <div class="flex items-end gap-2 lg:col-span-4">

                    <button
                        type="submit"
                        class="h-10 rounded-lg bg-[#22C55E] px-5 text-sm font-medium text-white hover:bg-[#16A34A]"
                    >
                        Tìm kiếm
                    </button>


                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex h-10 items-center rounded-lg border border-[#CBD5E1] bg-white px-5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Xóa lọc
                    </a>

                </div>

            </form>

        </div>



        {{-- ===================================================== --}}
        {{-- USER TABLE                                              --}}
        {{-- ===================================================== --}}

        <div
            class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm"
        >


            {{-- TABLE HEADER --}}
            <div
                class="flex items-center justify-between border-b border-[#E2E8F0] px-5 py-4"
            >

                <div>

                    <h2 class="text-sm font-semibold text-slate-700">
                        Danh sách người dùng
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">

                        Tổng:

                        <span class="font-medium text-slate-600">
                            {{ number_format($users->total()) }}
                        </span>

                        tài khoản

                    </p>

                </div>

            </div>



            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-[#F8FAFC]">

                        <tr>

                            <th
                                class="px-5 py-3 text-left text-xs font-semibold text-slate-500"
                            >
                                Người dùng
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs font-semibold text-slate-500"
                            >
                                Liên hệ
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-slate-500"
                            >
                                Vai trò
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-slate-500"
                            >
                                Trạng thái
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs font-semibold text-slate-500"
                            >
                                Đăng nhập cuối
                            </th>

                            <th
                                class="px-5 py-3 text-right text-xs font-semibold text-slate-500"
                            >
                                Thao tác
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-[#F1F5F9]">


                        @forelse($users as $user)


                            <tr class="transition hover:bg-[#F8FAFC]">


                                {{-- ================================= --}}
                                {{-- USER                                 --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">


                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#DCFCE7] text-sm font-semibold text-[#166534]"
                                        >

                                            {{ strtoupper(
                                                substr(
                                                    $user->name,
                                                    0,
                                                    1
                                                )
                                            ) }}

                                        </div>


                                        <div class="min-w-0">

                                            <div
                                                class="truncate text-sm font-medium text-slate-700"
                                            >
                                                {{ $user->name }}
                                            </div>

                                            <div
                                                class="mt-0.5 text-xs text-slate-400"
                                            >
                                                #{{ $user->id }}
                                            </div>

                                        </div>

                                    </div>

                                </td>



                                {{-- ================================= --}}
                                {{-- CONTACT                              --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <div class="text-sm text-slate-700">
                                        {{ $user->phone }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-slate-400">
                                        {{ $user->email ?: 'Chưa cập nhật email' }}
                                    </div>

                                </td>



                                {{-- ================================= --}}
                                {{-- ROLE                                 --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4 text-center">

                                    @if($user->role === 'admin')

                                        <span
                                            class="inline-flex rounded-full bg-violet-50 px-2.5 py-1 text-[11px] font-medium text-violet-700"
                                        >
                                            Admin
                                        </span>


                                    @elseif($user->role === 'staff')

                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-700"
                                        >
                                            Staff
                                        </span>


                                    @else

                                        <span
                                            class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-medium text-green-700"
                                        >
                                            Customer
                                        </span>

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- STATUS                               --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4 text-center">

                                    @if($user->status)

                                        <span
                                            class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-medium text-green-700"
                                        >
                                            Hoạt động
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-medium text-red-700"
                                        >
                                            Đã khóa
                                        </span>

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- LAST LOGIN                           --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    @if($user->last_login_at)

                                        <div class="text-sm text-slate-600">

                                            {{ \Carbon\Carbon::parse(
                                                $user->last_login_at
                                            )->format('d/m/Y') }}

                                        </div>

                                        <div class="mt-0.5 text-xs text-slate-400">

                                            {{ \Carbon\Carbon::parse(
                                                $user->last_login_at
                                            )->format('H:i') }}

                                        </div>

                                    @else

                                        <span class="text-xs text-slate-400">
                                            Chưa đăng nhập
                                        </span>

                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- ACTIONS                              --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">


                                        {{-- ROLE --}}
                                        @if($user->role !== 'admin')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.users.update-role',
                                                    $user
                                                ) }}"
                                            >

                                                @csrf

                                                @method('PATCH')


                                                <select
                                                    name="role"
                                                    onchange="this.form.submit()"
                                                    class="h-9 rounded-lg border border-[#CBD5E1] bg-white px-2 text-xs text-slate-600 outline-none focus:border-[#22C55E]"
                                                >

                                                    <option
                                                        value="customer"
                                                        @selected($user->role === 'customer')
                                                    >
                                                        Customer
                                                    </option>

                                                    <option
                                                        value="staff"
                                                        @selected($user->role === 'staff')
                                                    >
                                                        Staff
                                                    </option>

                                                </select>

                                            </form>

                                        @else

                                            <span
                                                class="text-xs text-slate-400"
                                            >
                                                Bảo vệ
                                            </span>

                                        @endif



                                        {{-- TOGGLE --}}
                                        @if($user->role !== 'admin')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.users.toggle-status',
                                                    $user
                                                ) }}"
                                            >

                                                @csrf

                                                @method('PATCH')


                                                @if($user->status)

                                                    <button
                                                        type="submit"
                                                        class="inline-flex h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-medium text-amber-700 hover:bg-amber-100"
                                                        onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')"
                                                    >
                                                        Khóa
                                                    </button>

                                                @else

                                                    <button
                                                        type="submit"
                                                        class="inline-flex h-9 items-center rounded-lg border border-green-200 bg-green-50 px-3 text-xs font-medium text-green-700 hover:bg-green-100"
                                                        onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?')"
                                                    >
                                                        Mở khóa
                                                    </button>

                                                @endif

                                            </form>

                                        @endif



                                        {{-- DELETE --}}
                                        @if($user->role !== 'admin')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.users.destroy',
                                                    $user
                                                ) }}"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')"
                                            >

                                                @csrf

                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    class="inline-flex h-9 items-center rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-medium text-red-600 hover:bg-red-100"
                                                >
                                                    Xóa
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>


                        @empty


                            <tr>

                                <td
                                    colspan="6"
                                    class="px-5 py-16 text-center"
                                >

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl"
                                    >
                                        👥
                                    </div>

                                    <p
                                        class="mt-4 text-sm font-medium text-slate-700"
                                    >
                                        Không tìm thấy người dùng
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Hãy thử thay đổi từ khóa hoặc bộ lọc.
                                    </p>

                                </td>

                            </tr>


                        @endforelse


                    </tbody>

                </table>

            </div>



            {{-- ================================================= --}}
            {{-- PAGINATION                                         --}}
            {{-- ================================================= --}}

            <div
                class="flex flex-col gap-3 border-t border-[#E2E8F0] bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div class="text-sm text-slate-500">

                    Hiển thị

                    <span class="font-medium text-slate-700">
                        {{ $users->firstItem() ?? 0 }}
                    </span>

                    -

                    <span class="font-medium text-slate-700">
                        {{ $users->lastItem() ?? 0 }}
                    </span>

                    trong

                    <span class="font-medium text-slate-700">
                        {{ $users->total() }}
                    </span>

                    người dùng

                </div>


                <div>
                    {{ $users->links() }}
                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- CREATE USER MODAL                                         --}}
    {{-- ========================================================= --}}

    <div
        id="create-user-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4"
    >

        <div
            class="w-full max-w-lg rounded-2xl bg-white shadow-xl"
        >

            {{-- HEADER --}}
            <div
                class="flex items-center justify-between border-b border-[#E2E8F0] px-6 py-4"
            >

                <div>

                    <h2 class="text-lg font-semibold text-[#0F172A]">
                        Thêm người dùng
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Tạo tài khoản mới cho hệ thống.
                    </p>

                </div>


                <button
                    type="button"
                    onclick="closeCreateUserModal()"
                    class="text-xl text-slate-400 hover:text-slate-600"
                >
                    ×
                </button>

            </div>



            {{-- FORM --}}
            <form
                method="POST"
                action="{{ route('admin.users.store') }}"
                class="space-y-5 p-6"
            >

                @csrf


                {{-- NAME --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Họ và tên
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        maxlength="100"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                        placeholder="Nhập họ và tên"
                    >

                </div>



                {{-- PHONE --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Số điện thoại
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        required
                        maxlength="20"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                        placeholder="Nhập số điện thoại"
                    >

                </div>



                {{-- EMAIL --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                        placeholder="Không bắt buộc"
                    >

                </div>



                {{-- ROLE --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Vai trò
                    </label>

                    <select
                        name="role"
                        required
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] bg-white px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                    >

                        <option value="customer">
                            Customer
                        </option>

                        <option value="staff">
                            Staff
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                    </select>

                </div>



                {{-- PASSWORD --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Mật khẩu
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        minlength="6"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                        placeholder="Tối thiểu 6 ký tự"
                    >

                </div>



                {{-- CONFIRM PASSWORD --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-600">
                        Xác nhận mật khẩu
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        minlength="6"
                        class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-[#DCFCE7]"
                        placeholder="Nhập lại mật khẩu"
                    >

                </div>



                {{-- BUTTONS --}}
                <div
                    class="flex justify-end gap-3 border-t border-[#E2E8F0] pt-5"
                >

                    <button
                        type="button"
                        onclick="closeCreateUserModal()"
                        class="rounded-lg border border-[#CBD5E1] bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Hủy
                    </button>


                    <button
                        type="submit"
                        class="rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16A34A]"
                    >
                        Tạo tài khoản
                    </button>

                </div>

            </form>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- JAVASCRIPT                                                 --}}
    {{-- ========================================================= --}}

    <script>

        function openCreateUserModal()
        {
            const modal =
                document.getElementById(
                    'create-user-modal'
                );

            if (!modal) {
                return;
            }

            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );
        }


        function closeCreateUserModal()
        {
            const modal =
                document.getElementById(
                    'create-user-modal'
                );

            if (!modal) {
                return;
            }

            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );
        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {

                    closeCreateUserModal();

                }

            }
        );

    </script>

@endsection