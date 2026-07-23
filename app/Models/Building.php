<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['name', 'location', 'description'];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function getTotalLampsAttribute(): int
    {
        return $this->floors->sum(function ($floor) {
            return $floor->lamps->count();
        });
    }
}
