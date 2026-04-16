<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\JournalEntry;
use App\Models\Pledge;
use Illuminate\Support\Facades\DB;

class ContributionService
{
    public function record(array $data): Contribution
    {
        return DB::transaction(function () use ($data) {

            // Create the contribution
            $contribution = Contribution::create([
                'member_id'         => $data['member_id'],
                'project_id'        => $data['project_id'],
                'amount'            => $data['amount'],
                'contribution_date' => $data['contribution_date'],
                'payment_method'    => $data['payment_method'],
                'reference_number'  => $data['reference_number'] ?? null,
                'recorded_by'       => $data['recorded_by'] ?? auth()->user()->name,
                'notes'             => $data['notes'] ?? null,
            ]);

            // Generate journal reference
            $ref = 'CONTRIB-' . str_pad($contribution->id, 5, '0', STR_PAD_LEFT);

            // Double-entry journal — Debit member account, Credit project fund
            JournalEntry::create([
                'reference'       => $ref,
                'contribution_id' => $contribution->id,
                'member_id'       => $contribution->member_id,
                'project_id'      => $contribution->project_id,
                'type'            => 'debit',
                'amount'          => $contribution->amount,
                'description'     => "Contribution by member towards project",
                'entry_date'      => $contribution->contribution_date,
            ]);

            JournalEntry::create([
                'reference'       => $ref,
                'contribution_id' => $contribution->id,
                'member_id'       => $contribution->member_id,
                'project_id'      => $contribution->project_id,
                'type'            => 'credit',
                'amount'          => $contribution->amount,
                'description'     => "Project fund received from member",
                'entry_date'      => $contribution->contribution_date,
            ]);

            // Auto-update pledge status if fulfilled
            $pledge = Pledge::where('member_id', $contribution->member_id)
                ->where('project_id', $contribution->project_id)
                ->where('status', 'active')
                ->first();

            if ($pledge) {
                $totalPaid = Contribution::where('member_id', $contribution->member_id)
                    ->where('project_id', $contribution->project_id)
                    ->sum('amount');

                if ($totalPaid >= $pledge->pledged_amount) {
                    $pledge->update(['status' => 'fulfilled']);
                }
            }

            return $contribution;
        });
    }

    public function reverse(Contribution $contribution): bool
    {
        return DB::transaction(function () use ($contribution) {
            $contribution->journalEntries()->delete();
            $contribution->delete();
            return true;
        });
    }
}