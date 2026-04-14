<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'capacity_tier_id', 'member_number',
        'phone', 'address', 'joined_date', 'status'
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function capacityTier()
    {
        return $this->belongsTo(CapacityTier::class);
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }
}