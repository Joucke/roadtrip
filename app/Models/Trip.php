<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('user', fn(Builder $builder) => $builder
            ->whereHas('users', fn ($q) => $q->where('users.id', auth()->id())));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
