@extends('layouts.member')
@section('content')

{{-- Welcome --}}
<div class="mb-4">
    <h4 class="mb-1">Welcome, {{ $member->user->name }} 👋</h4>
    <p class="text-muted mb-0">
        {{ $member->member_number }}
        @if($member->capacityTier)
            &nbsp;·&nbsp; <span class="badge bg-info text-dark">{{ $member->capacityTier->name }}</span>
        @endif
    </p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small mb-1">Total Allocated</div>
            <div class="fs-6 fw-bold text-primary">UGX {{ number_format($totalAllocated) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small mb-1">Total Contributed</div>
            <div class="fs-6 fw-bold text-success">UGX {{ number_format($totalContributed) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small mb-1">Outstanding Balance</div>
            <div class="fs-6 fw-bold text-danger">UGX {{ number_format($totalBalance) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small mb-1">Active Projects</div>
            <div class="fs-6 fw-bold">{{ $activeProjects }}</div>
        </div>
    </div>
</div>

{{-- My Projects Summary --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-folder me-2"></i>My Projects</h6>
        <a href="{{ route('member.projects') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Project</th>
                    <th>Allocated</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->allocations->take(5) as $allocation)
                @php
                    $paid = $member->contributions->where('project_id', $allocation->project_id)->sum('amount');
                    $bal  = max(0, $allocation->allocated_amount - $paid);
                    $pct  = $allocation->allocated_amount > 0
                        ? min(100, round(($paid / $allocation->allocated_amount) * 100, 1)) : 0;
                @endphp
                <tr>
                    <td>{{ $allocation->project->name }}</td>
                    <td>UGX {{ number_format($allocation->allocated_amount) }}</td>
                    <td class="text-success">UGX {{ number_format($paid) }}</td>
                    <td class="{{ $bal > 0 ? 'text-danger' : 'text-success' }}">UGX {{ number_format($bal) }}</td>
                    <td style="min-width:100px;">
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $pct }}%</small>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No projects allocated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Contributions --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Contributions</h6>
        <a href="{{ route('member.contributions') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->contributions->sortByDesc('contribution_date')->take(5) as $c)
                <tr>
                    <td>{{ $c->contribution_date->format('d M Y') }}</td>
                    <td>{{ $c->project->name }}</td>
                    <td class="fw-bold text-success">UGX {{ number_format($c->amount) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $c->payment_method)) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No contributions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection