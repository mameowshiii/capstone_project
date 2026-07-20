@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;margin:0 auto;">

  <!-- Profile Card -->
  <div class="card" style="grid-column:span 2;">
    <div style="padding:28px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
      <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));
                  border:4px solid #fff;display:flex;align-items:center;justify-content:center;
                  font-size:32px;font-weight:800;color:#fff;box-shadow: 0 4px 6px rgba(0,0,0,0.15); overflow:hidden; flex-shrink: 0;">
        @if ($resident->photo)
          <img src="{{ asset('assets/uploads/' . $resident->photo) }}" alt="Profile photo" style="width:100%;height:100%;object-fit:cover;">
        @else
          {{ strtoupper(substr($resident->first_name, 0, 1)) }}
        @endif
      </div>
      <div>
        <div style="font-size:22px;font-weight:800;">{{ $resident->full_name }}</div>
        <div style="color:#6b7280;font-size:14px;margin-bottom:8px;">@{{ Auth::user()->username }}</div>
        <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:#374151;">
          <span><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:4px;"></i>{{ $resident->address }}</span>
          <span><i class="fas fa-phone" style="color:var(--primary);margin-right:4px;"></i>{{ $resident->contact_number ?? '—' }}</span>
          <span><i class="fas fa-birthday-cake" style="color:var(--primary);margin-right:4px;"></i>{{ \Carbon\Carbon::parse($resident->birthdate)->format('F d, Y') }}</span>
          <span><i class="fas fa-user" style="color:var(--primary);margin-right:4px;"></i>{{ $resident->age }} yrs old</span>
        </div>
      </div>
    </div>
  </div>

  <div class="card" style="grid-column:span 2;">
    <div class="card-header"><h5><i class="fas fa-camera" style="color:var(--primary);margin-right:8px;"></i>Profile Photo</h5></div>
    <div class="card-body">
      <form method="POST" action="{{ route('resident.profile') }}" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
        @csrf
        <input type="hidden" name="action" value="update_photo">
        <div style="flex:1;min-width:220px;">
          <label class="form-label">Upload New Photo</label>
          <input type="file" name="photo" class="form-control" accept="image/*" required>
        </div>
        <button class="btn btn-primary"><i class="fas fa-upload"></i> Update Photo</button>
      </form>
      @if ($resident->photo)
      <form method="POST" action="{{ route('resident.profile') }}" style="margin-top:10px;">
        @csrf
        <input type="hidden" name="action" value="remove_photo">
        <button class="btn btn-outline-secondary btn-sm" onclick="return confirm('Remove profile photo?')">
          <i class="fas fa-trash"></i> Remove Photo
        </button>
      </form>
      @endif
    </div>
  </div>

  <!-- Update Info Form -->
  <div class="card">
    <div class="card-header"><h5><i class="fas fa-user-edit" style="color:var(--primary);margin-right:8px;"></i>Edit Profile</h5></div>
    <div class="card-body">
      <form method="POST" action="{{ route('resident.profile') }}">
        @csrf
        <input type="hidden" name="action" value="update_profile">
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $resident->first_name) }}" pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
          </div>
          <div class="form-group">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required value="{{ old('last_name', $resident->last_name) }}" pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Middle Name</label>
          <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $resident->middle_name) }}" pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
        </div>
        <div class="form-group">
          <label class="form-label">Contact Number</label>
          <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $resident->contact_number) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" required value="{{ old('address', $resident->address) }}">
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Purok</label>
            <input type="text" name="purok" class="form-control" value="{{ old('purok', $resident->purok) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Occupation</label>
            <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $resident->occupation) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Civil Status</label>
            <select name="civil_status" class="form-select">
              @foreach (['Single','Married','Widowed','Separated'] as $cs)
              <option {{ old('civil_status', $resident->civil_status) === $cs ? 'selected' : '' }}>{{ $cs }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Voter Status</label>
            <select name="voter_status" class="form-select">
              @foreach (['Not Registered','Registered'] as $vs)
              <option {{ old('voter_status', $resident->voter_status) === $vs ? 'selected' : '' }}>{{ $vs }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Update Profile</button>
      </form>
    </div>
  </div>

  <!-- Change Password -->
  <div class="card">
    <div class="card-header"><h5><i class="fas fa-lock" style="color:var(--primary);margin-right:8px;"></i>Change Password</h5></div>
    <div class="card-body">
      <form method="POST" action="{{ route('resident.profile') }}">
        @csrf
        <input type="hidden" name="action" value="change_password">
        <div class="form-group">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="form-group">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-warning w-100"><i class="fas fa-key"></i> Change Password</button>
      </form>
    </div>
  </div>

</div>
@endsection
