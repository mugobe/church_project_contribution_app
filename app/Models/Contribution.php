<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contribution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'project_id', 'amount', 'contribution_date',
        'payment_method', 'reference_number', 'recorded_by', 'notes'
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'amount'            => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}