@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-people me-2"></i>Members</h4>
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Add Member
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Member No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td><span class="badge bg-secondary">{{ $member->member_number }}</span></td>
                    <td>{{ $member->user->name }}</td>
                    <td>{{ $member->user->email }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td>
                        @if($member->capacityTier)
                            <span class="badge bg-info text-dark">{{ $member->capacityTier->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColor = match($member->status) {
                                'active'    => 'success',
                                'inactive'  => 'secondary',
                                'suspended' => 'danger',
                                default     => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }}">{{ ucfirst($member->status) }}</span>
                    </td>
                    <td>{{ $member->joined_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.members.show', $member) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.members.edit', $member) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST"
                                        onsubmit="return confirm('Remove this member?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Remove</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No members found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div class="card-footer">{{ $members->links() }}</div>
    @endif
</div>
@endsection