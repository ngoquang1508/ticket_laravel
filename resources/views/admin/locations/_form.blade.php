<div>
    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Tên địa điểm</label>
    <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name', $location->name ?? '') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
</div>

<div>
    <label for="address" class="mb-2 block text-sm font-medium text-gray-700">Địa chỉ</label>
    <input id="address" name="address" type="text" required maxlength="500" value="{{ old('address', $location->address ?? '') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
</div>

<div>
    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Mô tả</label>
    <textarea id="description" name="description" rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">{{ old('description', $location->description ?? '') }}</textarea>
</div>
