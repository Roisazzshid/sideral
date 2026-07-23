<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'floor_id', 'lamp_id', 'type', 'description', 'priority', 'status', 
        'scheduled_date', 'completed_date', 'assigned_to', 'resolution_notes',
        'work_start_time', 'work_end_time'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function lamp(): BelongsTo
    {
        return $this->belongsTo(Lamp::class);
    }
}
