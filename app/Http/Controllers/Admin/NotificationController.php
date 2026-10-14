<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Danh sách tất cả thông báo của Admin
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all'); // 'all' hoặc 'unread'

        $query = $filter === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query->latest()->paginate(15)->withQueryString();

        $metrics = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
        ];
        $metrics['read'] = max(0, $metrics['total'] - $metrics['unread']);

        return view('admin.notifications.index', compact('notifications', 'metrics', 'filter'));
    }

    /**
     * Đánh dấu 1 thông báo đã đọc và chuyển hướng đến trang chi tiết
     */
    public function readAndRedirect(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Ưu tiên điều hướng chính xác theo mã đơn hàng order_code
        $orderCode = $notification->data['order_code'] ?? null;
        if ($orderCode) {
            return redirect()->route('admin.orders.show', $orderCode);
        }

        $orderId = $notification->data['order_id'] ?? null;
        if ($orderId) {
            $order = \App\Models\Order::find($orderId);
            if ($order && $order->order_code) {
                return redirect()->route('admin.orders.show', $order->order_code);
            }
        }

        $url = $notification->data['url'] ?? route('admin.dashboard');

        return redirect($url);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc (thông qua Ajax hoặc Form)
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    /**
     * Đánh dấu toàn bộ thông báo là đã đọc
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    /**
     * Xóa 1 thông báo
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        auth()->user()->notifications()->findOrFail($id)->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotifications()->count(),
                'total_count' => auth()->user()->notifications()->count(),
            ]);
        }

        return back()->with('success', 'Đã xóa thông báo thành công.');
    }

    /**
     * API lấy dữ liệu thông báo mới nhất cho chuông trên topbar (nếu cần poll định kỳ)
     */
    public function dropdownData(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'total_count' => 0, 'notifications' => []]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();
        $notifications = $user->notifications()->latest()->take(10)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'read' => !is_null($item->read_at),
                'title' => $item->data['title'] ?? 'Thông báo mới',
                'message' => $item->data['message'] ?? '',
                'url' => route('admin.notifications.read', $item->id),
                'icon_type' => $item->data['icon_type'] ?? 'info',
                'created_at_human' => $item->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
            'notifications' => $notifications,
        ]);
    }
}
