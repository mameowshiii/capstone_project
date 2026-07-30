@extends('layouts.app')

@section('title', 'My Summons / Blotters')

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fas fa-gavel" style="color:var(--primary);margin-right:8px;"></i>My Registered Case Summons &amp; Blotters</h5>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Case Number</th>
          <th>Type</th>
          <th>Role</th>
          <th>Opposing Party</th>
          <th>Incident Details</th>
          <th>Next Hearing Schedule</th>
          <th>Status</th>
          <th>Hearing History / Remarks</th>
        </tr>
      </thead>
      <tbody>
        @if ($summons->isEmpty())
          <tr>
            <td colspan="8" class="text-center" style="padding:40px;">
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
                <span class="badge bg-{{ $s->case_type === 'summon' ? 'primary' : 'secondary' }}" style="font-size:10px;">
                  {{ ucfirst($s->case_type) }}
                </span>
              </td>
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
                <div style="font-weight:600; font-size:12px;">{{ $s->nature_of_complaint ?? 'Dispute / Incident' }}</div>
                @if($s->incident_location)
                  <div style="font-size:11px; color:var(--gray);">📍 {{ $s->incident_location }}</div>
                @endif
                @if($s->incident_date)
                  <div style="font-size:11px; color:var(--gray);">📅 {{ \Carbon\Carbon::parse($s->incident_date)->format('M d, Y') }}</div>
                @endif
              </td>
              <td>
                @if($s->case_type === 'blotter')
                  <span class="text-muted" style="font-style:italic; font-size:11px;">Record Only (No Hearing)</span>
                @elseif($s->schedule_date)
                  <div style="font-weight:600;">{{ \Carbon\Carbon::parse($s->schedule_date)->format('M d, Y') }}</div>
                  <div style="font-size:11px; color:var(--gray);">{{ \Carbon\Carbon::parse($s->schedule_date)->format('h:i A') }}</div>
                @else
                  <span class="text-muted" style="font-style:italic;">Not Scheduled</span>
                @endif
              </td>
              <td>
                @php
                  $badgeColor = 'warning';
                  if ($s->status === 'amicably_settled') $badgeColor = 'success';
                  elseif ($s->status === 'certified_to_file_action') $badgeColor = 'danger';
                  elseif ($s->status === 'scheduled') $badgeColor = 'info';
                  elseif ($s->status === 'dismissed' || $s->status === 'cancelled') $badgeColor = 'secondary';
                @endphp
                <span class="badge bg-{{ $badgeColor }}" style="font-size: 10px;">
                  {{ ucwords(str_replace('_', ' ', $s->status)) }}
                </span>
              </td>
              <td>
                <div style="font-size:12px; max-width:250px;">
                  @if($s->case_type === 'summon' && $s->hearings->count() > 0)
                    <div style="font-weight:600; color:var(--primary); margin-bottom:4px;">Hearings Timeline ({{ $s->hearings->count() }})</div>
                    <ul style="padding-left:14px; margin:0; line-height:1.4; font-size:11px; color:#555;">
                      @foreach($s->hearings as $h)
                        <li>
                          <strong>Session #{{ $h->hearing_number }}</strong> ({{ $h->schedule_date->format('M d, Y') }}):
                          <span class="text-muted">{{ $h->remarks ?? 'No notes' }}</span>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    @if($s->hearing_remarks)
                      <strong>Remarks:</strong> {{ $s->hearing_remarks }}
                    @else
                      <span class="text-muted" style="font-style:italic;">No remarks yet</span>
                    @endif
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
