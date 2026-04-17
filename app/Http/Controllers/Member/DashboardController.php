<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member()->with([
            'capacityTier',
            'allocations.project',
            'contributions.project',
            'pledges.project'
        ])->first();

        if (!$member) {
            return view('member.no-profile');
        }

        $totalAllocated   = $member->allocations->sum('allocated_amount');
        $totalContributed = $member->contributions->sum('amount');
        $totalBalance     = max(0, $totalAllocated - $totalContributed);
        $activeProjects   = $member->allocations->where('project.status', 'active')->count();

        return view('member.dashboard', compact(
            'member', 'totalAllocated', 'totalContributed',
            'totalBalance', 'activeProjects'
        ));
    }

    public function projects()
    {
        $member = Auth::user()->member()->with([
            'allocations.project',
            'contributions.project',
        ])->first();

        $allocations = $member->allocations()->with('project')
            ->whereHas('project', fn($q) => $q->whereIn('status', ['active', 'draft']))
            ->get()
            ->map(function ($allocation) use ($member) {
                $paid = $member->contributions()
                    ->where('project_id', $allocation->project_id)
                    ->sum('amount');

                $allocation->amount_paid = $paid;
                $allocation->balance     = max(0, $allocation->allocated_amount - $paid);
                $allocation->percentage  = $allocation->allocated_amount > 0
                    ? min(100, round(($paid / $allocation->allocated_amount) * 100, 1))
                    : 0;

                return $allocation;
            });

        return view('member.projects', compact('member', 'allocations'));
    }

    public function contributions()
    {
        $member = Auth::user()->member;

        $contributions = $member->contributions()
            ->with('project')
            ->latest('contribution_date')
            ->paginate(20);

        $totalContributed = $member->contributions()->sum('amount');

        return view('member.contributions', compact('member', 'contributions', 'totalContributed'));
    }
}