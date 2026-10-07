@extends('layouts.admin')

@section('title', 'Sửa địa điểm')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Sửa địa điểm</h1>
    <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="space-y-5 rounded-xl border bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.locations._form')
        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 font-semibold text-white hover:bg-primary-700">Cập nhật</button>
            <a href="{{ route('admin.locations.index') }}" class="rounded-lg border px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
        </div>
    </form>
</div>
@endsection
