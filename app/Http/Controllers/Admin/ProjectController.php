<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\AllocationService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected AllocationService $allocationService;

    public function __construct(AllocationService $allocationService)
    {
        $this->allocationService = $allocationService;
    }

    public function index()
    {
        $projects = Project::withCount('allocations')
            ->latest()
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'start_date'    => 'nullable|date',
            'target_date'   => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:draft,active,completed,suspended',
        ]);

        $project = Project::create($request->only([
            'name', 'description', 'target_amount',
            'start_date', 'target_date', 'status'
        ]));

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['allocations.member.user', 'allocations.member.capacityTier']);

        $totalAllocated   = $project->allocations->sum('allocated_amount');
        $totalContributed = $project->totalContributed();
        $fundingPct       = $project->fundingPercentage();

        return view('admin.projects.show', compact(
            'project', 'totalAllocated', 'totalContributed', 'fundingPct'
        ));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'start_date'    => 'nullable|date',
            'target_date'   => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:draft,active,completed,suspended',
        ]);

        $project->update($request->only([
            'name', 'description', 'target_amount',
            'start_date', 'target_date', 'status'
        ]));

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project removed.');
    }

    // Auto-generate allocations for this project
    public function generateAllocations(Request $request, Project $project)
    {
        $overwrite = $request->boolean('overwrite_manual', false);
        $result    = $this->allocationService->generateAllocations($project, $overwrite);

        return redirect()->route('admin.projects.show', $project)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    // Update a single member allocation inline
    public function updateAllocation(Request $request, Project $project)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount'    => 'required|numeric|min:0',
            'notes'     => 'nullable|string',
        ]);

        $this->allocationService->updateAllocation(
            $request->member_id,
            $project->id,
            $request->amount,
            $request->notes
        );

        return back()->with('success', 'Allocation updated.');
    }

    // CSV upload for allocations
    public function importAllocations(Request $request, Project $project)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $rows   = [];
        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($handle); // first row = headers

        // Normalize headers
        $header = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $header);

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($header, $row);
        }

        fclose($handle);

        $result = $this->allocationService->importFromCsv($project, $rows);

        $message = "Imported {$result['imported']} allocations.";
        if (!empty($result['errors'])) {
            $message .= ' Errors: ' . implode(' | ', $result['errors']);
        }

        return back()->with($result['success'] ? 'success' : 'error', $message);
    }
}