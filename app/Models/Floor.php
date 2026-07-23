<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    protected $fillable = ['building_id', 'name', 'floor_number', 'description', 'floor_plan_image'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function lamps(): HasMany
    {
        return $this->hasMany(Lamp::class);
    }
}
