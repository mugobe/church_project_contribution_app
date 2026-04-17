@extends('layouts.admin')
@section('content')

{{-- Project Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-folder me-2"></i>{{ $project->name }}</h4>
        <p class="text-muted mb-0">{{ $project->description }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small">Target</div>
                <div class="fs-5 fw-bold">UGX {{ number_format($project->target_amount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small">Collected</div>
                <div class="fs-5 fw-bold text-success">UGX {{ number_format($totalContributed) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small">Total Allocated</div>
                <div class="fs-5 fw-bold text-primary">UGX {{ number_format($totalAllocated) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small">Funding Progress</div>
                <div class="fs-5 fw-bold">{{ $fundingPct }}%</div>
                <div class="progress mt-1" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:{{ $fundingPct }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Allocation Actions --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Member Allocations</h6>
        <div class="d-flex gap-2">
            {{-- Auto Generate --}}
            <form action="{{ route('admin.projects.generate-allocations', $project) }}" method="POST"
                onsubmit="return confirm('Generate allocations for all active members?')">
                @csrf
                <button class="btn btn-sm btn-success">
                    <i class="bi bi-magic me-1"></i>Auto Generate
                </button>
            </form>

            {{-- CSV Upload --}}
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#csvModal">
                <i class="bi bi-upload me-1"></i>Import CSV
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Member</th>
                    <th>Tier</th>
                    <th>Allocated</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Override</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->allocations as $allocation)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $allocation->member->user->name }}</div>
                        <small class="text-muted">{{ $allocation->member->member_number }}</small>
                    </td>
                    <td>
                        @if($allocation->member->capacityTier)
                            <span class="badge bg-info text-dark">{{ $allocation->member->capacityTier->name }}</span>
                        @else —
                        @endif
                    </td>
                    <td>UGX {{ number_format($allocation->allocated_amount) }}</td>
                    <td class="text-success">UGX {{ number_format($allocation->amountPaid()) }}</td>
                    <td class="{{ $allocation->balance() > 0 ? 'text-danger' : 'text-success' }}">
                        UGX {{ number_format($allocation->balance()) }}
                    </td>
                    <td><span class="badge bg-secondary">{{ ucfirst($allocation->method) }}</span></td>
                    <td>
                        @php $sc = match($allocation->status) { 'notified'=>'info','acknowledged'=>'success',default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $sc }}">{{ ucfirst($allocation->status) }}</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#overrideModal"
                            data-member-id="{{ $allocation->member_id }}"
                            data-member-name="{{ $allocation->member->user->name }}"
                            data-amount="{{ $allocation->allocated_amount }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">
                    No allocations yet. Use Auto Generate or Import CSV to get started.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Override Modal --}}
<div class="modal fade" id="overrideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.projects.update-allocation', $project) }}" method="POST">
                @csrf
                <input type="hidden" name="member_id" id="overrideMemberId">
                <div class="modal-header">
                    <h6 class="modal-title">Override Allocation — <span id="overrideMemberName"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Amount (UGX)</label>
                    <input type="number" name="amount" id="overrideAmount" class="form-control" min="0" required>
                    <label class="form-label mt-3">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Override</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSV Import Modal --}}
<div class="modal fade" id="csvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.projects.import-allocations', $project) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-upload me-2"></i>Import Allocations from CSV</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">CSV should have columns: <code>member_number</code>, <code>name</code> or <code>phone</code>, and <code>amount</code>. System will match members automatically.</p>
                    <label class="form-label">Upload CSV File</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('overrideModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('overrideMemberId').value = btn.dataset.memberId;
    document.getElementById('overrideMemberName').textContent = btn.dataset.memberName;
    document.getElementById('overrideAmount').value = btn.dataset.amount;
});
</script>
@endpush