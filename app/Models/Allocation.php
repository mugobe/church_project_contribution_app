<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $fillable = [
        'member_id', 'project_id', 'allocated_amount',
        'method', 'status', 'notes'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // How much of this allocation has been paid
    public function amountPaid(): float
    {
        return Contribution::where('member_id', $this->member_id)
            ->where('project_id', $this->project_id)
            ->sum('amount');
    }

    // Outstanding balance
    public function balance(): float
    {
        return max(0, $this->allocated_amount - $this->amountPaid());
    }
}