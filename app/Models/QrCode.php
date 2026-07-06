<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = [
        'vehicle_id', 'registered_user_id', 'token',
        'encrypted_payload', 'generation_date', 'expiry_date', 'status'
    ];

    protected $casts = [
        'generation_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function registeredUser()
    {
        return $this->belongsTo(RegisteredUser::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    /**
     * Issue a new encrypted QR code for a vehicle.
     */
    public static function issueFor(Vehicle $vehicle, ?\DateTimeInterface $expiry = null): self
    {
        $token = strtoupper(Str::random(16));

        $qr = self::create([
            'vehicle_id' => $vehicle->id,
            'registered_user_id' => $vehicle->registered_user_id,
            'token' => $token,
            'encrypted_payload' => '',
            'generation_date' => now(),
            'expiry_date' => $expiry,
            'status' => 'active',
        ]);

        $payload = [
            'user_id' => $vehicle->registered_user_id,
            'vehicle_id' => $vehicle->id,
            'qr_code_id' => $qr->id,
            'token' => $token,
            'expiry' => $expiry?->toIso8601String(),
        ];

        $qr->encrypted_payload = Crypt::encryptString(json_encode($payload));
        $qr->save();

        return $qr;
    }

    /**
     * Decrypt and validate a scanned QR value against the database.
     * Returns ['valid' => bool, 'reason' => string|null, 'qr' => QrCode|null]
     */
    public static function resolveFromScan(string $scannedValue): array
    {
        try {
            $decrypted = Crypt::decryptString($scannedValue);
            $payload = json_decode($decrypted, true);
        } catch (\Exception $e) {
            return ['valid' => false, 'reason' => 'Malformed or tampered QR code.', 'qr' => null];
        }

        $qr = self::find($payload['qr_code_id'] ?? null);

        if (!$qr || $qr->token !== ($payload['token'] ?? null)) {
            return ['valid' => false, 'reason' => 'QR code does not match any record.', 'qr' => null];
        }

        if ($qr->status !== 'active') {
            return ['valid' => false, 'reason' => 'QR code has been revoked.', 'qr' => $qr];
        }

        if ($qr->expiry_date && $qr->expiry_date->isPast()) {
            return ['valid' => false, 'reason' => 'QR code has expired.', 'qr' => $qr];
        }

        return ['valid' => true, 'reason' => null, 'qr' => $qr];
    }
}
