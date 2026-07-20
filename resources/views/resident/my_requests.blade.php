@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fas fa-list" style="color:var(--primary);margin-right:8px;"></i>My Requests ({{ $requests->total() }})</h5>
    <a href="{{ route('resident.request') }}" class="btn btn-primary btn-sm">
      <i class="fas fa-plus"></i> New Request
    </a>
  </div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Tracking #</th>
          <th>Document</th>
          <th>Purpose</th>
          <th>Fee</th>
          <th>Payment</th>
          <th>Filed</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if ($requests->isEmpty())
          <tr>
            <td colspan="8" class="text-center" style="padding:40px;">
              <div style="font-size:40px;margin-bottom:10px;">📭</div>
              <p class="text-muted">No requests yet.</p>
              <a href="{{ route('resident.request') }}" class="btn btn-primary" style="margin-top:8px;">
                <i class="fas fa-plus"></i> Make a Request
              </a>
            </td>
          </tr>
        @else
          @foreach ($requests as $r)
            <tr>
              <td><code style="font-size:11px;">{{ $r->tracking_number }}</code></td>
              <td style="font-weight:600;">{{ $r->certificate->name }}</td>
              <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                title="{{ $r->purpose }}">{{ $r->purpose }}</td>
              <td>
                @if ($r->certificate->fee > 0)
                  ₱{{ number_format($r->certificate->fee, 2) }}
                @else
                  <span class="badge bg-success">FREE</span>
                @endif
              </td>
              <td>
                <span class="badge bg-{{ ($r->payment->payment_status ?? 'unpaid') === 'paid' ? 'success' : (($r->payment->payment_status ?? 'unpaid') === 'waived' ? 'secondary' : 'danger') }}">
                    {{ ucfirst($r->payment->payment_status ?? 'unpaid') }}
                </span>
              </td>
              <td><small>{{ \Carbon\Carbon::parse($r->requested_at)->format('M d, Y h:i A') }}</small></td>
              <td>
                <span class="badge bg-{{ $r->status === 'pending' ? 'warning' : ($r->status === 'processing' ? 'info' : ($r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'primary'))) }}">
                    {{ ucfirst($r->status) }}
                </span>
              </td>
              <td>
                <div style="display:flex;gap:4px;">
                  <a href="{{ route('track', ['tracking' => $r->tracking_number]) }}"
                    class="btn btn-outline-secondary btn-sm" target="_blank" title="Track">
                    <i class="fas fa-search-location"></i>
                  </a>
                </div>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  @if ($requests->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:center;">
      {{ $requests->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>
@endsection
