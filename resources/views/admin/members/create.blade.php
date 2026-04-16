@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add Member</h4>
    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:680px;">
    <div class="card-body">
        <form action="{{ route('admin.members.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity Tier</label>
                    <select name="capacity_tier_id" class="form-select">
                        <option value="">— Select Tier —</option>
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}" {{ old('capacity_tier_id') == $tier->id ? 'selected' : '' }}>
                                {{ $tier->name }} (Weight: {{ $tier->weight }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Joined Date</label>
                    <input type="date" name="joined_date" class="form-control" value="{{ old('joined_date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                </div>
                <div class="col-12">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        A login account will be created automatically. Default password is <strong>password</strong>.
                    </p>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i>Create Member
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection