@extends('layouts.admin')

@section('title', 'Sửa biến thể')

@section('page-title', 'Sửa biến thể')

@section('content')

    <div class="max-w-3xl">

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-900">
                Sửa biến thể
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $product->name }}
            </p>

        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                Có lỗi xảy ra. Vui lòng kiểm tra lại.
            </div>
        @endif

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <form
                method="POST"
                action="{{ route(
                    'admin.products.variants.update',
                    [$product, $variant]
                ) }}"
            >
                @csrf
                @method('PUT')

                @php
                    $submitText = 'Cập nhật biến thể';
                @endphp

                @include('admin.products.variants._form')

            </form>

        </div>

    </div>

@endsection