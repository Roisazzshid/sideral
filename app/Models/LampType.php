<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LampType extends Model
{
    protected $fillable = ['name', 'type', 'shape', 'watt', 'price', 'description', 'status'];

    public function lamps(): HasMany
    {
        return $this->hasMany(Lamp::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
