@extends('layouts.admin')
@section('title', 'Thêm loại vé')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8"><h1 class="mb-6 text-2xl font-bold">Thêm loại vé</h1><form action="{{ route('admin.events.ticket-types.store', $event) }}" method="POST" class="space-y-5 rounded-xl border bg-white p-6 shadow-sm">@csrf @include('admin.ticket-types._form')<div class="flex gap-3"><button class="rounded-lg bg-primary-600 px-5 py-2.5 font-semibold text-white">Lưu loại vé</button><a href="{{ route('admin.events.ticket-types.index', $event) }}" class="rounded-lg border px-5 py-2.5 font-semibold">Hủy</a></div></form></div>
@endsection
