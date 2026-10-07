<nav class="bg-black text-white">
    <div class="mx-auto max-w-7xl px-8">
        <div class="flex items-center gap-7 overflow-x-auto whitespace-nowrap
                   [&::-webkit-scrollbar]:hidden
                   [-ms-overflow-style:none]
                   [scrollbar-width:none]">
            @foreach ($categories as $category)
                <a href="#" class="shrink-0 py-4 text-sm font-medium transition
                                   hover:text-green-400">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</nav>