<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registered_user_id', 'plate_number', 'vehicle_brand',
        'vehicle_model', 'vehicle_color', 'vehicle_type', 'registration_date'
    ];

    public function registeredUser()
    {
        return $this->belongsTo(RegisteredUser::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function activeQrCode()
    {
        return $this->hasOne(QrCode::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Computed status — a vehicle is "active" if it has a currently valid QR code.
     * This replaces the old stored `status` column; authorization now lives on the QR code (per spec 3.6.4).
     */
    public function getStatusAttribute(): string
    {
        $qr = $this->activeQrCode;

        if (!$qr) {
            return 'suspended';
        }

        if ($qr->expiry_date && $qr->expiry_date->isPast()) {
            return 'suspended';
        }

        return 'active';
    }
}
