<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Hồ sơ tài khoản (Profile) của người dùng
     */
    public function index(): View
    {
        $user = auth()->user();

        // Lấy danh sách các đơn hàng có trạng thái completed kèm vé của người dùng,
        // sắp xếp theo đơn hàng có sự kiện sắp diễn ra lên đầu (starts_at >= now() ASC),
        // sau đó đến các sự kiện đã diễn ra (starts_at DESC).
        $orders = Order::query()
            ->select('orders.*')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->with([
                'event.location',
                'items.ticketType',
                'tickets.orderItem.ticketType',
                'tickets.seat'
            ])
            ->where('orders.user_id', $user->id)
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->orderByRaw('CASE WHEN events.starts_at >= ? THEN 0 ELSE 1 END ASC', [now()])
            ->orderByRaw('CASE WHEN events.starts_at >= ? THEN events.starts_at END ASC', [now()])
            ->orderBy('events.starts_at', 'desc')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(6);

        // Thống kê nhanh dữ liệu vé của người dùng
        $totalOrders = Order::where('user_id', $user->id)->count();

        $totalTickets = Ticket::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('status', Order::STATUS_COMPLETED);
        })->count();

        $upcomingTicketsCount = Ticket::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereHas('event', function ($ev) {
                    $ev->where('ends_at', '>=', now());
                });
        })->where('is_used', false)->whereNull('checked_in_at')->count();

        return view('user.profile.index', compact(
            'user',
            'orders',
            'totalOrders',
            'totalTickets',
            'upcomingTicketsCount'
        ));
    }

    /**
     * Cập nhật thông tin cá nhân (chỉ cho phép cập nhật name và phone, không cho phép sửa email)
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()
            ->with('status', 'profile-updated')
            ->with('success', 'Cập nhật thông tin cá nhân thành công!')
            ->with('active_tab', 'personal-info');
    }

    /**
     * Cập nhật mật khẩu tài khoản
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()
            ->with('status', 'password-updated')
            ->with('success', 'Mật khẩu của bạn đã được thay đổi thành công!')
            ->with('active_tab', 'security');
    }
}
