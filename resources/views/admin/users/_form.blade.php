@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Họ tên <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name" required value="{{ old('name', $user->name ?? '') }}"
            class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-500 @enderror">
        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" id="email" required value="{{ old('email', $user->email ?? '') }}"
            class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror">
        @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">Số điện thoại</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}"
            class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('phone') border-red-500 @enderror">
        @error('phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="role" class="mb-2 block text-sm font-medium text-gray-700">Vai trò <span class="text-red-500">*</span></label>
        <select name="role" id="role" required class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('role') border-red-500 @enderror">
            <option value="customer" @selected(old('role', $user->role ?? 'customer') === 'customer')>Customer</option>
            <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin</option>
        </select>
        @error('role') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="is_active" class="mb-2 block text-sm font-medium text-gray-700">Trạng thái <span class="text-red-500">*</span></label>
        <select name="is_active" id="is_active" required class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('is_active') border-red-500 @enderror">
            <option value="1" @selected(old('is_active', $user->is_active ?? 1) == 1)>Hoạt động</option>
            <option value="0" @selected(old('is_active', $user->is_active ?? 1) == 0)>Khóa tài khoản</option>
        </select>
        @error('is_active') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="mb-2 block text-sm font-medium text-gray-700">Mật khẩu @if(!isset($user))<span class="text-red-500">*</span>@endif</label>
        <input type="password" name="password" id="password" @if(!isset($user)) required @endif
            class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror"
            placeholder="{{ isset($user) ? 'Bỏ trống nếu không muốn đổi mật khẩu' : '' }}">
        @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">Xác nhận mật khẩu @if(!isset($user))<span class="text-red-500">*</span>@endif</label>
        <input type="password" name="password_confirmation" id="password_confirmation" @if(!isset($user)) required @endif
            class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
            placeholder="{{ isset($user) ? 'Bỏ trống nếu không muốn đổi mật khẩu' : '' }}">
    </div>
</div>
