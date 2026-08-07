@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fas fa-history" style="color:var(--primary);margin-right:8px;"></i>
      System Audit Logs <span style="font-size:13px;font-weight:400;color:#6b7280;">({{ $logs->total() }})</span></h5>
  </div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>User</th>
          <th>Action</th>
          <th>Module</th>
          <th>Description</th>
          <th>IP Address</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody>
        @if ($logs->isEmpty())
          <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">No system audit logs found.</td></tr>
        @else
          @foreach ($logs as $log)
            <tr>
              <td style="font-weight:600;">{{ $log->user ? $log->user->username : 'System' }}</td>
              <td><code>{{ $log->action }}</code></td>
              <td><span class="badge bg-secondary">{{ $log->module ?? 'General' }}</span></td>
              <td>{{ $log->description }}</td>
              <td><small>{{ $log->ip_address }}</small></td>
              <td><small>{{ $log->created_at->format('M d, Y h:i A') }}</small></td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  @if ($logs->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:center;">
      {{ $logs->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>
@endsection
