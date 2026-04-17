@extends('layouts.member')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>My Contribution History</h4>
    <div class="text-muted small">
        Total: <strong class="text-success">UGX {{ number_format($totalContributed) }}</strong>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $c)
                <tr>
                    <td>{{ $c->contribution_date->format('d M Y') }}</td>
                    <td>{{ $c->project->name }}</td>
                    <td class="fw-bold text-success">UGX {{ number_format($c->amount) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $c->payment_method)) }}</td>
                    <td>{{ $c->reference_number ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No contributions recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contributions->hasPages())
    <div class="card-footer">{{ $contributions->links() }}</div>
    @endif
</div>

@endsection