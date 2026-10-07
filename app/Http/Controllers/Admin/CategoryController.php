<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('events')
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã tạo danh mục thành công.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
            'slug' => ['nullable', 'string', 'max:100', 'unique:categories,slug,'.$category->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['name'], $category->id);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục thành công.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->events()->exists()) {
            return back()->withErrors([
                'category' => 'Không thể xóa danh mục đang được sử dụng bởi event.',
            ]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục thành công.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $counter = 2;

        while (Category::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
