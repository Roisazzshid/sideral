<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = ['lamp_type_id', 'stock_quantity', 'min_stock'];

    public function lampType(): BelongsTo
    {
        return $this->belongsTo(LampType::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'habis';
        }
        if ($this->stock_quantity <= $this->min_stock) {
            return 'menipis';
        }
        return 'tersedia';
    }
}
