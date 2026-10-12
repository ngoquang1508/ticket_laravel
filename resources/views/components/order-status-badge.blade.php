@props(['status'])

@php
    $config = match ($status) {
        'completed' => [
            'label' => 'Hoàn tất',
            'dot' => 'bg-emerald-500',
            'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        ],
        'processing' => [
            'label' => 'Đang xử lý',
            'dot' => 'bg-blue-500',
            'classes' => 'border-blue-200 bg-blue-50 text-blue-700',
        ],
        'pending' => [
            'label' => 'Chờ thanh toán',
            'dot' => 'bg-amber-500',
            'classes' => 'border-amber-200 bg-amber-50 text-amber-700',
        ],
        'cancelled' => [
            'label' => 'Đã hủy',
            'dot' => 'bg-red-500',
            'classes' => 'border-red-200 bg-red-50 text-red-700',
        ],
        'expired' => [
            'label' => 'Hết hạn',
            'dot' => 'bg-gray-400',
            'classes' => 'border-gray-200 bg-gray-100 text-gray-600',
        ],
        default => [
            'label' => ucfirst($status),
            'dot' => 'bg-gray-400',
            'classes' => 'border-gray-200 bg-gray-50 text-gray-700',
        ],
    };
@endphp

{{-- Badge trạng thái đơn hàng --}}
<span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $config['classes'] }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $config['dot'] }}"></span>
    {{ $config['label'] }}
</span>
