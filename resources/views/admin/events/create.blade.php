@extends('layouts.admin')
@section('title', 'Thêm sự kiện')
@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8"><h1 class="mb-6 text-2xl font-bold">Thêm sự kiện</h1><form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-xl border bg-white p-6 shadow-sm">@csrf @include('admin.events._form')<div class="flex gap-3"><button class="rounded-lg bg-primary-600 px-5 py-2.5 font-semibold text-white hover:bg-primary-700">Tạo sự kiện</button><a href="{{ route('admin.events.index') }}" class="rounded-lg border px-5 py-2.5 font-semibold">Hủy</a></div></form></div>
@endsection
