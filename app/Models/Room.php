<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['floor_id', 'name', 'type', 'description'];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function lamps(): HasMany
    {
        return $this->hasMany(Lamp::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
