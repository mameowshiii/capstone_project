@extends('layouts.app')

@section('title', 'Barangay Announcements')

@section('content')
<div style="margin-bottom:24px;">
  <h2 style="font-size:24px; font-weight:800; color:var(--dark); margin:0;">Barangay Public Bulletin</h2>
  <p class="text-muted" style="margin:4px 0 0;">Stay updated with the latest events, advisories, and activities in Barangay Pili.</p>
</div>

<!-- Announcements list -->
<div style="display:flex; flex-direction:column; gap:20px;">
  @if($bulletins->isEmpty())
    <div class="card" style="padding:40px; text-align:center;">
      <div style="font-size:40px; margin-bottom:12px;">📢</div>
      <p class="text-muted" style="margin:0;">No announcements posted at this time.</p>
    </div>
  @else
    @foreach($bulletins as $b)
      <div class="card" style="margin:0; padding:24px; border-left: 6px solid {{ $b->is_pinned ? '#eab308' : ($b->category === 'Advisory' ? 'var(--primary)' : ($b->category === 'Event' ? '#2563eb' : '#0d9488')) }}; position:relative; box-shadow:var(--shadow);">
        
        @if($b->is_pinned)
          <div style="position:absolute; top:20px; right:24px; color:#eab308; font-size:12px; font-weight:700; display:flex; align-items:center; gap:4px; background:#fef9c3; padding:4px 8px; border-radius:6px;">
            <i class="fas fa-thumbtack"></i> PINNED NOTICE
          </div>
        @else
          <div style="position:absolute; top:20px; right:24px; color:var(--gray); font-size:11px;">
            <span class="badge bg-{{ $b->category === 'Advisory' ? 'danger' : ($b->category === 'Event' ? 'primary' : ($b->category === 'Meeting' ? 'info' : 'secondary')) }}">
              {{ $b->category }}
            </span>
          </div>
        @endif

        <h3 style="font-size:18px; font-weight:700; color:var(--dark); margin:0 0 8px; max-width:80%;">{{ $b->title }}</h3>
        
        <div style="font-size:12px; color:var(--gray); display:flex; align-items:center; gap:16px; margin-bottom:16px;">
          <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($b->published_at)->format('M d, Y h:i A') }}</span>
          <span><i class="fas fa-user-edit"></i> {{ $b->creator->username ?? 'Official' }}</span>
        </div>

        <div style="font-size:14px; line-height:1.7; color:#374151; white-space:pre-line;">
          {{ $b->content }}
        </div>
      </div>
    @endforeach
  @endif
</div>

@if ($bulletins->hasPages())
  <div style="margin-top:24px; display:flex; justify-content:center;">
    {{ $bulletins->links('vendor.pagination.simple-default') }}
  </div>
@endif
@endsection
