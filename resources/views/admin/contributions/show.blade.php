@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-receipt me-2"></i>Contribution Detail</h4>
    <a href="{{ route('admin.contributions.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Contribution</h6></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted">Member</td><td class="fw-semibold">{{ $contribution->member->user->name }}</td></tr>
                    <tr><td class="text-muted">Member No.</td><td>{{ $contribution->member->member_number }}</td></tr>
                    <tr><td class="text-muted">Project</td><td>{{ $contribution->project->name }}</td></tr>
                    <tr><td class="text-muted">Amount</td><td class="fw-bold text-success">UGX {{ number_format($contribution->amount) }}</td></tr>
                    <tr><td class="text-muted">Date</td><td>{{ $contribution->contribution_date->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Method</td><td>{{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}</td></tr>
                    <tr><td class="text-muted">Reference</td><td>{{ $contribution->reference_number ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Recorded By</td><td>{{ $contribution->recorded_by ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Notes</td><td>{{ $contribution->notes ?? '—' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-journal me-2"></i>Journal Entries</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contribution->journalEntries as $entry)
                        <tr>
                            <td><code>{{ $entry->reference }}</code></td>
                            <td>
                                <span class="badge bg-{{ $entry->type === 'debit' ? 'danger' : 'success' }}">
                                    {{ strtoupper($entry->type) }}
                                </span>
                            </td>
                            <td>{{ $entry->description }}</td>
                            <td>UGX {{ number_format($entry->amount) }}</td>
                            <td>{{ $entry->entry_date->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection