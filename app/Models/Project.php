<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'target_amount',
        'start_date', 'target_date', 'status', 'cover_image'
    ];

    protected $casts = [
        'start_date'    => 'date',
        'target_date'   => 'date',
        'target_amount' => 'decimal:2',
    ];

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

    // Total amount contributed so far
    public function totalContributed(): float
    {
        return $this->contributions()->sum('amount');
    }

    // Percentage funded
    public function fundingPercentage(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->totalContributed() / $this->target_amount) * 100, 1));
    }
}