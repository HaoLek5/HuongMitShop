<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Hương Mít Shop')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="min-h-screen bg-[#F8FAFC] text-[#0F172A]">

    {{-- =========================================================
         HEADER CUSTOMER
    ========================================================== --}}

    <header class="sticky top-0 z-40 border-b border-[#E2E8F0] bg-white">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- =====================================================
                 HEADER MAIN
            ====================================================== --}}

            <div class="flex min-h-16 items-center gap-4">


                {{-- =================================================
                     LOGO
                ================================================== --}}

                <a
                    href="{{ route('home') }}"
                    class="shrink-0 text-xl font-bold text-[#16A34A] sm:text-2xl"
                >
                    Hương Mít Shop
                </a>


                {{-- =================================================
                     SEARCH DESKTOP
                ================================================== --}}

                <form
                    method="GET"
                    action="{{ route('home') }}"
                    class="hidden min-w-0 flex-1 md:block"
                >

                    <div class="relative mx-auto max-w-xl">

                        {{-- Search icon --}}

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            ></circle>

                            <path
                                d="m20 20-3.5-3.5"
                            ></path>

                        </svg>


                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Tìm kiếm sản phẩm..."
                            class="w-full rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] py-2.5 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:bg-white focus:ring-2 focus:ring-green-100"
                        >

                    </div>

                </form>


                {{-- =================================================
                     NAVIGATION
                ================================================== --}}

                <nav class="ml-auto flex items-center gap-1 sm:gap-2">


                    {{-- =================================================
                         TRANG CHỦ
                    ================================================== --}}

                    <a
                        href="{{ route('home') }}"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                    >
                        <span class="hidden sm:inline">
                            Trang chủ
                        </span>

                        <span class="sm:hidden">
                            🏠
                        </span>
                    </a>


                    @auth

                        {{-- =================================================
                             GIỎ HÀNG
                        ================================================== --}}

                        <a
                            href="{{ route('cart.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                        >

                            <span class="text-base">
                                🛒
                            </span>

                            <span class="hidden sm:inline">
                                Giỏ hàng
                            </span>

                        </a>


                        {{-- =================================================
                             ĐƠN HÀNG CUSTOMER
                        ================================================== --}}

                        @if(auth()->user()->role === 'customer')

                            <a
                                href="{{ route('customer.orders.index') }}"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                            >

                                <span class="text-base">
                                    📦
                                </span>

                                <span class="hidden sm:inline">
                                    Đơn hàng
                                </span>

                            </a>

                        @endif


                        {{-- =================================================
                             ACCOUNT
                        ================================================== --}}

                        <div class="relative">

                            <details class="group">

                                <summary
                                    class="flex cursor-pointer list-none items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                >

                                    <span class="text-base">
                                        👤
                                    </span>


                                    <span class="hidden max-w-28 truncate sm:inline">
                                        {{ auth()->user()->name }}
                                    </span>


                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        class="h-4 w-4 text-slate-400 transition group-open:rotate-180"
                                    >

                                        <path
                                            d="m6 9 6 6 6-6"
                                        />

                                    </svg>

                                </summary>


                                {{-- =================================================
                                     DROPDOWN
                                ================================================== --}}

                                <div
                                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-lg"
                                >


                                    {{-- =============================
                                         USER INFO
                                    ============================== --}}

                                    <div class="border-b border-[#E2E8F0] px-4 py-3">

                                        <div class="text-sm font-semibold text-slate-800">
                                            {{ auth()->user()->name }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            {{ auth()->user()->phone }}
                                        </div>

                                    </div>


                                    {{-- =============================
                                         TÀI KHOẢN
                                    ============================== --}}

                                    @if(auth()->user()->role === 'customer')

                                        <a
                                            href="{{ route('account') }}"
                                            class="block px-4 py-3 text-sm text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                        >
                                            👤 Tài khoản
                                        </a>

                                    @endif


                                    {{-- =============================
                                         ĐƠN HÀNG
                                    ============================== --}}

                                    @if(auth()->user()->role === 'customer')

                                        <a
                                            href="{{ route('customer.orders.index') }}"
                                            class="block px-4 py-3 text-sm text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                        >
                                            📦 Đơn hàng của tôi
                                        </a>

                                    @endif


                                    {{-- =============================
                                         GIỎ HÀNG
                                    ============================== --}}

                                    @if(auth()->user()->role === 'customer')

                                        <a
                                            href="{{ route('cart.index') }}"
                                            class="block px-4 py-3 text-sm text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                        >
                                            🛒 Giỏ hàng
                                        </a>

                                    @endif


                                    {{-- =============================
                                         ĐĂNG XUẤT
                                    ============================== --}}

                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="w-full border-t border-[#E2E8F0] px-4 py-3 text-left text-sm text-red-500 transition hover:bg-red-50 hover:text-red-600"
                                        >
                                            Đăng xuất
                                        </button>

                                    </form>

                                </div>

                            </details>

                        </div>


                    @else

                        {{-- =================================================
                             GUEST
                        ================================================== --}}

                        <a
                            href="{{ route('login') }}"
                            class="hidden rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-[#F0FDF4] hover:text-[#16A34A] sm:inline-flex"
                        >
                            Đăng nhập
                        </a>


                        <a
                            href="{{ route('register') }}"
                            class="inline-flex rounded-lg bg-[#22C55E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
                        >
                            Đăng ký
                        </a>

                    @endauth

                </nav>

            </div>


            {{-- =========================================================
                 SEARCH MOBILE
            ========================================================== --}}

            <form
                method="GET"
                action="{{ route('home') }}"
                class="pb-3 md:hidden"
            >

                <div class="relative">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    >

                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        ></circle>

                        <path
                            d="m20 20-3.5-3.5"
                        ></path>

                    </svg>


                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] py-2.5 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >

                </div>

            </form>

        </div>

    </header>


    {{-- =========================================================
         CONTENT
    ========================================================== --}}

    <main class="min-h-[calc(100vh-64px)]">

        @yield('content')

    </main>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <footer class="mt-12 border-t border-[#E2E8F0] bg-white">

        <div class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-slate-400 sm:px-6 lg:px-8">

            © {{ date('Y') }} Hương Mít Shop

        </div>

    </footer>

</body>

</html>