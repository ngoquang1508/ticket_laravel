<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Các trạng thái đơn hàng hợp lệ trong hệ thống
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Phương thức thanh toán
    const PAYMENT_METHOD_VNPAY = 'vnpay';
    const PAYMENT_METHOD_DIRECT = 'direct';
    const PAYMENT_METHOD_FREE = 'free';

    // Trạng thái thanh toán
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Helper kiểm tra trạng thái đơn hàng
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    /**
     * Nhãn hiển thị phương thức thanh toán
     */
    public function getPaymentMethodNameAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_METHOD_VNPAY => 'VNPay',
            self::PAYMENT_METHOD_DIRECT => 'Trực tiếp / Chuyển khoản',
            self::PAYMENT_METHOD_FREE => 'Miễn phí (0đ)',
            default => 'Chưa chọn',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function getRouteKeyName()
    {
        return 'order_code';
    }

    /**
     * Scope lọc đơn hàng theo người dùng
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope lọc đơn hàng theo trạng thái nghiệp vụ (bao gồm logic hết hạn và hủy)
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }

        return match ($status) {
            'pending' => $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]),
            'completed' => $query->where('status', self::STATUS_COMPLETED)
                ->whereHas('event', function ($q) {
                    $q->where('ends_at', '>=', now());
                }),
            'expired' => $query->where(function ($q) {
                $q->where('status', self::STATUS_EXPIRED)
                    ->orWhere(function ($sub) {
                        $sub->where('status', self::STATUS_CANCELLED)
                            ->where('cancel_reason', 'like', '%Hết hạn%');
                    })
                    ->orWhere(function ($sub) {
                        $sub->where('status', self::STATUS_COMPLETED)
                            ->whereHas('event', function ($ev) {
                                $ev->where('ends_at', '<', now());
                            });
                    });
            }),
            'cancelled' => $query->where('status', self::STATUS_CANCELLED)
                ->where(function ($q) {
                    $q->whereNull('cancel_reason')
                        ->orWhere('cancel_reason', 'not like', '%Hết hạn%');
                }),
            default => $query->where('status', $status),
        };
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
