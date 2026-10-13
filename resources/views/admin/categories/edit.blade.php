@extends('layouts.admin')

@section('title', 'Sửa danh mục')

@section('page-title', 'Sửa danh mục')

@section('content')

    <div class="max-w-2xl">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Sửa danh mục
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Cập nhật thông tin danh mục.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                Có lỗi xảy ra. Vui lòng kiểm tra lại thông tin.
            </div>
        @endif

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <form
                method="POST"
                action="{{ route('admin.categories.update', $category) }}"
            >
                @csrf
                @method('PUT')

                @php
                    $submitText = 'Cập nhật danh mục';
                @endphp

                @include('admin.categories._form')

            </form>

        </div>

    </div>

@endsection