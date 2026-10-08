<nav class="bg-black text-white">
    <div class="mx-auto max-w-7xl px-8">
        <div class="flex items-center gap-7 overflow-x-auto whitespace-nowrap
                   [&::-webkit-scrollbar]:hidden
                   [-ms-overflow-style:none]
                   [scrollbar-width:none]">
            <a href="{{ route('events.index') }}" class="shrink-0 py-4 text-sm font-medium transition {{ !isset($currentCategory) && request()->routeIs('events.index') ? 'text-green-400' : 'hover:text-green-400' }}">
                Tất cả sự kiện
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('events.index', $category->slug) }}" class="shrink-0 py-4 text-sm font-medium transition
                                   {{ isset($currentCategory) && $currentCategory->id === $category->id ? 'text-green-400' : 'hover:text-green-400' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</nav>