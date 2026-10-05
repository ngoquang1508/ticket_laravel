<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'address',
        'description',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}