<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'reference', 'contribution_id', 'member_id', 'project_id',
        'type', 'amount', 'description', 'entry_date'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount'     => 'decimal:2',
    ];

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}