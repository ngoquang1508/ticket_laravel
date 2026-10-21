@extends('layouts.admin')

@section('title', 'Quản lý sự kiện')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Tiêu đề và nút thêm sự kiện --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Quản lý sự kiện</h1>
                <p class="mt-1 text-sm text-gray-500">Danh sách, theo dõi tình trạng bán vé và quản lý các sự kiện.</p>
            </div>
            <div>
                <a href="{{ route('admin.events.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-icons.plus class="h-4 w-4" />
                    Thêm sự kiện
                </a>
            </div>
        </div>

        {{-- Bộ lọc sự kiện --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.events.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12 items-center">
                {{-- Tìm kiếm tên --}}
                <div class="relative lg:col-span-4">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <x-icons.search class="h-4 w-4" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm kiếm theo tên sự kiện..."
                        class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Lọc địa điểm --}}
                <div class="lg:col-span-2">
                    <select name="location_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả địa điểm</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lọc hình thức --}}
                <div class="lg:col-span-2">
                    <select name="sale_mode"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả hình thức</option>
                        <option value="assigned_seat" @selected(request('sale_mode') === 'assigned_seat')>Ghế ngồi</option>
                        <option value="general_admission" @selected(request('sale_mode') === 'general_admission')>Khu vực
                        </option>
                        <option value="free_sale" @selected(request('sale_mode') === 'free_sale')>Tự do</option>
                    </select>
                </div>

                {{-- Lọc trạng thái --}}
                <div class="lg:col-span-2">
                    <select name="is_published"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" @selected(request('is_published') === '1')>Đã đăng</option>
                        <option value="0" @selected(request('is_published') === '0')>Bản nháp</option>
                    </select>
                </div>

                {{-- Nút bấm Lọc & Đặt lại --}}
                <div class="flex items-center gap-2 lg:col-span-2">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 cursor-pointer">
                        Lọc
                    </button>
                    @if (request()->anyFilled(['search', 'location_id', 'sale_mode', 'is_published']))
                        <a href="{{ route('admin.events.index') }}" title="Xóa bộ lọc"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                            <x-icons.close class="h-4 w-4 text-gray-500" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Bảng danh sách sự kiện --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead
                        class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-4 min-w-[260px]">Sự kiện</th>
                            <th class="px-5 py-4 min-w-[180px]">Địa điểm</th>
                            <th class="px-5 py-4 min-w-[160px]">Thời gian</th>
                            <th class="px-5 py-4 min-w-[110px]">Hình thức</th>
                            <th class="px-5 py-4 min-w-[130px]">Trạng thái</th>
                            <th class="px-5 py-4 min-w-[180px]">Số vé (Đã bán / Tổng)</th>
                            <th class="px-5 py-4 min-w-[150px]">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($events as $event)
                            @php
                                $sold = (int) $event->sold_tickets;
                                $paid = (int) $event->paid_tickets;
                                $total = (int) $event->total_tickets;
                                $percent = $total > 0 ? min(100, round(($sold / $total) * 100)) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                {{-- Cột thông tin sự kiện --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3.5">
                                        @if ($event->image)
                                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                                class="h-12 w-16 shrink-0 rounded-lg object-cover border border-gray-200 bg-gray-100 shadow-2xs">
                                        @else
                                            <div
                                                class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400">
                                                <x-icons.photograph class="h-6 w-6" />
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('admin.events.show', $event) }}"
                                                class="font-semibold text-gray-900 hover:text-primary-600 line-clamp-1 transition">
                                                {{ $event->name }}
                                            </a>
                                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                @forelse($event->categories->take(2) as $category)
                                                    <span
                                                        class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-[11px] font-medium text-gray-600">
                                                        {{ $category->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400">#{{ $event->id }}</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Cột địa điểm --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-1.5">
                                        <x-icons.location class="h-4 w-4 shrink-0 text-gray-400 mt-0.5" />
                                        <span class="font-medium text-gray-800 line-clamp-2">
                                            {{ $event->location->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Cột thời gian --}}
                                <td class="px-5 py-4">
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center gap-1.5 font-medium text-gray-800">
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            <span>{{ $event->starts_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-gray-500 pl-3">
                                            <span>đến {{ $event->ends_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Cột hình thức bán vé --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($event->sale_mode === 'assigned_seat')
                                        <span
                                            class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            Ghế ngồi
                                        </span>
                                    @elseif($event->sale_mode === 'general_admission')
                                        <span
                                            class="inline-flex items-center rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                            Khu vực
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            Tự do
                                        </span>
                                    @endif
                                </td>

                                {{-- Cột trạng thái --}}
                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-start gap-1.5">
                                        @if ($event->is_published)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Đã đăng
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Bản nháp
                                            </span>
                                        @endif

                                        @if ($event->is_featured)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full border border-yellow-200 bg-yellow-50 px-2 py-0.5 text-[11px] font-semibold text-yellow-700">
                                                ★ Nổi bật
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Cột số vé (Đã bán / Tổng số vé) --}}
                                <td class="px-5 py-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-baseline justify-between gap-2 text-xs">
                                            <div>
                                                <span class="text-sm font-bold text-gray-900">{{ number_format($sold) }}</span>
                                                <span class="text-gray-400 mx-1">/</span>
                                                <span class="font-medium text-gray-600">{{ number_format($total) }} vé</span>
                                            </div>
                                            <span
                                                class="font-semibold text-[11px] {{ $percent >= 100 ? 'text-emerald-700' : ($percent > 0 ? 'text-primary-700' : 'text-gray-500') }}">
                                                {{ $percent }}%
                                            </span>
                                        </div>
                                        {{-- Thanh tiến độ bán vé --}}
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $percent >= 100 ? 'bg-emerald-600' : ($percent > 0 ? 'bg-primary-500' : 'bg-gray-300') }}"
                                                style="width: {{ $percent }}%"></div>
                                        </div>
                                        @if ($sold > 0)
                                            <div class="text-[11px] text-gray-500 leading-tight">
                                                @if ($paid > 0 && $sold > $paid)
                                                    <span>{{ $paid }} đã thanh toán • {{ $sold - $paid }} đang giữ</span>
                                                @elseif ($paid > 0 && $sold == $paid)
                                                    <span class="text-emerald-600 font-medium">{{ $paid }} vé đã thanh toán</span>
                                                @else
                                                    <span class="text-amber-600 font-medium">{{ $sold }} vé đang giữ (chờ thanh
                                                        toán)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Cột thao tác --}}
                                <!-- xem -->
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.events.show', $event) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Xem chi tiết sự kiện">
                                            <x-icons.eye />
                                        </a>
                                        <!-- sửa -->
                                        <a href="{{ route('admin.events.edit', $event) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Chỉnh sửa sự kiện">
                                            <x-icons.pencil />
                                        </a>
                                        <!-- sơ đồ -->
                                        @if ($event->sale_mode !== 'free_sale')
                                            <a href="{{ route('admin.events.seat-map.edit', $event) }}"
                                                class="rounded-lg border border-indigo-200 bg-indigo-50/50 px-2.5 py-1.5 text-xs font-medium text-indigo-700 transition hover:bg-indigo-100 hover:border-indigo-300 shadow-2xs"
                                                title="Cấu hình sơ đồ và vé">
                                                <x-icons.layout />
                                            </a>
                                        @endif

                                        <!-- xóa -->
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện {{ $event->name }}?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                                title="Xóa sự kiện">
                                                <x-icons.delete />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-icons.calendar class="h-10 w-10 text-gray-300 mb-2" />
                                        <p class="font-medium text-gray-600">Không tìm thấy sự kiện nào.</p>
                                        <p class="text-xs text-gray-400 mt-1">Thử thay đổi bộ lọc tìm kiếm hoặc thêm sự kiện
                                            mới.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    </div>
@endsection