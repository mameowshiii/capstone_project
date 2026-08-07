@extends('layouts.app')

@section('title', 'My Requests')

@section('styles')
<style>
/* ── My Requests Mobile Cards ─────────────────────────── */
.requests-table-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* Mobile card layout */
@media (max-width: 640px) {
  .requests-table-wrap { display: none; }

  .request-cards { display: flex; flex-direction: column; gap: 12px; padding: 0; }

  .request-card {
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
  }

  .request-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
  }

  .request-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #161616;
    line-height: 1.3;
  }

  .request-card-track {
    font-size: 10px;
    color: #6b7280;
    font-family: monospace;
    margin-top: 2px;
  }

  .request-card-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 12px;
    margin-bottom: 12px;
  }

  .request-card-meta-item label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #9ca3af;
    margin-bottom: 2px;
  }

  .request-card-meta-item span {
    font-size: 13px;
    color: #374151;
    font-weight: 500;
  }

  .request-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid #f3f4f6;
    gap: 8px;
  }

  .request-card-date {
    font-size: 11px;
    color: #9ca3af;
  }
}

@media (min-width: 641px) {
  .request-cards { display: none; }
}
</style>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fas fa-list" style="color:var(--primary);margin-right:8px;"></i>My Requests ({{ $requests->total() }})</h5>
    <a href="{{ route('resident.request') }}" class="btn btn-primary btn-sm">
      <i class="fas fa-plus"></i> New Request
    </a>
  </div>

  {{-- ── Desktop table ──────────────────────────────────── --}}
  <div class="requests-table-wrap">
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

  {{-- ── Mobile cards ───────────────────────────────────── --}}
  <div class="request-cards" style="padding:12px;">
    @if ($requests->isEmpty())
      <div style="text-align:center;padding:40px 16px;">
        <div style="font-size:48px;margin-bottom:12px;">📭</div>
        <p class="text-muted" style="margin-bottom:16px;">No requests yet.</p>
        <a href="{{ route('resident.request') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Make a Request
        </a>
      </div>
    @else
      @foreach ($requests as $r)
        <div class="request-card">
          <div class="request-card-header">
            <div>
              <div class="request-card-title">{{ $r->certificate->name }}</div>
              <div class="request-card-track">{{ $r->tracking_number }}</div>
            </div>
            <span class="badge bg-{{ $r->status === 'pending' ? 'warning' : ($r->status === 'processing' ? 'info' : ($r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'primary'))) }}" style="flex-shrink:0;">
              {{ ucfirst($r->status) }}
            </span>
          </div>

          <div class="request-card-meta">
            <div class="request-card-meta-item">
              <label>Purpose</label>
              <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:140px;" title="{{ $r->purpose }}">{{ $r->purpose }}</span>
            </div>
            <div class="request-card-meta-item">
              <label>Fee</label>
              <span>
                @if ($r->certificate->fee > 0)
                  ₱{{ number_format($r->certificate->fee, 2) }}
                @else
                  <span class="badge bg-success">FREE</span>
                @endif
              </span>
            </div>
            <div class="request-card-meta-item">
              <label>Payment</label>
              <span class="badge bg-{{ ($r->payment->payment_status ?? 'unpaid') === 'paid' ? 'success' : (($r->payment->payment_status ?? 'unpaid') === 'waived' ? 'secondary' : 'danger') }}">
                {{ ucfirst($r->payment->payment_status ?? 'unpaid') }}
              </span>
            </div>
            <div class="request-card-meta-item">
              <label>Filed</label>
              <span style="font-size:11px;">{{ \Carbon\Carbon::parse($r->requested_at)->format('M d, Y') }}</span>
            </div>
          </div>

          <div class="request-card-footer">
            <span class="request-card-date">{{ \Carbon\Carbon::parse($r->requested_at)->format('h:i A') }}</span>
            <a href="{{ route('track', ['tracking' => $r->tracking_number]) }}"
              class="btn btn-outline-secondary btn-sm" target="_blank">
              <i class="fas fa-search-location"></i> Track
            </a>
          </div>
        </div>
      @endforeach
    @endif
  </div>

  @if ($requests->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:center;">
      {{ $requests->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>
@endsection
