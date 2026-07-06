<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
   protected $fillable = [
        'qr_code_id', 'gate_point_id', 'operator_id',
        'scan_timestamp', 'access_decision', 'direction', 'denial_reason',
        'is_acknowledged', 'acknowledged_at', 'acknowledged_by'
    ];

    protected $casts = [
        'scan_timestamp' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function gatePoint()
    {
        return $this->belongsTo(GatePoint::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    // Convenience accessors so views can still reach vehicle/user through the chain
    public function vehicle()
    {
        return $this->qrCode?->vehicle;
    }
}
