<div>
    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Tên danh mục</label>
    <input id="name" name="name" type="text" required maxlength="100" value="{{ old('name', $category->name ?? '') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
</div>
<div>
    <label for="slug" class="mb-2 block text-sm font-medium text-gray-700">Slug <span class="font-normal text-gray-400">(để trống sẽ tự tạo)</span></label>
    <input id="slug" name="slug" type="text" maxlength="100" value="{{ old('slug', $category->slug ?? '') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
</div>
<div>
    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Mô tả</label>
    <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<label class="flex items-center gap-2 text-sm font-medium text-gray-700"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))> Đang hoạt động</label>
