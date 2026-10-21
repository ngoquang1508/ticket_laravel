{{-- Bảng loại vé đã đặt --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xs">
    <div class="border-b border-gray-200 bg-gray-50/80 px-6 py-4">
        <h2 class="font-bold text-gray-900">Chi tiết sản phẩm đặt mua</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-100 bg-white text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3.5">Loại vé</th>
                    <th class="px-6 py-3.5 text-right">Đơn giá</th>
                    <th class="px-6 py-3.5 text-center">Số lượng</th>
                    <th class="px-6 py-3.5 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->ticketType?->name ?: 'Loại vé không xác định' }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600 font-mono">
                            {{ number_format($item->price, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 text-center font-semibold text-gray-900">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 font-mono">
                            {{ number_format($item->total, 0, ',', '.') }}đ
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-gray-200 bg-gray-50/50">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">Tổng cộng:</td>
                    <td class="px-6 py-4 text-right text-base font-semibold text-primary-600 font-mono">
                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
