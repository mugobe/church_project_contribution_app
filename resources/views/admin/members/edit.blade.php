@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Member — {{ $member->user->name }}</h4>
    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:680px;">
    <div class="card-body">
        <form action="{{ route('admin.members.update', $member) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $member->user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $member->user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $member->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity Tier</label>
                    <select name="capacity_tier_id" class="form-select">
                        <option value="">— Select Tier —</option>
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}"
                                {{ old('capacity_tier_id', $member->capacity_tier_id) == $tier->id ? 'selected' : '' }}>
                                {{ $tier->name }} (Weight: {{ $tier->weight }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Joined Date</label>
                    <input type="date" name="joined_date" class="form-control"
                        value="{{ old('joined_date', $member->joined_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['active', 'inactive', 'suspended'] as $s)
                            <option value="{{ $s }}" {{ old('status', $member->status) === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $member->address) }}</textarea>
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