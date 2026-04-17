@extends('layouts.admin')
@section('content')

<h4 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>

{{-- Top Stats --}}

<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-label">Active Members</div>
                <div class="stat-value">{{ $totalMembers }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-folder-fill"></i>
            </div>
            <div>
                <div class="stat-label">Projects</div>
                <div class="stat-value">{{ $totalProjects }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <div class="stat-label">Total Allocated</div>
                <div class="stat-value text-primary" style="font-size:1.1rem;">
                    UGX {{ number_format($totalAllocated) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Collected</div>
                <div class="stat-value text-success" style="font-size:1.1rem;">
                    UGX {{ number_format($totalCollected) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label">Outstanding</div>
                <div class="stat-value text-danger" style="font-size:1.1rem;">
                    UGX {{ number_format($totalOutstanding) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Left Column --}}
    <div class="col-md-8">

        {{-- Monthly Trend Chart --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Collection Trend — Last 6 Months</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>

        {{-- Project Progress --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Project Funding Progress</h6>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($projects as $project)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <a href="{{ route('admin.projects.show', $project) }}"
                            class="text-decoration-none fw-semibold small">
                            {{ $project->name }}
                        </a>
                        <span class="text-muted small">
                            UGX {{ number_format($project->collected) }}
                            / UGX {{ number_format($project->target_amount) }}
                        </span>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar bg-success" style="width:{{ $project->percentage }}%"
                            title="{{ $project->percentage }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ $project->allocations_count }} members allocated</small>
                        <small class="text-muted">{{ $project->percentage }}%</small>
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No active projects.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">

        {{-- Top Members --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Contributors</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    @forelse($topMembers as $i => $row)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-{{ $i === 0 ? 'warning text-dark' : ($i === 1 ? 'secondary' : 'light text-dark') }} me-2">
                                #{{ $i + 1 }}
                            </span>
                            {{ $row->member->user->name ?? '—' }}
                        </td>
                        <td class="text-end pe-3 text-success fw-semibold">
                            UGX {{ number_format($row->total) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td class="text-muted text-center py-3">No contributions yet.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h6>
                <a href="{{ route('admin.contributions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentContributions as $c)
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $c->member->user->name }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ $c->project->name }} · {{ $c->contribution_date->format('d M') }}
                                </div>
                            </div>
                            <span class="text-success fw-bold small">
                                UGX {{ number_format($c->amount) }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-muted text-center py-3">No activity yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = @json($trend->pluck('label'));
const data   = @json($trend->pluck('total'));

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'UGX Collected',
            data: data,
            backgroundColor: 'rgba(25, 135, 84, 0.7)',
            borderColor: 'rgba(25, 135, 84, 1)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'UGX ' + Number(ctx.raw).toLocaleString()
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => 'UGX ' + Number(val).toLocaleString()
                }
            }
        }
    }
});
</script>
@endpush