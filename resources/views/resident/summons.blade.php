@extends('layouts.app')

@section('title', 'My Summons / Blotters')

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fas fa-gavel" style="color:var(--primary);margin-right:8px;"></i>My Registered Case Summons</h5>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Case Number</th>
          <th>Role</th>
          <th>Opposing Party</th>
          <th>Hearing Schedule</th>
          <th>Details of Incident</th>
          <th>Status</th>
          <th>remarks / Hearing resolution</th>
        </tr>
      </thead>
      <tbody>
        @if ($summons->isEmpty())
          <tr>
            <td colspan="7" class="text-center" style="padding:40px;">
              <div style="font-size:40px;margin-bottom:10px;">🕊️</div>
              <p class="text-muted">You do not have any registered summons or blotters active.</p>
            </td>
          </tr>
        @else
          @foreach ($summons as $s)
            @php
              $isComplainant = (Auth::user()->resident->id == $s->complainant_resident_id);
            @endphp
            <tr>
              <td><strong style="color:var(--primary); font-size:12px;">{{ $s->case_number }}</strong></td>
              <td>
                <span class="badge bg-{{ $isComplainant ? 'primary' : 'danger' }}">
                  {{ $isComplainant ? 'Complainant (You)' : 'Respondent' }}
                </span>
              </td>
              <td>
                <strong>{{ $isComplainant ? $s->respondent_name : $s->complainant_name }}</strong>
                <div style="font-size:11px; color:var(--gray);">
                  Contact: {{ $isComplainant ? ($s->respondent_contact ?? 'N/A') : ($s->complainant_contact ?? 'N/A') }}
                </div>
              </td>
              <td>
                <div style="font-weight:600;">{{ \Carbon\Carbon::parse($s->schedule_date)->format('M d, Y') }}</div>
                <div style="font-size:11px; color:var(--gray);">{{ \Carbon\Carbon::parse($s->schedule_date)->format('h:i A') }}</div>
              </td>
              <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis;" title="{{ $s->complain_details }}">
                {{ $s->complain_details }}
              </td>
              <td>
                <span class="badge bg-{{ $s->status === 'resolved' ? 'success' : ($s->status === 'cancelled' ? 'secondary' : ($s->status === 'scheduled' ? 'info' : 'warning')) }}">
                  {{ ucfirst($s->status) }}
                </span>
              </td>
              <td>
                <div style="font-size:12px; max-width:200px;">
                  @if($s->hearing_remarks)
                    {{ $s->hearing_remarks }}
                  @else
                    <span class="text-muted" style="font-style:italic;">No remarks yet</span>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  @if ($summons->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:center;">
      {{ $summons->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>
@endsection
