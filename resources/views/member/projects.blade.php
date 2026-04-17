@extends('layouts.member')
@section('content')

<h4 class="mb-4"><i class="bi bi-folder me-2"></i>My Projects</h4>

@forelse($allocations as $allocation)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="mb-1">{{ $allocation->project->name }}</h6>
                <p class="text-muted small mb-0">{{ $allocation->project->description }}</p>
            </div>
            @php $sc = match($allocation->project->status) {
                'active'=>'success','draft'=>'secondary',
                'completed'=>'primary','suspended'=>'danger',default=>'secondary'
            }; @endphp
            <span class="badge bg-{{ $sc }}">{{ ucfirst($allocation->project->status) }}</span>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-4 text-center">
                <div class="text-muted small">Allocated to me</div>
                <div class="fw-bold text-primary">UGX {{ number_format($allocation->allocated_amount) }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="text-muted small">I have paid</div>
                <div class="fw-bold text-success">UGX {{ number_format($allocation->amount_paid) }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="text-muted small">My balance</div>
                <div class="fw-bold {{ $allocation->balance > 0 ? 'text-danger' : 'text-success' }}">
                    UGX {{ number_format($allocation->balance) }}
                </div>
            </div>
        </div>

        <div class="progress mb-1" style="height:8px;">
            <div class="progress-bar bg-success" style="width:{{ $allocation->percentage }}%"></div>
        </div>
        <small class="text-muted">{{ $allocation->percentage }}% of my allocation paid</small>

        @if($allocation->project->target_date)
            <div class="mt-2">
                <small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>
                    Target completion: {{ $allocation->project->target_date->format('d M Y') }}
                </small>
            </div>
        @endif
    </div>
</div>
@empty
<div class="text-center text-muted py-5">
    <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
    No projects allocated to you yet.
</div>
@endforelse

@endsection