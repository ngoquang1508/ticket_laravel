<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\Ticket;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Hồ sơ tài khoản (Profile) của người dùng
     */
    public function index()
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
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'name.string' => 'Họ và tên không hợp lệ.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.regex' => 'Số điện thoại chứa ký tự không hợp lệ.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
        ]);

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
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại của bạn.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()
            ->with('status', 'password-updated')
            ->with('success', 'Mật khẩu của bạn đã được thay đổi thành công!')
            ->with('active_tab', 'security');
    }
}
