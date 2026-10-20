/**
 * Module điều khiển Bộ lọc sự kiện phong cách Ticketbox
 * Quản lý: Lịch 2 tháng song song, chọn ngày/dải ngày, các nút chọn nhanh Preset,
 * bộ lọc vị trí, vé miễn phí, thể loại và điều hướng URL chuẩn.
 */

export function initTicketboxFilter() {
    const filterWrapper = document.getElementById('ticketbox-filter-wrapper');
    if (!filterWrapper) return;

    // Lấy các phần tử DOM
    const btnToggleDate = document.getElementById('btn-toggle-date-filter');
    const popoverDate = document.getElementById('popover-date-filter');
    const btnToggleMain = document.getElementById('btn-toggle-main-filter');
    const popoverMain = document.getElementById('popover-main-filter');
    const closeButtons = document.querySelectorAll('.btn-close-popover');

    // Lấy dữ liệu state ban đầu từ HTML data attributes
    const eventsBaseUrl = filterWrapper.dataset.eventsUrl || window.location.pathname;
    let selectedPreset = filterWrapper.dataset.datePreset || 'all';
    let startDate = filterWrapper.dataset.startDate || '';
    let endDate = filterWrapper.dataset.endDate || '';
    
    let selectedCategories = [];
    try {
        selectedCategories = JSON.parse(filterWrapper.dataset.selectedCategories || '[]');
    } catch (e) {
        selectedCategories = [];
    }

    // ================= 1. ĐIỀU KHIỂN BẬT / TẮT POPOVERS =================
    if (btnToggleDate && popoverDate) {
        btnToggleDate.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (popoverMain) popoverMain.classList.add('hidden');
            popoverDate.classList.toggle('hidden');
        });

        popoverDate.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    if (btnToggleMain && popoverMain) {
        btnToggleMain.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (popoverDate) popoverDate.classList.add('hidden');
            popoverMain.classList.toggle('hidden');
        });

        popoverMain.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    // Đóng khi click vào nút ✕ trên mobile
    closeButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (popoverDate) popoverDate.classList.add('hidden');
            if (popoverMain) popoverMain.classList.add('hidden');
        });
    });

    // Đóng khi click ra ngoài popover
    document.addEventListener('click', function (e) {
        if (popoverDate && !popoverDate.contains(e.target) && (!btnToggleDate || !btnToggleDate.contains(e.target))) {
            popoverDate.classList.add('hidden');
        }
        if (popoverMain && !popoverMain.contains(e.target) && (!btnToggleMain || !btnToggleMain.contains(e.target))) {
            popoverMain.classList.add('hidden');
        }
    });

    // Đóng khi nhấn phím Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (popoverDate) popoverDate.classList.add('hidden');
            if (popoverMain) popoverMain.classList.add('hidden');
        }
    });

    // ================= 2. XỬ LÝ LỊCH 2 THÁNG & PRESETS =================
    function formatDateStr(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // Tính toán khoảng ngày theo Preset và render lại lịch
    function setPresetRange(preset) {
        selectedPreset = preset;
        const now = new Date();
        now.setHours(0, 0, 0, 0);

        if (preset === 'today') {
            startDate = formatDateStr(now);
            endDate = '';
        } else if (preset === 'tomorrow') {
            const tomorrow = new Date(now);
            tomorrow.setDate(now.getDate() + 1);
            startDate = formatDateStr(tomorrow);
            endDate = '';
        } else if (preset === 'weekend') {
            const day = now.getDay(); // 0: CN, 1: T2, ..., 5: T6, 6: T7
            const sunday = new Date(now);
            const daysUntilSunday = day === 0 ? 0 : (7 - day);
            sunday.setDate(now.getDate() + daysUntilSunday);

            const friday = new Date(sunday);
            friday.setDate(sunday.getDate() - 2);

            const startDay = now > friday ? now : friday;
            startDate = formatDateStr(startDay);
            endDate = formatDateStr(sunday);
        } else if (preset === 'month') {
            const lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            startDate = formatDateStr(now);
            endDate = formatDateStr(lastDayOfMonth);
        } else {
            startDate = '';
            endDate = '';
        }

        if (startDate) {
            const p = new Date(startDate);
            calYear = p.getFullYear();
            calMonth = p.getMonth();
        }

        renderCalendars();
    }

    let currentCalendarDate = new Date();
    if (startDate) {
        const parsedStart = new Date(startDate);
        if (!isNaN(parsedStart.getTime())) {
            currentCalendarDate = parsedStart;
        }
    }

    let calYear = currentCalendarDate.getFullYear();
    let calMonth = currentCalendarDate.getMonth(); // 0-indexed

    const labelMonthLeft = document.getElementById('label-month-left');
    const labelMonthRight = document.getElementById('label-month-right');
    const gridLeft = document.getElementById('days-grid-left');
    const gridRight = document.getElementById('days-grid-right');
    const btnPrevMonth = document.getElementById('btn-prev-month');
    const btnNextMonth = document.getElementById('btn-next-month');

    function renderCalendars() {
        renderSingleCalendar(calYear, calMonth, labelMonthLeft, gridLeft);

        // Tháng tiếp theo bên phải (cho màn hình desktop)
        let nextYear = calMonth === 11 ? calYear + 1 : calYear;
        let nextMonth = calMonth === 11 ? 0 : calMonth + 1;
        renderSingleCalendar(nextYear, nextMonth, labelMonthRight, gridRight);
    }

    function renderSingleCalendar(year, month, labelEl, gridEl) {
        if (!labelEl || !gridEl) return;
        labelEl.innerText = `Tháng ${month + 1}, ${year}`;
        gridEl.innerHTML = '';

        const firstDayOfWeek = (new Date(year, month, 1).getDay() + 6) % 7; // Thứ 2 = 0, CN = 6
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Các ngày của tháng trước (màu xám)
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayNum = daysInPrevMonth - i;
            const cell = document.createElement('div');
            cell.className = 'h-9 w-full flex items-center justify-center text-gray-300 text-xs select-none';
            cell.innerText = dayNum;
            gridEl.appendChild(cell);
        }

        // Các ngày trong tháng
        for (let day = 1; day <= daysInMonth; day++) {
            const dateObj = new Date(year, month, day);
            dateObj.setHours(0, 0, 0, 0);

            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'h-9 w-full flex items-center justify-center text-xs sm:text-sm font-medium transition cursor-pointer select-none';
            cell.innerText = day;

            const isPast = dateObj < today;
            if (isPast) {
                cell.classList.add('text-gray-300', 'cursor-not-allowed');
            } else {
                cell.classList.add('text-gray-800', 'hover:bg-gray-100');
            }

            // Highlight dải ngày hoặc ngày đơn
            if (startDate && !endDate && dateStr === startDate) {
                cell.classList.remove('hover:bg-gray-100', 'text-gray-800');
                cell.classList.add('bg-[#0066ff]', 'text-white', 'font-bold', 'rounded-lg', 'shadow-sm');
            } else if (startDate && endDate) {
                if (dateStr === startDate && dateStr === endDate) {
                    cell.classList.remove('hover:bg-gray-100', 'text-gray-800');
                    cell.classList.add('bg-[#0066ff]', 'text-white', 'font-bold', 'rounded-lg', 'shadow-sm');
                } else if (dateStr === startDate) {
                    cell.classList.remove('hover:bg-gray-100', 'text-gray-800');
                    cell.classList.add('bg-[#0066ff]', 'text-white', 'font-bold', 'rounded-l-lg', 'rounded-r-none', 'shadow-sm');
                } else if (dateStr === endDate) {
                    cell.classList.remove('hover:bg-gray-100', 'text-gray-800');
                    cell.classList.add('bg-[#0066ff]', 'text-white', 'font-bold', 'rounded-r-lg', 'rounded-l-none', 'shadow-sm');
                } else if (dateStr > startDate && dateStr < endDate) {
                    cell.classList.remove('hover:bg-gray-100', 'text-gray-800');
                    cell.classList.add('bg-[#dce9fc]', 'text-blue-900', 'font-semibold', 'rounded-none');
                } else {
                    cell.classList.add('rounded-lg');
                }
            } else {
                cell.classList.add('rounded-lg');
                if (dateObj.getTime() === today.getTime()) {
                    cell.classList.add('font-bold', 'text-[#0066ff]');
                }
            }

            if (!isPast) {
                cell.addEventListener('click', function (e) {
                    e.stopPropagation();
                    handleDateCellClick(dateStr);
                });
            }

            gridEl.appendChild(cell);
        }
    }

    function handleDateCellClick(dateStr) {
        selectedPreset = 'custom';
        document.querySelectorAll('.btn-date-preset').forEach(function (b) {
            b.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/70');
            b.classList.add('border-gray-200', 'text-gray-700');
        });

        if (!startDate || (startDate && endDate)) {
            startDate = dateStr;
            endDate = '';
        } else if (startDate && !endDate) {
            if (dateStr >= startDate) {
                endDate = dateStr;
            } else {
                endDate = startDate;
                startDate = dateStr;
            }
        }
        renderCalendars();
    }

    if (btnPrevMonth) {
        btnPrevMonth.addEventListener('click', function (e) {
            e.stopPropagation();
            calMonth--;
            if (calMonth < 0) {
                calMonth = 11;
                calYear--;
            }
            renderCalendars();
        });
    }

    if (btnNextMonth) {
        btnNextMonth.addEventListener('click', function (e) {
            e.stopPropagation();
            calMonth++;
            if (calMonth > 11) {
                calMonth = 0;
                calYear++;
            }
            renderCalendars();
        });
    }

    // Xử lý các nút chọn nhanh Preset ngày
    document.querySelectorAll('.btn-date-preset').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.btn-date-preset').forEach(function (b) {
                b.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/70');
                b.classList.add('border-gray-200', 'text-gray-700');
            });
            this.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/70');
            this.classList.remove('border-gray-200', 'text-gray-700');

            const preset = this.getAttribute('data-preset');
            setPresetRange(preset);
        });
    });

    if (!startDate && selectedPreset && selectedPreset !== 'all') {
        setPresetRange(selectedPreset);
    }

    // Nút Thiết lập lại Lịch
    const btnResetDate = document.getElementById('btn-reset-date');
    if (btnResetDate) {
        btnResetDate.addEventListener('click', function (e) {
            e.stopPropagation();
            startDate = '';
            endDate = '';
            selectedPreset = 'all';
            document.querySelectorAll('.btn-date-preset').forEach(function (b) {
                b.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/70');
                b.classList.add('border-gray-200', 'text-gray-700');
            });
            const allBtn = document.querySelector('.btn-date-preset[data-preset="all"]');
            if (allBtn) {
                allBtn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/70');
                allBtn.classList.remove('border-gray-200', 'text-gray-700');
            }
            renderCalendars();

            const url = new URL(window.location.href);
            url.searchParams.delete('date_preset');
            url.searchParams.delete('start_date');
            url.searchParams.delete('end_date');
            window.location.href = url.toString();
        });
    }

    // Nút Áp dụng Lịch
    const btnApplyDate = document.getElementById('btn-apply-date');
    if (btnApplyDate) {
        btnApplyDate.addEventListener('click', function (e) {
            e.stopPropagation();
            const url = new URL(window.location.href);

            if (startDate) {
                url.searchParams.delete('date_preset');
                url.searchParams.set('start_date', startDate);
                if (endDate && endDate !== startDate) {
                    url.searchParams.set('end_date', endDate);
                } else {
                    url.searchParams.delete('end_date');
                }
            } else if (selectedPreset && selectedPreset !== 'all') {
                url.searchParams.set('date_preset', selectedPreset);
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
            } else {
                url.searchParams.delete('date_preset');
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
            }
            window.location.href = url.toString();
        });
    }

    renderCalendars();

    // ================= 3. XỬ LÝ BỘ LỌC TỔNG HỢP =================
    // Click các pill thể loại
    document.querySelectorAll('.btn-category-pill').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const slug = this.getAttribute('data-slug');
            const idx = selectedCategories.indexOf(slug);
            if (idx > -1) {
                selectedCategories.splice(idx, 1);
                this.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-600', 'font-bold', 'shadow-sm');
                this.classList.add('border-gray-200', 'text-gray-700', 'bg-gray-50/80');
            } else {
                selectedCategories.push(slug);
                this.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-600', 'font-bold', 'shadow-sm');
                this.classList.remove('border-gray-200', 'text-gray-700', 'bg-gray-50/80');
            }
        });
    });

    // Nút Thiết lập lại Bộ lọc Tổng hợp
    const btnResetMain = document.getElementById('btn-reset-main-filter');
    if (btnResetMain) {
        btnResetMain.addEventListener('click', function (e) {
            e.stopPropagation();
            const targetUrl = new URL(eventsBaseUrl, window.location.origin);
            const currentUrl = new URL(window.location.href);

            if (currentUrl.searchParams.has('q')) {
                targetUrl.searchParams.set('q', currentUrl.searchParams.get('q'));
            }

            ['date_preset', 'start_date', 'end_date'].forEach(function (param) {
                if (currentUrl.searchParams.has(param)) {
                    targetUrl.searchParams.set(param, currentUrl.searchParams.get(param));
                }
            });

            window.location.href = targetUrl.toString();
        });
    }

    // Nút Áp dụng Bộ lọc Tổng hợp
    const btnApplyMain = document.getElementById('btn-apply-main-filter');
    if (btnApplyMain) {
        btnApplyMain.addEventListener('click', function (e) {
            e.stopPropagation();
            const targetUrl = new URL(eventsBaseUrl, window.location.origin);
            const currentUrl = new URL(window.location.href);

            if (currentUrl.searchParams.has('q')) {
                targetUrl.searchParams.set('q', currentUrl.searchParams.get('q'));
            }

            ['date_preset', 'start_date', 'end_date'].forEach(function (param) {
                if (currentUrl.searchParams.has(param)) {
                    targetUrl.searchParams.set(param, currentUrl.searchParams.get(param));
                }
            });

            // 1. Vị trí
            const checkedLoc = document.querySelector('input[name="temp_location"]:checked');
            if (checkedLoc && checkedLoc.value !== 'all') {
                targetUrl.searchParams.set('location', checkedLoc.value);
            }

            // 2. Miễn phí
            const toggleFree = document.getElementById('temp-toggle-free');
            if (toggleFree && toggleFree.checked) {
                targetUrl.searchParams.set('is_free', '1');
            }

            // 3. Thể loại
            if (selectedCategories.length > 0) {
                targetUrl.searchParams.set('categories', selectedCategories.join(','));
            }

            window.location.href = targetUrl.toString();
        });
    }
}
