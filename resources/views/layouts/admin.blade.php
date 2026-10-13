<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Quản trị')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#F8FAFC] text-[#0F172A]">

    <div class="min-h-screen lg:flex">

        {{-- ===================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ===================================================== --}}

        <aside
            class="fixed inset-y-0 left-0 z-40 hidden w-[260px] border-r border-[#E2E8F0] bg-[#F0FDF4] lg:flex lg:flex-col"
        >

            {{-- LOGO --}}
            <div class="flex h-[72px] items-center border-b border-[#E2E8F0] px-6">

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22C55E] text-lg font-bold text-white"
                    >
                        H
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-[#14532D]">
                            Hương Mít Shop
                        </div>

                        <div class="text-xs text-slate-500">
                            Quản trị cửa hàng
                        </div>
                    </div>

                </a>

            </div>


            {{-- MENU --}}
            <nav class="flex-1 px-4 py-5">

                <p class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    Quản lý
                </p>


                {{-- =============================== --}}
                {{-- THỐNG KÊ --}}
                {{-- =============================== --}}

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="group mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-[#86EFAC] text-[#14532D]'
                        : 'text-[#334155] hover:bg-[#DCFCE7] hover:text-[#14532D]' }}"
                >

                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg
                        {{ request()->routeIs('admin.dashboard')
                            ? 'bg-white/70'
                            : 'bg-transparent group-hover:bg-white/60' }}"
                    >
                        📊
                    </span>

                    <span>
                        Thống kê
                    </span>

                </a>


                {{-- =============================== --}}
                {{-- SẢN PHẨM --}}
                {{-- =============================== --}}

                <a
                    href="{{ route('admin.products.index') }}"
                    class="group mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.products.*')
                        ? 'bg-[#86EFAC] text-[#14532D]'
                        : 'text-[#334155] hover:bg-[#DCFCE7] hover:text-[#14532D]' }}"
                >

                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg
                        {{ request()->routeIs('admin.products.*')
                            ? 'bg-white/70'
                            : 'bg-transparent group-hover:bg-white/60' }}"
                    >
                        🛍️
                    </span>

                    <span>
                        Sản phẩm
                    </span>

                </a>


                {{-- =============================== --}}
                {{-- NHẬP HÀNG --}}
                {{-- =============================== --}}

                <a
                    href="{{ route('admin.purchase-receipts.index') }}"
                    class="group mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.purchase-receipts.*')
                        ? 'bg-[#86EFAC] text-[#14532D]'
                        : 'text-[#334155] hover:bg-[#DCFCE7] hover:text-[#14532D]' }}"
                >

                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg
                        {{ request()->routeIs('admin.purchase-receipts.*')
                            ? 'bg-white/70'
                            : 'bg-transparent group-hover:bg-white/60' }}"
                    >
                        📦
                    </span>

                    <span>
                        Nhập hàng
                    </span>

                </a>


                {{-- =============================== --}}
                {{-- ĐƠN HÀNG --}}
                {{-- =============================== --}}

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="group mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.orders.*')
                        ? 'bg-[#86EFAC] text-[#14532D]'
                        : 'text-[#334155] hover:bg-[#DCFCE7] hover:text-[#14532D]' }}"
                >

                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg
                        {{ request()->routeIs('admin.orders.*')
                            ? 'bg-white/70'
                            : 'bg-transparent group-hover:bg-white/60' }}"
                    >
                        🧾
                    </span>

                    <span>
                        Đơn hàng
                    </span>

                </a>

                @if(Auth::user()->role === 'admin')

    <a
        href="{{ route('admin.users.index') }}"
        class="group mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
        {{ request()->routeIs('admin.users.*')
            ? 'bg-[#86EFAC] text-[#14532D]'
            : 'text-[#334155] hover:bg-[#DCFCE7] hover:text-[#14532D]' }}"
    >

        <span
            class="flex h-8 w-8 items-center justify-center rounded-lg
            {{ request()->routeIs('admin.users.*')
                ? 'bg-white/70'
                : 'bg-transparent group-hover:bg-white/60' }}"
        >
            👥
        </span>

        <span>
            Người dùng
        </span>

    </a>

@endif

            </nav>


            {{-- SIDEBAR BOTTOM --}}
            <div class="border-t border-[#E2E8F0] p-4">

                <div class="rounded-xl border border-[#E2E8F0] bg-white p-3">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#DCFCE7] text-sm font-semibold text-[#166534]"
                        >
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">

                            <div class="truncate text-sm font-medium text-slate-700">
                                {{ Auth::user()->name }}
                            </div>

                            <div class="text-xs text-slate-400">
                                {{ ucfirst(Auth::user()->role) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </aside>


        {{-- ===================================================== --}}
        {{-- MAIN AREA --}}
        {{-- ===================================================== --}}

        <div class="min-w-0 flex-1 lg:ml-[260px]">


            {{-- ================================================= --}}
            {{-- HEADER --}}
            {{-- ================================================= --}}

            <header
                class="sticky top-0 z-30 h-[72px] border-b border-[#E2E8F0] bg-white"
            >

                <div class="flex h-full items-center justify-between px-4 sm:px-6 lg:px-8">

                    {{-- LEFT --}}
                    <div class="flex items-center gap-4">

                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#E2E8F0] bg-white text-slate-500 hover:bg-slate-50 lg:hidden"
                        >
                            ☰
                        </button>


                        <div>

                            <h1 class="text-lg font-semibold text-[#0F172A]">
                                @yield('page-title', 'Quản trị')
                            </h1>

                            <p class="hidden text-xs text-slate-400 sm:block">
                                Hệ thống quản lý Hương Mít Shop
                            </p>

                        </div>

                    </div>


                    {{-- RIGHT --}}
                    <div class="flex items-center gap-3">

                        {{-- USER --}}
                        <details class="group relative">

                            <summary
                                class="flex cursor-pointer list-none items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-50"
                            >

                                <div class="hidden text-right sm:block">

                                    <div class="text-sm font-medium text-slate-700">
                                        {{ Auth::user()->name }}
                                    </div>

                                    <div class="text-xs text-slate-400">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </div>

                                </div>


                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-[#DCFCE7] text-sm font-semibold text-[#166534]"
                                >
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>

                                <span class="text-xs text-slate-400">
                                    ▼
                                </span>

                            </summary>


                            {{-- DROPDOWN --}}
                            <div
                                class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-lg"
                            >

                                <div class="border-b border-[#E2E8F0] px-4 py-3">

                                    <div class="text-sm font-semibold text-slate-700">
                                        {{ Auth::user()->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ Auth::user()->phone }}
                                    </div>

                                </div>


                                <div class="p-2">

                                    <a
                                        href="{{ route('home') }}"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50"
                                    >
                                        🏠
                                        <span>Trang bán hàng</span>
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-red-600 hover:bg-red-50"
                                        >
                                            ↪
                                            <span>Đăng xuất</span>
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </details>

                    </div>

                </div>

            </header>


            {{-- ================================================= --}}
            {{-- CONTENT --}}
            {{-- ================================================= --}}

            <main class="min-h-[calc(100vh-72px)] p-4 sm:p-6 lg:p-8">

                <div class="mx-auto max-w-7xl">

                    {{-- SUCCESS --}}
                    @if(session('success'))

                        <div
                            class="mb-6 rounded-xl border border-green-200 bg-[#F0FDF4] px-4 py-3 text-sm text-green-700"
                        >
                            {{ session('success') }}
                        </div>

                    @endif


                    {{-- ERROR --}}
                    @if(session('error'))

                        <div
                            class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                        >
                            {{ session('error') }}
                        </div>

                    @endif


                    {{-- VALIDATION --}}
                    @if($errors->any())

                        <div
                            class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                        >

                            <div class="font-semibold">
                                Có lỗi xảy ra
                            </div>

                            <ul class="mt-2 list-disc space-y-1 pl-5">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    @yield('content')

                </div>

            </main>

        </div>

    </div>

</body>

</html>