<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'location_id',
        'parent_event_id',
        'name',
        'slug',
        'description',
        'image',
        'starts_at',
        'ends_at',
        'sale_mode',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Số giờ trước khi sự kiện bắt đầu sẽ ngừng chấp nhận thanh toán trực tiếp.
     */
    const DIRECT_PAYMENT_CUTOFF_HOURS = 48;

    /**
     * Thời điểm ngừng nhận thanh toán trực tiếp (48 giờ trước khi sự kiện bắt đầu).
     */
    public function getDirectPaymentCutoffAtAttribute(): ?\Carbon\Carbon
    {
        return $this->starts_at ? $this->starts_at->copy()->subHours(self::DIRECT_PAYMENT_CUTOFF_HOURS) : null;
    }

    /**
     * Kiểm tra xem sự kiện có còn cho phép thanh toán trực tiếp hay không.
     */
    public function canPayDirect(): bool
    {
        if (!$this->starts_at) {
            return false;
        }

        return now()->lt($this->direct_payment_cutoff_at);
    }

    public function scopeActiveListing($query)
    {
        return $query->with('location')
            ->withMin('ticketTypes', 'price')
            ->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now())->orderBy('starts_at', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now())->orderBy('starts_at', 'desc');
    }

    /**
     * Tìm kiếm sự kiện theo từ khóa (tên sự kiện, mô tả, địa điểm hoặc danh mục)
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('location', function ($locQuery) use ($term) {
                    $locQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%");
                })
                ->orWhereHas('categories', function ($catQuery) use ($term) {
                    $catQuery->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'event_category',
            'event_id',
            'category_id'
        );
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function seatMap(): HasOne
    {
        return $this->hasOne(SeatMap::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Các đơn hàng của sự kiện
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Các mục vé trong đơn hàng thuộc sự kiện
     */
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Order::class);
    }

    /**
     * Lấy số vé đã bán / đã đặt của sự kiện (các đơn hàng chưa bị hủy)
     */
    public function getSoldTicketsAttribute(): int
    {
        if (isset($this->attributes['booked_tickets_count'])) {
            return (int) $this->attributes['booked_tickets_count'];
        }

        return (int) $this->orderItems()
            ->whereHas('order', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->where('order_items.status', '!=', 'cancelled')
            ->sum('quantity');
    }

    /**
     * Lấy số vé đã thanh toán thành công của sự kiện
     */
    public function getPaidTicketsAttribute(): int
    {
        if (isset($this->attributes['paid_tickets_count'])) {
            return (int) $this->attributes['paid_tickets_count'];
        }

        return (int) $this->orderItems()
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->where('order_items.status', '!=', 'cancelled')
            ->sum('quantity');
    }

    /**
     * Lấy tổng số vé phát hành của sự kiện
     */
    public function getTotalTicketsAttribute(): int
    {
        // Với sự kiện ghế ngồi: tổng vé = số ghế đã thiết lập
        if ($this->sale_mode === 'assigned_seat' && ($this->seats_count ?? $this->seats()->count()) > 0) {
            return (int) ($this->seats_count ?? $this->seats()->count());
        }

        // Với sự kiện khu vực hoặc tự do: quantity trong ticket_types là số vé còn lại trong kho
        // Tổng số vé ban đầu = số vé còn lại + số vé đã được đặt/bán
        $remaining = (int) ($this->remaining_tickets_count ?? $this->ticketTypes()->sum('quantity'));

        return $remaining + $this->sold_tickets;
    }
}