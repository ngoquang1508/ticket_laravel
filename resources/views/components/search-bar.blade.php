@props(['id' => 'search-' . uniqid()])

<form action="{{ route('events.index') }}" method="GET" autocomplete="off" class="w-full relative search-form-container"
    data-search-id="{{ $id }}">
    <div class="relative">
        {{-- Search icon --}}
        <x-icons.search class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-white/60" />

        {{-- Search input --}}
        <input id="{{ $id }}-input" type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm sự kiện..."
            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
            class="w-full rounded-full border border-white/20 bg-white/10 py-2.5 pl-10 pr-10 text-sm text-white placeholder:text-white/60 outline-none transition focus:border-white/40 focus:bg-white/15 focus:ring-2 focus:ring-white/20">

        {{-- Loading Spinner (ẩn mặc định, xoay khi đang fetch dữ liệu) --}}
        <div id="{{ $id }}-spinner" class="hidden absolute right-8 top-1/2 -translate-y-1/2">
            <svg class="h-4 w-4 animate-spin text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>

        {{-- Clear button --}}
        <button id="{{ $id }}-clear" type="button"
            class="{{ request('q') ? '' : 'hidden' }} absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-white/60 transition hover:bg-white/10 hover:text-white"
            aria-label="Xóa tìm kiếm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Dropdown kết quả gợi ý trực tiếp (Live Search Dropdown) --}}
    <div id="{{ $id }}-dropdown"
        class="hidden absolute left-0 right-0 top-full mt-2 z-50 rounded-2xl bg-[#141815]/95 border border-white/15 shadow-[0_16px_36px_rgba(0,0,0,0.85)] backdrop-blur-xl overflow-hidden transition-all duration-200">

        {{-- Container danh sách các sự kiện --}}
        <div id="{{ $id }}-results"
            class="divide-y divide-white/5 max-h-[380px] overflow-y-auto no-scrollbar [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
            {{-- Kết quả tìm kiếm sẽ được chèn vào đây bằng JS --}}
        </div>

        {{-- Dòng footer: Xem tất cả kết quả --}}
        <div id="{{ $id }}-footer" class="border-t border-white/10 bg-black/40 p-2.5 text-center">
            <a id="{{ $id }}-view-all" href="{{ route('events.index') }}"
                class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors flex items-center justify-center gap-1.5 py-1">
                <span>Xem tất cả kết quả</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</form>

{{-- Script điều khiển Live Search --}}
@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchForms = document.querySelectorAll('.search-form-container');

            searchForms.forEach(form => {
                const searchId = form.getAttribute('data-search-id');
                const input = document.getElementById(`${searchId}-input`);
                const clearBtn = document.getElementById(`${searchId}-clear`);
                const spinner = document.getElementById(`${searchId}-spinner`);
                const dropdown = document.getElementById(`${searchId}-dropdown`);
                const resultsBox = document.getElementById(`${searchId}-results`);
                const viewAllLink = document.getElementById(`${searchId}-view-all`);

                if (!input || !dropdown || !resultsBox) return;

                let debounceTimer = null;
                let currentSearchTerm = '';
                const cache = {};

                // 1. Lắng nghe người dùng gõ vào ô tìm kiếm
                input.addEventListener('input', function () {
                    const query = this.value.trim();

                    // Ẩn/Hiện nút xóa [X]
                    if (clearBtn) {
                        if (this.value.length > 0) {
                            clearBtn.classList.remove('hidden');
                        } else {
                            clearBtn.classList.add('hidden');
                        }
                    }

                    if (query.length < 1) {
                        dropdown.classList.add('hidden');
                        resultsBox.innerHTML = '';
                        currentSearchTerm = '';
                        return;
                    }

                    currentSearchTerm = query;

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetchSuggestions(query);
                    }, 280);
                });

                // 2. Fetch dữ liệu gợi ý từ server
                function fetchSuggestions(query) {
                    if (cache[query]) {
                        renderResults(cache[query], query);
                        return;
                    }

                    if (spinner) spinner.classList.remove('hidden');

                    const suggestionsUrl = @json(route('events.search.suggestions')) + '?q=' + encodeURIComponent(query);

                    fetch(suggestionsUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (spinner) spinner.classList.add('hidden');

                            // Chỉ render nếu từ khóa vẫn khớp với lần gõ mới nhất
                            if (query === currentSearchTerm) {
                                cache[query] = data;
                                renderResults(data, query);
                            }
                        })
                        .catch(err => {
                            if (spinner) spinner.classList.add('hidden');
                            console.error('Lỗi tìm kiếm:', err);
                        });
                }

                // 3. Render HTML danh sách kết quả vào Dropdown
                function renderResults(data, query) {
                    const events = data.events || [];
                    const total = data.total || 0;

                    resultsBox.innerHTML = '';

                    if (events.length === 0) {
                        resultsBox.innerHTML = `
                            <div class="p-4 text-center text-xs text-gray-400">
                                Không tìm thấy sự kiện nào cho "<span class="text-white font-medium">${escapeHtml(query)}</span>"
                            </div>
                        `;
                        if (viewAllLink) {
                            viewAllLink.parentElement.classList.add('hidden');
                        }
                    } else {
                        events.forEach(event => {
                            const item = document.createElement('a');
                            item.href = event.url;
                            item.className = 'flex items-center gap-3 p-3 hover:bg-white/10 transition-colors group text-left';

                            const imgHtml = event.image
                                ? `<img src="${event.image}" alt="${escapeHtml(event.name)}" class="h-11 w-16 shrink-0 object-cover rounded-lg bg-gray-800 border border-white/10">`
                                : `<div class="h-11 w-16 shrink-0 bg-gray-800 rounded-lg flex items-center justify-center text-[10px] text-gray-400 border border-white/10">Vé</div>`;

                            item.innerHTML = `
                                ${imgHtml}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-white group-hover:text-emerald-400 transition-colors truncate">
                                        ${escapeHtml(event.name)}
                                    </h4>
                                    <div class="mt-0.5 flex items-center gap-2 text-xs text-gray-400">
                                        <span>${event.city}</span>
                                        <span>•</span>
                                        <span>${event.date}</span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-xs font-bold text-emerald-400">${event.price}</span>
                                </div>
                            `;
                            resultsBox.appendChild(item);
                        });

                        // Cập nhật link xem tất cả
                        if (viewAllLink) {
                            viewAllLink.parentElement.classList.remove('hidden');
                            viewAllLink.href = data.search_url || (@json(route('events.index')) + '?q=' + encodeURIComponent(query));
                            viewAllLink.innerHTML = `
                                <span>Xem tất cả ${total} kết quả cho "<b class="text-white">${escapeHtml(query)}</b>"</span>
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            `;
                        }
                    }

                    dropdown.classList.remove('hidden');
                }

                // 4. Thoát HTML an toàn
                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return String(text).replace(/[&<>"']/g, m => map[m]);
                }

                // 5. Nút xóa [X]
                if (clearBtn) {
                    clearBtn.addEventListener('click', function () {
                        input.value = '';
                        this.classList.add('hidden');
                        dropdown.classList.add('hidden');
                        resultsBox.innerHTML = '';
                        currentSearchTerm = '';
                        input.focus();
                    });
                }

                // 6. Mở lại dropdown khi focus vào input nếu đã có kết quả
                input.addEventListener('focus', function () {
                    if (resultsBox.children.length > 0 && this.value.trim().length > 0) {
                        dropdown.classList.remove('hidden');
                    }
                });

                // 7. Nhấn ESC hoặc click ra ngoài để đóng dropdown
                document.addEventListener('click', function (e) {
                    if (!form.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endonce