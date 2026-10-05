<form action="#" method="GET" autocomplete="off">
    <div class="relative">

        {{-- Search icon --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5
                   -translate-y-1/2 text-white/60"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0
                   8 8 0 0 1 16 0Z"
            />
        </svg>

        {{-- Search input --}}
        <input
            id="{{ $id }}-input"
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Tìm kiếm sự kiện..."
            autocomplete="off"
            autocorrect="off"
            autocapitalize="off"
            spellcheck="false"
            class="w-full rounded-full border border-white/20
                   bg-white/10 py-2.5 pl-10 pr-10 text-sm text-white
                   placeholder:text-white/60 outline-none transition
                   focus:border-white/40 focus:bg-white/15
                   focus:ring-2 focus:ring-white/20"
        >

        {{-- Clear button --}}
        <button
            id="{{ $id }}-clear"
            type="button"
            class="{{ request('q') ? '' : 'hidden' }}
                   absolute right-3 top-1/2 -translate-y-1/2
                   rounded-full p-1 text-black/60
                   transition hover:bg-white/10 hover:text-black"
            aria-label="Xóa tìm kiếm"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>

    </div>
</form>