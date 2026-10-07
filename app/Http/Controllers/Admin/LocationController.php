<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount('events')
            ->latest()
            ->paginate(10);

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        Location::create($validated);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Đã tạo địa điểm thành công.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        $location->update($validated);

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Đã cập nhật địa điểm thành công.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->events()->exists()) {
            return back()->withErrors([
                'location' => 'Không thể xóa địa điểm đang được sử dụng bởi event.',
            ]);
        }

        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Đã xóa địa điểm thành công.');
    }
}
