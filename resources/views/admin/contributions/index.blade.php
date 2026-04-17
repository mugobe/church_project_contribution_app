@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Contributions</h4>
    <a href="{{ route('admin.contributions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Record Contribution
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Project</label>
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Member</label>
                <select name="member_id" class="form-select form-select-sm">
                    <option value="">All Members</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('admin.contributions.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="alert alert-light border mb-3">
    <strong>Total in view:</strong> UGX {{ number_format($totalAmount) }}
    &nbsp;·&nbsp; {{ $contributions->total() }} records
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Recorded By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $c)
                <tr>
                    <td>{{ $c->contribution_date->format('d M Y') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $c->member->user->name }}</div>
                        <small class="text-muted">{{ $c->member->member_number }}</small>
                    </td>
                    <td>{{ $c->project->name }}</td>
                    <td class="fw-bold text-success">UGX {{ number_format($c->amount) }}</td>
                    <td>
                        @php $mc = match($c->payment_method) {
                            'mobile_money'  => 'warning',
                            'bank_transfer' => 'info',
                            'cash'          => 'success',
                            default         => 'secondary'
                        }; @endphp
                        <span class="badge bg-{{ $mc }}">{{ ucfirst(str_replace('_', ' ', $c->payment_method)) }}</span>
                    </td>
                    <td>{{ $c->reference_number ?? '—' }}</td>
                    <td>{{ $c->recorded_by ?? '—' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.contributions.show', $c) }}">
                                        <i class="bi bi-eye me-2"></i>View Journal
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.contributions.destroy', $c) }}" method="POST"
                                        onsubmit="return confirm('Reverse this contribution? Journal entries will be deleted.')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger">
                                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reverse
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No contributions recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contributions->hasPages())
    <div class="card-footer">{{ $contributions->links() }}</div>
    @endif
</div>
@endsection