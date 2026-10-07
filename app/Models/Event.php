<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}