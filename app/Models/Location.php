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

    /**
     * Lấy tên Thành phố / Tỉnh bằng cách tách phần sau dấu phẩy cuối cùng từ phải sang
     */
    public function getCityAttribute(): string
    {
        if (empty($this->address)) {
            return 'Khác';
        }

        // Tách chuỗi theo dấu phẩy và lấy phần tử cuối cùng từ phải sang
        $parts = explode(',', $this->address);
        $rawCity = trim(end($parts));

        // Chuẩn hóa tên các thành phố trọng điểm
        if (preg_match('/(Hồ Chí Minh|TP\.?\s*HCM|Sài Gòn)/ui', $rawCity)) {
            return 'TP.HCM';
        }
        if (preg_match('/Hà Nội/ui', $rawCity)) {
            return 'Hà Nội';
        }
        if (preg_match('/Đà Nẵng/ui', $rawCity)) {
            return 'Đà Nẵng';
        }

        return $rawCity ?: 'Khác';
    }
}