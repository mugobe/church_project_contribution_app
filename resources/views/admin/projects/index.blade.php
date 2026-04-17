@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-folder me-2"></i>Projects</h4>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Project
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Project</th>
                    <th>Target Amount</th>
                    <th>Collected</th>
                    <th>Progress</th>
                    <th>Members</th>
                    <th>Status</th>
                    <th>Target Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                @php
                    $pct = $project->fundingPercentage();
                    $statusColor = match($project->status) {
                        'active'    => 'success',
                        'draft'     => 'secondary',
                        'completed' => 'primary',
                        'suspended' => 'danger',
                        default     => 'secondary'
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('admin.projects.show', $project) }}" class="fw-semibold text-decoration-none">
                            {{ $project->name }}
                        </a>
                        @if($project->description)
                            <div class="text-muted small">{{ Str::limit($project->description, 50) }}</div>
                        @endif
                    </td>
                    <td>UGX {{ number_format($project->target_amount) }}</td>
                    <td>UGX {{ number_format($project->totalContributed()) }}</td>
                    <td style="min-width:120px;">
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $pct }}%</small>
                    </td>
                    <td>{{ $project->allocations_count }}</td>
                    <td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($project->status) }}</span></td>
                    <td>{{ $project->target_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.projects.show', $project) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.projects.edit', $project) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                                        onsubmit="return confirm('Delete this project?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
    <div class="card-footer">{{ $projects->links() }}</div>
    @endif
</div>
@endsection