@extends('layouts.public')
@section('content')

<div class="text-center mb-5">
    <h2 class="fw-bold">Our Community Projects</h2>
    <p class="text-muted">Track the progress of all active church projects and see how your contributions are making a difference.</p>
</div>

@forelse($projects as $project)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h5 class="mb-0 fw-bold">{{ $project->name }}</h5>
                    <span class="badge bg-success">Active</span>
                </div>
                @if($project->description)
                    <p class="text-muted mb-3">{{ $project->description }}</p>
                @endif

                {{-- Progress Bar --}}
                <div class="progress mb-2" style="height:14px; border-radius:10px;">
                    <div class="progress-bar bg-success"
                        style="width:{{ $project->percentage }}%; border-radius:10px;"
                        role="progressbar">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        UGX {{ number_format($project->collected) }} raised
                    </small>
                    <small class="fw-bold text-success">{{ $project->percentage }}% funded</small>
                </div>
            </div>

            <div class="col-md-4 mt-3 mt-md-0">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small">Target</div>
                            <div class="fw-bold">UGX {{ number_format($project->target_amount) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded p-3">
                            <div class="text-muted small">Raised</div>
                            <div class="fw-bold text-success">UGX {{ number_format($project->collected) }}</div>
                        </div>
                    </div>
                    @if($project->target_date)
                    <div class="col-12">
                        <div class="bg-light rounded p-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                Target: {{ $project->target_date->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <i class="bi bi-folder2-open fs-1 text-muted d-block mb-3"></i>
    <h5 class="text-muted">No active projects at the moment.</h5>
    <p class="text-muted">Check back soon.</p>
</div>
@endforelse

@endsection