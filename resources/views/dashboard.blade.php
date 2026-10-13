@extends('layouts.app')

@section('title', 'Tài khoản')

@section('content')

<div class="min-h-screen bg-[#F8FAFC]">

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="mb-8">

            <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">
                Tài khoản của tôi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Quản lý thông tin cá nhân của bạn.
            </p>

        </div>


        {{-- =========================================================
             THÔNG TIN CÁ NHÂN
        ========================================================== --}}

        <div class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white">

            <div class="border-b border-[#E2E8F0] px-6 py-5">

                <h2 class="text-lg font-semibold text-slate-800">
                    Thông tin cá nhân
                </h2>

            </div>


            <div class="divide-y divide-[#E2E8F0]">

                {{-- Họ tên --}}

                <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="text-sm text-slate-400">
                            Họ tên
                        </div>

                        <div class="mt-1 font-medium text-slate-800">
                            {{ auth()->user()->name }}
                        </div>

                    </div>

                </div>


                {{-- Số điện thoại --}}

                <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="text-sm text-slate-400">
                            Số điện thoại
                        </div>

                        <div class="mt-1 font-medium text-slate-800">
                            {{ auth()->user()->phone }}
                        </div>

                    </div>

                </div>


                {{-- Email --}}

                <div class="flex flex-col gap-1 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="text-sm text-slate-400">
                            Email
                        </div>

                        <div class="mt-1 font-medium text-slate-800">
                            {{ auth()->user()->email ?? 'Chưa cập nhật' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             THAO TÁC
        ========================================================== --}}

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">

            <a
                href="{{ route('home') }}"
                class="inline-flex items-center justify-center rounded-lg border border-[#BBF7D0] bg-white px-5 py-2.5 text-sm font-semibold text-[#16A34A] transition hover:bg-[#F0FDF4]"
            >
                ← Tiếp tục mua sắm
            </a>


            <a
                href="{{ route('cart.index') }}"
                class="inline-flex items-center justify-center rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
            >
                🛒 Xem giỏ hàng
            </a>

        </div>

    </div>

</div>

@endsection