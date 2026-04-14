<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacityTier extends Model
{
    protected $fillable = ['name', 'weight', 'description', 'is_active'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}