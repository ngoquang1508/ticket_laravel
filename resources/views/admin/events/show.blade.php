@extends('layouts.admin')
@section('title', $event->name)
@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.events.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50">Quay
                    lại</a>
                @if($event->sale_mode !== 'free_sale')<a href="{{ route('admin.events.seat-map.edit', $event) }}"
                class="rounded-lg border px-4 py-2 font-semibold">Cấu hình vé / seatmap</a>@endif
                <a href="{{ route('admin.events.edit', $event) }}"
                    class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white">Sửa</a>
                <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc muốn xóa event này?')">@csrf @method('DELETE')<button
                        class="rounded-lg border border-red-200 px-4 py-2 font-semibold text-red-600">Xóa</button></form>
            </div>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
                    @if($event->image)
                        <div class="flex items-center justify-center border-b bg-gray-100">
                            <img src="{{ asset('storage/' . $event->image) }}" class="w-full max-h-[600px] object-contain">
                        </div>
                    @endif
                    <div class="p-6">
                        <div class="mb-6">
                            <h2 class="mb-3 text-lg font-bold text-gray-900">Thông tin chi tiết</h2>
                            <dl class="grid gap-4 rounded-xl border bg-gray-50 p-5 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Địa điểm</dt>
                                    <dd class="mt-1 font-medium text-gray-900">{{ $event->location->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Thời gian</dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        {{ $event->starts_at->format('d/m/Y H:i') }} -
                                        {{ $event->ends_at->format('d/m/Y H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Hình thức bán vé
                                    </dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        {{ $event->sale_mode === 'assigned_seat' ? 'Ghế ngồi (Assigned Seat)' : ($event->sale_mode === 'free_sale' ? 'Tự do (Free Sale)' : 'Khu vực (Zone)') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Sự kiện cha</dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        {{ $event->parentEvent?->name ?: 'Không có' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mb-6">
                            <h2 class="mb-2 text-lg font-bold text-gray-900">Mô tả sự kiện</h2>
                            <div class="text-gray-700">
                                @if($event->description)
                                    <p class="whitespace-pre-line leading-relaxed">{{ $event->description }}</p>
                                @else
                                    <p class="italic text-gray-500">Chưa có mô tả cho sự kiện này.</p>
                                @endif
                            </div>
                        </div>

                        @if($event->categories->count() > 0)
                            <div>
                                <h2 class="mb-2 text-lg font-bold text-gray-900">Danh mục Thẻ</h2>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($event->categories as $category)
                                        <span
                                            class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 border border-primary-100">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <h2 class="flex gap-2 mb-4 font-semibold">Trạng thái: <p
                            class="{{ $event->is_published ? 'text-green-600' : 'text-red-600' }}">
                            {{ $event->is_published ? 'Đã xuất bản' : 'Chưa xuất bản' }}
                        </p>
                    </h2>
                    <div class="space-y-3 text-sm">
                        <p>Bài đăng nổi bật: <strong>{{ $event->is_featured ? 'Có' : 'Không' }}</strong></p>
                        <p>Loại vé: <strong>{{ $event->ticketTypes->count() }}</strong></p>
                        <p>Tổng ghế:
                            <strong>{{ $event->sale_mode === 'assigned_seat' ? $event->seats_count : 'Không áp dụng' }}</strong>
                        </p>
                        <p>Sơ đồ:
                            <strong>{{ $event->sale_mode === 'free_sale' ? 'Không áp dụng' : ($event->seatMap ? 'Đã thiết lập' : 'Chưa thiết lập') }}</strong>
                        </p>
                    </div>
                </div>
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <h2 class="mb-3 font-semibold">Cấu hình vé</h2>
                    @forelse($event->ticketTypes as $ticket)
                        <div class="flex justify-between border-b py-2 text-sm last:border-0">
                            <span>{{ $ticket->name }} <small class="text-gray-700">({{ $ticket->quantity }} vé)</small></span>
                            <span>{{ number_format($ticket->price, 0, ',', '.') }}đ</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Chưa cấu hình vé.</p>
                    @endforelse

                    @if($event->ticketTypes->count() > 0)
                        <div class="pt-3 flex justify-between font-semibold text-sm">
                            <span class="text-xl font-bold">Tổng cộng</span>
                            <span class="text-xl font-bold">{{ $event->ticketTypes->sum('quantity') }} vé</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection