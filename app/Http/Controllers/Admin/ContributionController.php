<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Member;
use App\Models\Project;
use App\Services\ContributionService;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    protected ContributionService $contributionService;

    public function __construct(ContributionService $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    public function index(Request $request)
    {
        $query = Contribution::with(['member.user', 'project'])
            ->latest('contribution_date');

        // Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('contribution_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('contribution_date', '<=', $request->to);
        }

        $contributions = $query->paginate(25)->withQueryString();
      $projects = Project::whereIn('status', ['active', 'draft'])->get();
        $members       = Member::with('user')->where('status', 'active')->get();
        $totalAmount   = $query->sum('amount');

        return view('admin.contributions.index', compact(
            'contributions', 'projects', 'members', 'totalAmount'
        ));
    }

    public function create()
    {

$projects = Project::whereIn('status', ['active', 'draft'])->get();
    $members  = Member::with('user')->where('status', 'active')->get();
    return view('admin.contributions.create', compact('projects', 'members'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'         => 'required|exists:members,id',
            'project_id'        => 'required|exists:projects,id',
            'amount'            => 'required|numeric|min:1',
            'contribution_date' => 'required|date',
            'payment_method'    => 'required|in:cash,mobile_money,bank_transfer,other',
            'reference_number'  => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        $this->contributionService->record($request->all());

        return redirect()->route('admin.contributions.index')
            ->with('success', 'Contribution recorded and journal entries created.');
    }

    public function show(Contribution $contribution)
    {
        $contribution->load(['member.user', 'project', 'journalEntries']);
        return view('admin.contributions.show', compact('contribution'));
    }

    public function destroy(Contribution $contribution)
    {
        $this->contributionService->reverse($contribution);
        return redirect()->route('admin.contributions.index')
            ->with('success', 'Contribution reversed and journal entries removed.');
    }
}