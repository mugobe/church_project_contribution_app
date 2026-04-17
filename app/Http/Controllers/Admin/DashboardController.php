<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Member;
use App\Models\Project;
use App\Models\Allocation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Top stats
        $totalMembers      = Member::where('status', 'active')->count();
        $totalProjects     = Project::whereIn('status', ['active', 'draft'])->count();
        $totalCollected    = Contribution::sum('amount');
        $totalAllocated    = Allocation::sum('allocated_amount');
        $totalOutstanding  = max(0, $totalAllocated - $totalCollected);

        // Projects with progress
        $projects = Project::whereIn('status', ['active', 'draft'])
            ->withCount('allocations')
            ->get()
            ->map(function ($project) {
                $project->collected   = $project->totalContributed();
                $project->percentage  = $project->fundingPercentage();
                return $project;
            })
            ->sortByDesc('percentage');

        // Recent contributions
        $recentContributions = Contribution::with(['member.user', 'project'])
            ->latest('contribution_date')
            ->take(8)
            ->get();

        // Monthly collection trend — last 6 months
        $trend = Contribution::select(
                DB::raw('YEAR(contribution_date) as year'),
                DB::raw('MONTH(contribution_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('contribution_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'label' => date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year)),
                'total' => (float) $row->total,
            ]);

        // Top contributing members
        $topMembers = Contribution::select('member_id', DB::raw('SUM(amount) as total'))
            ->with('member.user')
            ->groupBy('member_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers', 'totalProjects', 'totalCollected',
            'totalAllocated', 'totalOutstanding', 'projects',
            'recentContributions', 'trend', 'topMembers'
        ));
    }
}