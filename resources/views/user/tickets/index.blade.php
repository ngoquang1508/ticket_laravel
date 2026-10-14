@extends('layouts.app')

@section('title', 'Vé của tôi')

@section('content')
    <div class="pt-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <!-- Bộ lọc trạng thái đơn hàng -->
            @include('user.tickets.partials.status-tabs')

            <!-- Danh sách đơn hàng -->
            <div class="space-y-4">
                @forelse($orders as $order)
                    {{-- Thẻ đơn hàng (kèm dropdown danh sách vé) --}}
                    @include('user.tickets.partials.order-card', ['order' => $order])

                    {{-- Modal chỉnh sửa vé và Modal thanh toán cho đơn hàng Pending --}}
                    @if($order->status === 'pending' && $order->expires_at > now())
                        @include('user.tickets.partials.edit-modal', ['order' => $order])
                        @include('booking.checkout', ['order' => $order])
                    @endif
                @empty
                    {{-- Giao diện khi chưa có vé nào --}}
                    @include('user.tickets.partials.empty-state')
                @endforelse

                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Script tương tác vé, modal và đếm ngược thời gian -->
    @include('user.tickets.partials.scripts')

    @if(session('active_checkout_order'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof window.openCheckoutModal === 'function') {
                    window.openCheckoutModal("{{ session('active_checkout_order') }}");
                }
            });
        </script>
    @endif
@endsection