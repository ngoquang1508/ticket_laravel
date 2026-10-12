@extends('layouts.admin')
@section('title', 'Loại vé - ' . $event->name)

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">{{ $event->name }}</p>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý loại vé</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.show', $event) }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Quay lại
                </a>
                <a href="{{ route('admin.events.ticket-types.create', $event) }}"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition">
                    + Thêm loại vé
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tên</th>
                            <th class="px-6 py-4 font-semibold">Giá</th>
                            <th class="px-6 py-4 font-semibold">Số lượng</th>
                            <th class="px-6 py-4 font-semibold">Đã bán</th>
                            <th class="px-6 py-4 font-semibold">Trạng thái</th>
                            <th class="px-6 py-4 text-right font-semibold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($ticketTypes as $ticketType)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $ticketType->name }}
                                    @if ($ticketType->description)
                                        <p class="text-xs text-gray-500 font-normal mt-0.5">{{ $ticketType->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ number_format($ticketType->price, 0, ',', '.') }}đ</td>
                                <td class="px-6 py-4 text-gray-700">{{ number_format($ticketType->quantity) }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ number_format($ticketType->sold_quantity) }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-medium {{ $ticketType->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $ticketType->is_active ? 'Đang bán' : 'Tắt' }}
                                    </span>
                                </td>
                                {{-- Cột thao tác --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút sửa --}}
                                        <a href="{{ route('admin.events.ticket-types.edit', [$event, $ticketType]) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Chỉnh sửa loại vé">
                                            <x-icons.pencil />
                                        </a>

                                        {{-- Nút xóa --}}
                                        <form action="{{ route('admin.events.ticket-types.destroy', [$event, $ticketType]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa loại vé {{ $ticketType->name }}?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                                title="Xóa loại vé">
                                                <x-icons.delete />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có loại vé nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $ticketTypes->links() }}</div>
    </div>
@endsection
