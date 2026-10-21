<div>
    <label for="name" class="mb-2 block text-sm font-medium">Tên sự kiện</label>
    <input id="name" name="name" required maxlength="255" value="{{ old('name', $event->name ?? '') }}" class="w-full rounded-lg border px-4 py-2.5 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="location_id" class="mb-2 block text-sm font-medium">Địa điểm</label>
        <select id="location_id" name="location_id" required class="w-full rounded-lg border px-4 py-2.5">
            @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $event->location_id ?? '') == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="parent_event_id" class="mb-2 block text-sm font-medium">Event cha <span class="font-normal text-gray-400">(không bắt buộc)</span></label>
        <select id="parent_event_id" name="parent_event_id" class="w-full rounded-lg border px-4 py-2.5">
            <option value="">Không có</option>
            @foreach($parentEvents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_event_id', $event->parent_event_id ?? '') == $parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label for="description" class="mb-2 block text-sm font-medium">Mô tả</label>
    <textarea id="description" name="description" rows="5" class="w-full rounded-lg border px-4 py-2.5">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div>
    <label for="image" class="mb-2 block text-sm font-medium">Ảnh sự kiện</label>
    <input id="image" type="file" name="image" accept="image/*" class="w-full rounded-lg border px-4 py-2.5">
    @if(!empty($event?->image))
        <img src="{{ asset('storage/'.$event->image) }}" class="mt-3 h-28 w-44 rounded object-cover">
    @endif
</div>

<div class="grid gap-5 md:grid-cols-3">
    <div>
        <label for="starts_at" class="mb-2 block text-sm font-medium">Bắt đầu sự kiện <span class="text-red-500">*</span></label>
        <input id="starts_at" type="datetime-local" name="starts_at" required value="{{ old('starts_at', isset($event) ? $event->starts_at?->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border px-4 py-2.5">
    </div>
    <div>
        <label for="ends_at" class="mb-2 block text-sm font-medium">Kết thúc sự kiện <span class="text-red-500">*</span></label>
        <input id="ends_at" type="datetime-local" name="ends_at" required value="{{ old('ends_at', isset($event) ? $event->ends_at?->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border px-4 py-2.5">
    </div>
    <div>
        <label for="ticket_sale_starts_at" class="mb-2 block text-sm font-medium">
            <span>Thời gian mở bán vé</span>
        </label>
        <input id="ticket_sale_starts_at" type="datetime-local" name="ticket_sale_starts_at" value="{{ old('ticket_sale_starts_at', isset($event) ? $event->ticket_sale_starts_at?->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border px-4 py-2.5">
        <p class="text-xs text-gray-500 mt-1">Để trống = mở bán ngay khi tạo sự kiện</p>
    </div>
</div>

<?php
    $selectedSaleMode = old('sale_mode', $event->sale_mode ?? 'general_admission');

    // Chuẩn bị dữ liệu danh sách vé tự do (ưu tiên old data, sau đó đến ticketTypes của sự kiện, hoặc tạo sẵn 1 dòng mặc định)
    $freeTickets = old('free_tickets');
    if ($freeTickets === null) {
        if (isset($event) && $event->ticketTypes && $event->ticketTypes->isNotEmpty()) {
            $freeTickets = $event->ticketTypes->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'name' => $ticket->name,
                    'price' => (float) $ticket->price,
                    'quantity' => (int) $ticket->quantity,
                    'sold_quantity' => (int) $ticket->sold_quantity,
                ];
            })->toArray();
        } else {
            $freeTickets = [
                ['id' => null, 'name' => 'Vé thường', 'price' => 0, 'quantity' => 100, 'sold_quantity' => 0]
            ];
        }
    }
?>
<div>
    <label for="sale_mode" class="mb-2 block text-sm font-medium">Hình thức bán vé</label>
    <select id="sale_mode" name="sale_mode" required class="w-full rounded-lg border px-4 py-2.5">
        <option value="assigned_seat" @selected($selectedSaleMode === 'assigned_seat')>Ghế (chọn ghế cụ thể)</option>
        <option value="general_admission" @selected($selectedSaleMode === 'general_admission')>Khu vực (khu A, B...)</option>
        <option value="free_sale" @selected($selectedSaleMode === 'free_sale')>Tự do (chỉ số lượng)</option>
    </select>
</div>

<div id="free-ticket-fields" class="space-y-4 rounded-xl border border-primary-200 bg-primary-50/60 p-4 sm:p-5">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="font-bold text-primary-950 text-base">Danh sách loại vé tự do</p>
            <p class="text-xs sm:text-sm text-primary-700">Cấu hình một hoặc nhiều loại vé (ví dụ: Vé VIP, Vé Thường...). Hình thức này không tạo sơ đồ ghế.</p>
        </div>
        <button type="button" id="btn-add-free-ticket" class="inline-flex items-center justify-center gap-1.5 self-start sm:self-auto rounded-lg bg-primary-600 px-3.5 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition">
            <x-icons.plus class="w-4 h-4" />
            Thêm loại vé
        </button>
    </div>

    @error('free_tickets')
        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
    @enderror

    <div id="free-ticket-list" class="space-y-3">
        @foreach($freeTickets as $idx => $ticket)
            <div class="free-ticket-row rounded-lg border border-gray-200 bg-white p-3.5 sm:p-4 shadow-xs" data-index="{{ $idx }}">
                @if(!empty($ticket['id']))
                    <input type="hidden" name="free_tickets[{{ $idx }}][id]" value="{{ $ticket['id'] }}">
                @endif
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-end">
                    <div class="sm:col-span-5">
                        <label class="mb-1 block text-xs font-semibold text-gray-700">Tên loại vé <span class="text-red-500">*</span></label>
                        <input type="text" name="free_tickets[{{ $idx }}][name]" value="{{ $ticket['name'] ?? '' }}" placeholder="VD: Vé VIP, Vé Thường" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1 block text-xs font-semibold text-gray-700">Giá vé (đ) <span class="text-red-500">*</span></label>
                        <input type="number" min="0" step="1000" name="free_tickets[{{ $idx }}][price]" value="{{ $ticket['price'] ?? 0 }}" placeholder="0" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="mb-1 block text-xs font-semibold text-gray-700">Số lượng <span class="text-red-500">*</span></label>
                        <input type="number" min="{{ max(1, (int)($ticket['sold_quantity'] ?? 0)) }}" name="free_tickets[{{ $idx }}][quantity]" value="{{ $ticket['quantity'] ?? '' }}" placeholder="100" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                        @if(!empty($ticket['sold_quantity']) && $ticket['sold_quantity'] > 0)
                            <p class="mt-1 text-[11px] font-medium text-amber-600">Đã bán: {{ $ticket['sold_quantity'] }} vé</p>
                        @endif
                    </div>
                    <div class="sm:col-span-1 flex items-center justify-end sm:justify-center">
                        <button type="button" class="btn-remove-free-ticket inline-flex items-center justify-center rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 transition" title="Xóa loại vé này" @disabled(!empty($ticket['sold_quantity']) && $ticket['sold_quantity'] > 0)>
                            <x-icons.delete class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div>
    <p class="mb-2 text-sm font-medium">Danh mục</p>
    <div class="grid gap-2 sm:grid-cols-2">
        @foreach($categories as $category)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, old('categories', isset($event) ? $event->categories->pluck('id')->all() : [])))>
                {{ $category->name }}
            </label>
        @endforeach
    </div>
</div>

<div class="flex gap-6">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $event->is_featured ?? false))> Sự kiện nổi bật</label>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $event->is_published ?? false))> Đăng sự kiện</label>
</div>

@push('scripts')
    <script>
        (() => {
            const mode = document.getElementById('sale_mode');
            const fields = document.getElementById('free-ticket-fields');
            const list = document.getElementById('free-ticket-list');
            const btnAdd = document.getElementById('btn-add-free-ticket');

            let ticketIndex = {{ count($freeTickets) }};

            // Hàm đồng bộ trạng thái required cho các input vé tự do
            const updateInputsRequired = (isRequired) => {
                const inputs = fields.querySelectorAll('.free-ticket-input');
                inputs.forEach(input => {
                    input.required = isRequired;
                });
            };

            // Đồng bộ ẩn/hiện khối vé tự do theo hình thức bán vé
            const syncVisibility = () => {
                const isFree = mode.value === 'free_sale';
                fields.classList.toggle('hidden', !isFree);
                updateInputsRequired(isFree);
            };

            // Thêm dòng loại vé mới
            if (btnAdd) {
                btnAdd.addEventListener('click', () => {
                    const idx = 'new_' + Date.now() + '_' + (ticketIndex++);
                    const row = document.createElement('div');
                    row.className = 'free-ticket-row rounded-lg border border-gray-200 bg-white p-3.5 sm:p-4 shadow-xs';
                    row.dataset.index = idx;
                    row.innerHTML = `
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-end">
                            <div class="sm:col-span-5">
                                <label class="mb-1 block text-xs font-semibold text-gray-700">Tên loại vé <span class="text-red-500">*</span></label>
                                <input type="text" name="free_tickets[${idx}][name]" required placeholder="VD: Vé VIP, Vé Thường" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1 block text-xs font-semibold text-gray-700">Giá vé (đ) <span class="text-red-500">*</span></label>
                                <input type="number" min="0" step="1000" name="free_tickets[${idx}][price]" value="0" required placeholder="0" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="mb-1 block text-xs font-semibold text-gray-700">Số lượng <span class="text-red-500">*</span></label>
                                <input type="number" min="1" name="free_tickets[${idx}][quantity]" value="100" required placeholder="100" class="free-ticket-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                            </div>
                            <div class="sm:col-span-1 flex items-center justify-end sm:justify-center">
                                <button type="button" class="btn-remove-free-ticket inline-flex items-center justify-center rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 transition" title="Xóa loại vé này">
                                    <x-icons.delete class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    `;
                    list.appendChild(row);
                    updateInputsRequired(mode.value === 'free_sale');
                });
            }

            // Xóa dòng loại vé
            if (list) {
                list.addEventListener('click', (e) => {
                    const removeBtn = e.target.closest('.btn-remove-free-ticket');
                    if (!removeBtn || removeBtn.disabled) return;

                    const rows = list.querySelectorAll('.free-ticket-row');
                    if (rows.length <= 1) {
                        alert('Cần có ít nhất một loại vé cho sự kiện bán vé tự do.');
                        return;
                    }

                    const row = removeBtn.closest('.free-ticket-row');
                    if (row) {
                        row.remove();
                    }
                });
            }

            mode.addEventListener('change', syncVisibility);
            syncVisibility();
        })();
    </script>
@endpush
