@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Record Contribution</h4>
    <a href="{{ route('admin.contributions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.contributions.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Member</label>
                            <select name="member_id" class="form-select @error('member_id') is-invalid @enderror"
                                id="memberSelect" required>
                                <option value="">— Select Member —</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}"
                                        data-allocation="{{ $member->allocations->where('project_id', old('project_id'))->first()?->allocated_amount ?? 0 }}"
                                        {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->user->name }} ({{ $member->member_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">— Select Project —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (UGX)</label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount') }}" min="1" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contribution Date</label>
                            <input type="date" name="contribution_date"
                                class="form-control @error('contribution_date') is-invalid @enderror"
                                value="{{ old('contribution_date', today()->format('Y-m-d')) }}" required>
                            @error('contribution_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                @foreach(['cash' => 'Cash', 'mobile_money' => 'Mobile Money', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference Number <span class="text-muted small">(optional)</span></label>
                            <input type="text" name="reference_number" class="form-control"
                                value="{{ old('reference_number') }}" placeholder="e.g. MM ref, bank ref">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes <span class="text-muted small">(optional)</span></label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Record Contribution
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Allocation hint panel --}}
    <div class="col-md-5">
        <div class="card border-0 bg-light" id="allocationHint" style="display:none!important;">
            <div class="card-body">
                <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Member Allocation Info</h6>
                <div id="hintContent"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Load allocation info when member + project are both selected
const memberSel  = document.getElementById('memberSelect');
const projectSel = document.querySelector('select[name="project_id"]');
const hint       = document.getElementById('allocationHint');
const hintContent = document.getElementById('hintContent');

function loadAllocationHint() {
    const memberId  = memberSel.value;
    const projectId = projectSel.value;
    if (!memberId || !projectId) { hint.style.setProperty('display','none','important'); return; }

    fetch(`/admin/allocation-hint?member_id=${memberId}&project_id=${projectId}`)
        .then(r => r.json())
        .then(data => {
            if (data.allocation) {
                hint.style.removeProperty('display');
                hintContent.innerHTML = `
                    <div class="mb-2"><span class="text-muted small">Allocated</span><br>
                        <strong>UGX ${Number(data.allocation.allocated_amount).toLocaleString()}</strong></div>
                    <div class="mb-2"><span class="text-muted small">Already Paid</span><br>
                        <strong class="text-success">UGX ${Number(data.paid).toLocaleString()}</strong></div>
                    <div><span class="text-muted small">Outstanding Balance</span><br>
                        <strong class="text-danger">UGX ${Number(data.balance).toLocaleString()}</strong></div>
                `;
            } else {
                hint.style.removeProperty('display');
                hintContent.innerHTML = `<p class="text-muted small mb-0">No allocation found for this member on this project.</p>`;
            }
        });
}

memberSel.addEventListener('change', loadAllocationHint);
projectSel.addEventListener('change', loadAllocationHint);
</script>
@endpush