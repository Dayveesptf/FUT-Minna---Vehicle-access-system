<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatePoint extends Model
{
    protected $fillable = ['gate_name', 'location', 'status'];

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
}
