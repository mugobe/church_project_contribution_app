<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\Member;
use App\Models\Project;

class AllocationService
{
    // Auto-generate allocations based on capacity tier weights
    public function generateAllocations(Project $project, bool $overwriteManual = false): array
    {
        $members = Member::with('capacityTier')
            ->where('status', 'active')
            ->get();

        if ($members->isEmpty()) {
            return ['success' => false, 'message' => 'No active members found.'];
        }

        // Calculate total weight units across all members
        $totalUnits = $members->sum(fn($m) => $m->capacityTier?->weight ?? 1);

        if ($totalUnits <= 0) {
            return ['success' => false, 'message' => 'No valid tier weights found.'];
        }

        $amountPerUnit = $project->target_amount / $totalUnits;
        $created = 0;
        $skipped = 0;

        foreach ($members as $member) {
            $weight = $member->capacityTier?->weight ?? 1;
            $amount = round(($weight * $amountPerUnit) / 1000) * 1000; // round to nearest 1000

            $existing = Allocation::where('member_id', $member->id)
                ->where('project_id', $project->id)
                ->first();

            if ($existing) {
                // Skip manual overrides unless explicitly told to overwrite
                if ($existing->method === 'manual' && !$overwriteManual) {
                    $skipped++;
                    continue;
                }
                $existing->update([
                    'allocated_amount' => $amount,
                    'method'           => 'auto',
                ]);
            } else {
                Allocation::create([
                    'member_id'        => $member->id,
                    'project_id'       => $project->id,
                    'allocated_amount' => $amount,
                    'method'           => 'auto',
                    'status'           => 'pending',
                ]);
                $created++;
            }
        }

        return [
            'success' => true,
            'message' => "Allocations generated. {$created} created, {$skipped} manual overrides skipped.",
        ];
    }

    // Update a single member allocation manually
    public function updateAllocation(int $memberId, int $projectId, float $amount, string $notes = null): Allocation
    {
        return Allocation::updateOrCreate(
            ['member_id' => $memberId, 'project_id' => $projectId],
            [
                'allocated_amount' => $amount,
                'method'           => 'manual',
                'status'           => 'pending',
                'notes'            => $notes,
            ]
        );
    }

    // Import allocations from CSV
    public function importFromCsv(Project $project, array $rows): array
    {
        $imported = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            $member = $this->resolveMember($row);

            if (!$member) {
                $errors[] = "Row " . ($index + 1) . ": Could not match member — '{$row['name']}'";
                continue;
            }

            if (empty($row['amount']) || !is_numeric($row['amount'])) {
                $errors[] = "Row " . ($index + 1) . ": Invalid amount for {$member->user->name}";
                continue;
            }

            Allocation::updateOrCreate(
                ['member_id' => $member->id, 'project_id' => $project->id],
                [
                    'allocated_amount' => (float) $row['amount'],
                    'method'           => 'csv',
                    'status'           => 'pending',
                ]
            );

            $imported++;
        }

        return [
            'success'  => $imported > 0,
            'imported' => $imported,
            'errors'   => $errors,
        ];
    }

    // Try to match a CSV row to a member — by number, name, or phone
    private function resolveMember(array $row): ?Member
    {
        $members = Member::with('user')->get();

        // Match by member number
        if (!empty($row['member_number'])) {
            $match = $members->firstWhere('member_number', $row['member_number']);
            if ($match) return $match;
        }

        // Match by phone
        if (!empty($row['phone'])) {
            $match = $members->firstWhere('phone', $row['phone']);
            if ($match) return $match;
        }

        // Fuzzy match by name
        if (!empty($row['name'])) {
            $search = strtolower(trim($row['name']));
            foreach ($members as $member) {
                $memberName = strtolower(trim($member->user->name ?? ''));
                similar_text($search, $memberName, $percent);
                if ($percent >= 80) return $member;
            }
        }

        return null;
    }
}