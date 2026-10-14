<div class="relative" id="admin-notification-container">
    {{-- Nút chuông thông báo trên topbar --}}
    <button type="button" id="admin-notification-btn" aria-expanded="false" aria-label="Thông báo"
        class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-gray-600 hover:bg-gray-100 hover:text-primary-600 focus:outline-none transition-colors">
        <x-icons.notification class="h-6 w-6 transition-all duration-200 cursor-pointer" />

        @if (($adminUnreadNotificationsCount ?? 0) > 0)
            <span id="notification-badge"
                class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-white animate-pulse">
                {{ $adminUnreadNotificationsCount > 9 ? '9+' : $adminUnreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown thông báo --}}
    <div id="admin-notification-menu"
        class="hidden absolute right-0 top-full mt-2 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 z-50 border border-gray-100 overflow-hidden transition-all duration-150">

        {{-- Header của Dropdown --}}
        <div class="border-b border-gray-100 px-4 pt-3.5 pb-2.5 bg-gray-50/70">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-gray-900">Thông báo</h3>
                    @if (($adminUnreadNotificationsCount ?? 0) > 0)
                        <span id="header-unread-badge"
                            class="rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600 border border-red-200">
                            {{ $adminUnreadNotificationsCount }} mới
                        </span>
                    @endif
                </div>

                @if (($adminUnreadNotificationsCount ?? 0) > 0)
                    <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Đánh dấu tất cả là đã đọc"
                            class="text-xs font-medium text-primary-600 hover:text-primary-700 hover:underline transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Đã đọc tất cả
                        </button>
                    </form>
                @endif
            </div>

            {{-- Các Tab lọc: Tất cả / Chưa đọc --}}
            <div class="mt-2.5 flex items-center gap-1.5 p-1 bg-gray-200/60 rounded-xl text-xs font-semibold">
                <button type="button" id="tab-all-btn"
                    class="tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg bg-white text-gray-900 shadow-sm transition-all">
                    <span>Tất cả</span>
                    <span id="tab-all-count" class="rounded-full bg-gray-100 px-1.5 py-0.2 text-[10px] text-gray-600">
                        {{ $adminTotalNotificationsCount ?? count($adminLatestNotifications ?? []) }}
                    </span>
                </button>

                <button type="button" id="tab-unread-btn"
                    class="tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-gray-600 hover:text-gray-900 transition-all">
                    <span>Chưa đọc</span>
                    @if (($adminUnreadNotificationsCount ?? 0) > 0)
                        <span id="tab-unread-count"
                            class="rounded-full bg-red-500 px-1.5 py-0.2 text-[10px] font-bold text-white">
                            {{ $adminUnreadNotificationsCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Danh sách thông báo --}}
        <div id="notification-items-container" class="max-h-[380px] overflow-y-auto divide-y divide-gray-100">
            @forelse ($adminLatestNotifications ?? [] as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $iconType = $notification->data['icon_type'] ?? 'info';
                    $targetUrl = route('admin.notifications.read', $notification->id);
                @endphp
                <div class="notification-item group relative flex items-start gap-2.5 p-3.5 transition-colors {{ $isUnread ? 'bg-primary-50/35 hover:bg-primary-50/60' : 'bg-white hover:bg-gray-50' }}"
                    data-id="{{ $notification->id }}"
                    data-unread="{{ $isUnread ? '1' : '0' }}">
                    
                    {{-- Link nội dung chuyển sang trang chi tiết --}}
                    <a href="{{ $targetUrl }}" class="flex items-start gap-3 flex-1 min-w-0">
                        {{-- Icon phân loại thông báo --}}
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl 
                                @if ($iconType === 'success') bg-emerald-100 text-emerald-600
                                @elseif ($iconType === 'warning') bg-amber-100 text-amber-600
                                @elseif ($iconType === 'danger') bg-rose-100 text-rose-600
                                @else bg-blue-100 text-blue-600 @endif">

                            @if ($iconType === 'success')
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($iconType === 'warning')
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($iconType === 'danger')
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @else
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>

                        {{-- Nội dung thông báo --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold text-gray-900 truncate">
                                    {{ $notification->data['title'] ?? 'Thông báo đơn hàng' }}
                                </p>
                                @if ($isUnread)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-primary-600" title="Chưa đọc"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <span class="mt-1 block text-[11px] font-medium text-gray-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>

                    {{-- Nút xóa thông báo nhanh --}}
                    <button type="button"
                        class="btn-delete-notif flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 opacity-60 sm:opacity-0 group-hover:opacity-100 focus:opacity-100 transition-all self-center"
                        title="Xóa thông báo"
                        data-delete-url="{{ route('admin.notifications.destroy', $notification->id) }}"
                        data-is-unread="{{ $isUnread ? '1' : '0' }}">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @empty
                {{-- Sẽ hiển thị qua empty state bên dưới nếu không có data ban đầu --}}
            @endforelse

            {{-- Empty state: Khi không có thông báo nào ở tab Tất cả --}}
            <div id="empty-state-all" class="hidden p-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 mb-3">
                    <x-icons.notification class="h-6 w-6" />
                </div>
                <p class="text-xs font-semibold text-gray-700">Chưa có thông báo nào</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Bạn sẽ nhận được thông báo khi có đơn hàng mới hoặc cập nhật.</p>
            </div>

            {{-- Empty state: Khi không có thông báo chưa đọc nào ở tab Chưa đọc --}}
            <div id="empty-state-unread" class="hidden p-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-700">Không có thông báo chưa đọc</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Tuyệt vời! Bạn đã xem hết tất cả thông báo.</p>
            </div>
        </div>

        {{-- Footer của Dropdown: Nút "Xem thêm" --}}
        <div id="load-more-container" class="border-t border-gray-100 bg-gray-50/70 p-2 text-center">
            <button type="button" id="btn-load-more"
                class="w-full flex items-center justify-center gap-1.5 rounded-xl py-2 text-xs font-semibold text-primary-600 hover:bg-primary-50/80 hover:text-primary-700 transition-colors">
                <span>Xem thêm</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('admin-notification-btn');
        const menu = document.getElementById('admin-notification-menu');
        const container = document.getElementById('admin-notification-container');
        const itemsContainer = document.getElementById('notification-items-container');
        const tabAllBtn = document.getElementById('tab-all-btn');
        const tabUnreadBtn = document.getElementById('tab-unread-btn');
        const emptyStateAll = document.getElementById('empty-state-all');
        const emptyStateUnread = document.getElementById('empty-state-unread');
        const loadMoreContainer = document.getElementById('load-more-container');
        const btnLoadMore = document.getElementById('btn-load-more');

        if (!btn || !menu || !container || !itemsContainer) return;

        // Quản lý trạng thái tab và số lượng hiển thị
        let activeTab = 'all'; // 'all' hoặc 'unread'
        const STEP_COUNT = 5;
        let visibleCount = STEP_COUNT;

        function getItems() {
            return Array.from(document.querySelectorAll('.notification-item'));
        }

        function updateNotificationView() {
            const allItems = getItems();
            const matchingItems = allItems.filter(item => {
                if (activeTab === 'unread') {
                    return item.getAttribute('data-unread') === '1';
                }
                return true;
            });

            // Ẩn tất cả item không khớp tab
            allItems.forEach(item => {
                if (!matchingItems.includes(item)) {
                    item.style.display = 'none';
                }
            });

            // Hiển thị tối đa visibleCount items khớp tab
            matchingItems.forEach((item, index) => {
                if (index < visibleCount) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });

            // Xử lý Empty State
            if (matchingItems.length === 0) {
                if (activeTab === 'unread') {
                    if (emptyStateUnread) emptyStateUnread.classList.remove('hidden');
                    if (emptyStateAll) emptyStateAll.classList.add('hidden');
                } else {
                    if (emptyStateAll) emptyStateAll.classList.remove('hidden');
                    if (emptyStateUnread) emptyStateUnread.classList.add('hidden');
                }
                if (loadMoreContainer) loadMoreContainer.classList.add('hidden');
            } else {
                if (emptyStateAll) emptyStateAll.classList.add('hidden');
                if (emptyStateUnread) emptyStateUnread.classList.add('hidden');

                // Xử lý nút Xem thêm
                if (matchingItems.length > visibleCount) {
                    if (loadMoreContainer) {
                        loadMoreContainer.classList.remove('hidden');
                        const remaining = matchingItems.length - visibleCount;
                        btnLoadMore.innerHTML = `<span>Xem thêm (${remaining})</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>`;
                    }
                } else {
                    if (loadMoreContainer) loadMoreContainer.classList.add('hidden');
                }
            }
        }

        // Chuyển sang tab Tất cả
        if (tabAllBtn) {
            tabAllBtn.addEventListener('click', function () {
                activeTab = 'all';
                visibleCount = STEP_COUNT;

                tabAllBtn.className = 'tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg bg-white text-gray-900 shadow-sm transition-all';
                tabUnreadBtn.className = 'tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-gray-600 hover:text-gray-900 transition-all';

                updateNotificationView();
            });
        }

        // Chuyển sang tab Chưa đọc
        if (tabUnreadBtn) {
            tabUnreadBtn.addEventListener('click', function () {
                activeTab = 'unread';
                visibleCount = STEP_COUNT;

                tabUnreadBtn.className = 'tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg bg-white text-gray-900 shadow-sm transition-all';
                tabAllBtn.className = 'tab-btn flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-gray-600 hover:text-gray-900 transition-all';

                updateNotificationView();
            });
        }

        // Bấm nút Xem thêm
        if (btnLoadMore) {
            btnLoadMore.addEventListener('click', function () {
                visibleCount += STEP_COUNT;
                updateNotificationView();
            });
        }

        // Xử lý sự kiện xóa từng thông báo (gọi API DELETE)
        function bindDeleteButtons() {
            document.querySelectorAll('.btn-delete-notif').forEach(btnEl => {
                // Tránh gắn nhiều lần listener
                if (btnEl.dataset.bound === '1') return;
                btnEl.dataset.bound = '1';

                btnEl.addEventListener('click', async function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const deleteUrl = this.getAttribute('data-delete-url');
                    const notifItem = this.closest('.notification-item');
                    if (!deleteUrl || !notifItem) return;

                    this.disabled = true;

                    try {
                        const response = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();

                            // Hiệu ứng mờ dần và trượt nhẹ
                            notifItem.style.transition = 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)';
                            notifItem.style.opacity = '0';
                            notifItem.style.transform = 'scale(0.95)';

                            setTimeout(() => {
                                notifItem.remove();

                                const unreadCount = data.unread_count ?? 0;
                                const totalCount = data.total_count ?? 0;

                                // Cập nhật badge trên chuông
                                const badge = document.getElementById('notification-badge');
                                if (badge) {
                                    if (unreadCount > 0) {
                                        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                                    } else {
                                        badge.remove();
                                    }
                                }

                                // Cập nhật header badge
                                const headerBadge = document.getElementById('header-unread-badge');
                                if (headerBadge) {
                                    if (unreadCount > 0) {
                                        headerBadge.textContent = `${unreadCount} mới`;
                                    } else {
                                        headerBadge.remove();
                                    }
                                }

                                // Cập nhật tab counts
                                const tabAllCount = document.getElementById('tab-all-count');
                                if (tabAllCount) tabAllCount.textContent = totalCount;

                                const tabUnreadCount = document.getElementById('tab-unread-count');
                                if (tabUnreadCount) {
                                    if (unreadCount > 0) {
                                        tabUnreadCount.textContent = unreadCount;
                                    } else {
                                        tabUnreadCount.remove();
                                    }
                                }

                                updateNotificationView();
                            }, 250);
                        } else {
                            this.disabled = false;
                        }
                    } catch (err) {
                        console.error('Lỗi khi xóa thông báo:', err);
                        this.disabled = false;
                    }
                });
            });
        }

        // Mở / Đóng Menu
        function toggleMenu() {
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
                updateNotificationView();
            } else {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        }

        function closeMenu() {
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleMenu();
        });

        // Đóng khi click ra ngoài
        document.addEventListener('click', function (e) {
            if (!container.contains(e.target)) {
                closeMenu();
            }
        });

        // Đóng khi nhấn phím ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // Khởi tạo hiển thị lần đầu và gắn nút xóa
        bindDeleteButtons();
        updateNotificationView();

        // =========================================================================
        // TỰ ĐỘNG CẬP NHẬT THÔNG BÁO & BẮN TOAST (POLLING 12 GIÂY)
        // =========================================================================
        const POLLING_INTERVAL_MS = 12000;
        const dropdownDataUrl = '{{ route('admin.notifications.dropdown-data') }}';

        // Tập hợp các ID thông báo đã hiển thị ban đầu
        const knownNotificationIds = new Set(
            getItems().map(item => item.getAttribute('data-id')).filter(Boolean)
        );

        // Tạo âm thanh chuông "Ting" tinh tế bằng Web Audio API
        function playNotificationSound() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(1046.5, ctx.currentTime); // C6
                osc.frequency.exponentialRampToValueAtTime(1318.5, ctx.currentTime + 0.08); // E6

                gain.gain.setValueAtTime(0.18, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (err) {
                // Trình duyệt có thể hạn chế autoplay nếu chưa tương tác
            }
        }

        // Tạo thẻ HTML cho item thông báo mới
        function createNotificationItemElement(notif) {
            const div = document.createElement('div');
            const isUnread = !notif.read;
            div.className = `notification-item group relative flex items-start gap-2.5 p-3.5 transition-colors ${isUnread ? 'bg-primary-50/35 hover:bg-primary-50/60' : 'bg-white hover:bg-gray-50'}`;
            div.setAttribute('data-id', notif.id);
            div.setAttribute('data-unread', isUnread ? '1' : '0');

            let iconBg = 'bg-blue-100 text-blue-600';
            let iconSvg = `<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

            if (notif.icon_type === 'success') {
                iconBg = 'bg-emerald-100 text-emerald-600';
                iconSvg = `<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            } else if (notif.icon_type === 'warning') {
                iconBg = 'bg-amber-100 text-amber-600';
                iconSvg = `<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            } else if (notif.icon_type === 'danger') {
                iconBg = 'bg-rose-100 text-rose-600';
                iconSvg = `<svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            }

            div.innerHTML = `
                <a href="${notif.url}" class="flex items-start gap-3 flex-1 min-w-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl ${iconBg}">
                        ${iconSvg}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold text-gray-900 truncate">
                                ${notif.title || 'Thông báo mới'}
                            </p>
                            ${isUnread ? '<span class="h-2 w-2 shrink-0 rounded-full bg-primary-600" title="Chưa đọc"></span>' : ''}
                        </div>
                        <p class="mt-0.5 text-xs text-gray-600 line-clamp-2 leading-relaxed">
                            ${notif.message || ''}
                        </p>
                        <span class="mt-1 block text-[11px] font-medium text-gray-400">
                            ${notif.created_at_human || 'Vừa xong'}
                        </span>
                    </div>
                </a>
                <button type="button"
                    class="btn-delete-notif flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 opacity-60 sm:opacity-0 group-hover:opacity-100 focus:opacity-100 transition-all self-center"
                    title="Xóa thông báo"
                    data-delete-url="/admin/notifications/${notif.id}"
                    data-is-unread="${isUnread ? '1' : '0'}">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;

            return div;
        }

        // Cập nhật số đếm badge chuông và các tab
        function updateBadges(unreadCount, totalCount) {
            // Badge đỏ trên nút chuông
            let badge = document.getElementById('notification-badge');
            if (unreadCount > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.id = 'notification-badge';
                    badge.className = 'absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-white animate-pulse';
                    btn.appendChild(badge);
                }
                badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            } else if (badge) {
                badge.remove();
            }

            // Badge ở header dropdown
            let headerBadge = document.getElementById('header-unread-badge');
            if (unreadCount > 0) {
                if (!headerBadge) {
                    const titleContainer = menu.querySelector('h3.text-sm')?.parentElement;
                    if (titleContainer) {
                        headerBadge = document.createElement('span');
                        headerBadge.id = 'header-unread-badge';
                        headerBadge.className = 'rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600 border border-red-200';
                        titleContainer.appendChild(headerBadge);
                    }
                }
                if (headerBadge) headerBadge.textContent = `${unreadCount} mới`;
            } else if (headerBadge) {
                headerBadge.remove();
            }

            // Tab counts
            const tabAllCount = document.getElementById('tab-all-count');
            if (tabAllCount) tabAllCount.textContent = totalCount;

            const tabUnreadCount = document.getElementById('tab-unread-count');
            if (tabUnreadCount) {
                if (unreadCount > 0) {
                    tabUnreadCount.textContent = unreadCount;
                } else {
                    tabUnreadCount.remove();
                }
            } else if (unreadCount > 0 && tabUnreadBtn) {
                const countBadge = document.createElement('span');
                countBadge.id = 'tab-unread-count';
                countBadge.className = 'rounded-full bg-red-500 px-1.5 py-0.2 text-[10px] font-bold text-white';
                countBadge.textContent = unreadCount;
                tabUnreadBtn.appendChild(countBadge);
            }
        }

        // Hàm kiểm tra thông báo mới
        async function fetchNewNotifications() {
            try {
                const response = await fetch(dropdownDataUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;
                const data = await response.json();

                const unreadCount = data.unread_count ?? 0;
                const totalCount = data.total_count ?? (data.notifications?.length || 0);
                const notifications = data.notifications || [];

                // Lọc ra các thông báo chưa từng có trên giao diện
                const newItems = notifications.filter(n => !knownNotificationIds.has(String(n.id)));

                if (newItems.length > 0) {
                    let hasPlayedSound = false;

                    // Đảo ngược mảng để chèn các item mới nhất lên đầu cùng
                    newItems.slice().reverse().forEach(notif => {
                        knownNotificationIds.add(String(notif.id));

                        // Bắn Toast thông báo góc màn hình (click vào chuyển tới đơn hàng)
                        if (typeof window.showAppToast === 'function') {
                            window.showAppToast('success', notif.title, notif.message, notif.url);
                        }

                        // Chèn vào đầu danh sách dropdown
                        const notifEl = createNotificationItemElement(notif);
                        if (itemsContainer) {
                            itemsContainer.prepend(notifEl);
                        }

                        if (!hasPlayedSound) {
                            playNotificationSound();
                            hasPlayedSound = true;
                        }
                    });

                    // Gắn lại sự kiện nút xóa và cập nhật hiển thị theo tab
                    bindDeleteButtons();
                    updateNotificationView();
                }

                // Cập nhật số đếm badge
                updateBadges(unreadCount, totalCount);

            } catch (err) {
                console.debug('Polling notification error:', err);
            }
        }

        // Khởi động vòng lặp kiểm tra định kỳ mỗi 12 giây
        setInterval(fetchNewNotifications, POLLING_INTERVAL_MS);
    });
</script>