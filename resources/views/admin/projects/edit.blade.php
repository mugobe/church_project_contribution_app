@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit — {{ $project->name }}</h4>
    <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:680px;">
    <div class="card-body">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Project Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $project->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Target Amount (UGX)</label>
                    <input type="number" name="target_amount" class="form-control @error('target_amount') is-invalid @enderror"
                        value="{{ old('target_amount', $project->target_amount) }}" min="1" required>
                    @error('target_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft','active','suspended','completed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Target Completion Date</label>
                    <input type="date" name="target_date" class="form-control"
                        value="{{ old('target_date', $project->target_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection