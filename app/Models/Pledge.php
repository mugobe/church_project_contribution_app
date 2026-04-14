<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    protected $fillable = [
        'member_id', 'project_id', 'pledged_amount',
        'pledge_date', 'expiry_date', 'status', 'notes'
    ];

    protected $casts = [
        'pledge_date'   => 'date',
        'expiry_date'   => 'date',
        'pledged_amount'=> 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // How much has been paid against this pledge
    public function amountPaid(): float
    {
        return Contribution::where('member_id', $this->member_id)
            ->where('project_id', $this->project_id)
            ->sum('amount');
    }

    public function isFullfilled(): bool
    {
        return $this->amountPaid() >= $this->pledged_amount;
    }
}