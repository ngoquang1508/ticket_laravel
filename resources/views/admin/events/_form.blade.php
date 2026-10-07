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

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="starts_at" class="mb-2 block text-sm font-medium">Bắt đầu</label>
        <input id="starts_at" type="datetime-local" name="starts_at" required value="{{ old('starts_at', isset($event) ? $event->starts_at?->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border px-4 py-2.5">
    </div>
    <div>
        <label for="ends_at" class="mb-2 block text-sm font-medium">Kết thúc</label>
        <input id="ends_at" type="datetime-local" name="ends_at" required value="{{ old('ends_at', isset($event) ? $event->ends_at?->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border px-4 py-2.5">
    </div>
</div>

@php($selectedSaleMode = old('sale_mode', $event->sale_mode ?? 'general_admission'))
<div>
    <label for="sale_mode" class="mb-2 block text-sm font-medium">Hình thức bán vé</label>
    <select id="sale_mode" name="sale_mode" required class="w-full rounded-lg border px-4 py-2.5">
        <option value="assigned_seat" @selected($selectedSaleMode === 'assigned_seat')>Ghế (chọn ghế cụ thể)</option>
        <option value="general_admission" @selected($selectedSaleMode === 'general_admission')>Khu vực (khu A, B...)</option>
        <option value="free_sale" @selected($selectedSaleMode === 'free_sale')>Tự do (chỉ số lượng)</option>
    </select>
</div>

<div id="free-ticket-fields" class="grid gap-5 rounded-xl border border-primary-100 bg-primary-50 p-4 md:grid-cols-3">
    <div class="md:col-span-3">
        <p class="font-semibold text-primary-900">Vé tự do</p>
        <p class="text-sm text-primary-700">Nhập thông tin vé trực tiếp tại đây. Hình thức này không tạo seatmap.</p>
    </div>
    <div>
        <label for="ticket_name" class="mb-2 block text-sm font-medium">Tên loại vé</label>
        <input id="ticket_name" name="ticket_name" value="{{ old('ticket_name', $event?->ticketTypes?->first()?->name ?? 'Vé thường') }}" class="w-full rounded-lg border px-3 py-2.5">
    </div>
    <div>
        <label for="ticket_price" class="mb-2 block text-sm font-medium">Giá vé</label>
        <input id="ticket_price" name="ticket_price" type="number" min="0" step="0.01" value="{{ old('ticket_price', $event?->ticketTypes?->first()?->price ?? '') }}" class="w-full rounded-lg border px-3 py-2.5">
    </div>
    <div>
        <label for="ticket_quantity" class="mb-2 block text-sm font-medium">Số lượng vé</label>
        <input id="ticket_quantity" name="ticket_quantity" type="number" min="1" value="{{ old('ticket_quantity', $event?->ticketTypes?->first()?->quantity ?? '') }}" class="w-full rounded-lg border px-3 py-2.5">
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
            const inputs = fields.querySelectorAll('input');
            const sync = () => {
                const free = mode.value === 'free_sale';
                fields.classList.toggle('hidden', !free);
                inputs.forEach(input => input.required = free);
            };
            mode.addEventListener('change', sync);
            sync();
        })();
    </script>
@endpush
