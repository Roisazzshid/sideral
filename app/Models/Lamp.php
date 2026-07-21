<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lamp extends Model
{
    protected $fillable = ['room_id', 'lamp_type_id', 'code', 'position_x', 'position_y', 'rotation', 'width', 'height', 'status', 'installed_date', 'notes'];

    protected $casts = [
        'installed_date' => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function lampType(): BelongsTo
    {
        return $this->belongsTo(LampType::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
