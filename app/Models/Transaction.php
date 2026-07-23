<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['lamp_id', 'floor_id', 'lamp_type_id', 'type', 'quantity', 'transaction_date', 'technician', 'notes'];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function lamp(): BelongsTo
    {
        return $this->belongsTo(Lamp::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function lampType(): BelongsTo
    {
        return $this->belongsTo(LampType::class);
    }
}
