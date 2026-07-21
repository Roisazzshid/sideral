<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = ['inventory_id', 'type', 'quantity', 'transaction_date', 'reference', 'notes'];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
