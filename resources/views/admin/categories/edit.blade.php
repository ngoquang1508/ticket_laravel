@extends('layouts.admin')
@section('title', 'Sửa danh mục')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Sửa danh mục</h1>
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5 rounded-xl border bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        @include('admin.categories._form')
        <div class="flex gap-3"><button class="rounded-lg bg-primary-600 px-5 py-2.5 font-semibold text-white hover:bg-primary-700">Cập nhật</button><a href="{{ route('admin.categories.index') }}" class="rounded-lg border px-5 py-2.5 font-semibold">Hủy</a></div>
    </form>
</div>
@endsection
