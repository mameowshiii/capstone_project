@extends('layouts.app')

@section('title', 'Summon & Blotter Management')

@section('content')
<div class="card">
  <div class="card-header" style="flex-wrap:wrap; gap:12px;">
    <h5><i class="fas fa-gavel" style="color:var(--primary);margin-right:8px;"></i>Barangay Summons ({{ $summons->total() }})</h5>
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
      <form method="GET" action="{{ route('admin.summons') }}" style="display:flex; gap:8px; align-items:center;">
        <select name="status" class="form-select" style="width:130px;" onchange="this.form.submit()">
          <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
          <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
          <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
          <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <input type="text" name="search" class="form-control" placeholder="Search case / names..." value="{{ $search }}" style="width:200px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        @if($search || $status !== 'all')
          <a href="{{ route('admin.summons') }}" class="btn btn-outline-secondary"><i class="fas fa-sync"></i></a>
        @endif
      </form>
      <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> File New Summon
      </button>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Case Number</th>
          <th>Complainant</th>
          <th>Respondent</th>
          <th>Hearing Schedule</th>
          <th>Details</th>
          <th>Status</th>
          <th>Remarks</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if ($summons->isEmpty())
          <tr>
            <td colspan="8" class="text-center" style="padding:40px;">No summon cases registered yet.</td>
          </tr>
        @else
          @foreach ($summons as $s)
            <tr>
              <td><strong style="color:var(--primary); font-size:12px;">{{ $s->case_number }}</strong></td>
              <td>
                <div><strong>{{ $s->complainant_name }}</strong></div>
                @if($s->complainantResident)
                  <span class="badge bg-success" style="font-size:10px;">Registered Resident</span>
                @endif
                <div style="font-size:11px; color:var(--gray);">{{ $s->complainant_contact ?? 'No Contact' }}</div>
              </td>
              <td>
                <div><strong>{{ $s->respondent_name }}</strong></div>
                @if($s->respondentResident)
                  <span class="badge bg-success" style="font-size:10px;">Registered Resident</span>
                @endif
                <div style="font-size:11px; color:var(--gray);">{{ $s->respondent_contact ?? 'No Contact' }}</div>
              </td>
              <td>
                <div style="font-weight:600;">{{ \Carbon\Carbon::parse($s->schedule_date)->format('M d, Y') }}</div>
                <div style="font-size:11px; color:var(--gray);">{{ \Carbon\Carbon::parse($s->schedule_date)->format('h:i A') }}</div>
              </td>
              <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $s->complain_details }}">
                {{ $s->complain_details }}
              </td>
              <td>
                <span class="badge bg-{{ $s->status === 'resolved' ? 'success' : ($s->status === 'cancelled' ? 'secondary' : ($s->status === 'scheduled' ? 'info' : 'warning')) }}">
                  {{ ucfirst($s->status) }}
                </span>
              </td>
              <td style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $s->hearing_remarks ?? 'None' }}">
                {{ $s->hearing_remarks ?? '--' }}
              </td>
              <td>
                <div style="display:flex; gap:4px;">
                  <button type="button" class="btn btn-outline-primary btn-sm" onclick="openEditModal({{ json_encode($s) }})" title="Edit Summon">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="{{ route('admin.summons.delete', $s->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this summon case permanentely?')" title="Delete">
                    <i class="fas fa-trash"></i>
                  </a>
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
      {{ $summons->appends(request()->query())->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>

<!-- Create Summon Modal -->
<div class="modal" id="createModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px; overflow-y:auto;">
  <div class="modal-content" style="background:#fff; border-radius:16px; max-width:600px; width:100%; overflow:hidden; box-shadow:var(--shadow-md); margin-top: 40px; margin-bottom: 40px;">
    <div class="modal-header" style="padding:16px 24px; border-bottom:1px solid #f3f4f6; display:flex; justify-content:between; align-items:center;">
      <h5 style="margin:0; font-weight:600;"><i class="fas fa-plus" style="color:var(--primary); margin-right:8px;"></i>File Blotter / Summon Case</h5>
      <button type="button" class="btn-close" onclick="closeCreateModal()" style="background:none; border:none; cursor:pointer; font-size:18px; color:var(--gray);">&times;</button>
    </div>
    <form method="POST" action="{{ route('admin.summons.store') }}">
      @csrf
      
      <div style="padding:24px; display:flex; flex-direction:column; gap:16px; max-height:calc(100vh - 200px); overflow-y:auto;">
        <!-- Complainant -->
        <h6 style="margin:0; font-weight:700; color:var(--primary); border-bottom:1px solid #f3f4f6; padding-bottom:6px;">Complainant Details (Declarant)</h6>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Link Registered Resident (Optional)</label>
          <select name="complainant_resident_id" id="complainant_resident_select" class="form-select" onchange="autoFillComplainant(this)">
            <option value="">-- Choose Resident --</option>
            @foreach($residents as $res)
              <option value="{{ $res->id }}" data-name="{{ $res->full_name }}" data-contact="{{ $res->contact_number }}">{{ $res->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid-2" style="margin:0;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Complainant Full Name *</label>
            <input type="text" name="complainant_name" id="complainant_name" class="form-control" required placeholder="Full Name">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Contact Number</label>
            <input type="text" name="complainant_contact" id="complainant_contact" class="form-control" placeholder="Contact number">
          </div>
        </div>

        <!-- Respondent -->
        <h6 style="margin:0; font-weight:700; color:var(--primary); border-bottom:1px solid #f3f4f6; padding-bottom:6px;">Respondent Details (Accused)</h6>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Link Registered Resident (Optional)</label>
          <select name="respondent_resident_id" id="respondent_resident_select" class="form-select" onchange="autoFillRespondent(this)">
            <option value="">-- Choose Resident --</option>
            @foreach($residents as $res)
              <option value="{{ $res->id }}" data-name="{{ $res->full_name }}" data-contact="{{ $res->contact_number }}">{{ $res->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid-2" style="margin:0;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Respondent Full Name *</label>
            <input type="text" name="respondent_name" id="respondent_name" class="form-control" required placeholder="Full Name">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Contact Number</label>
            <input type="text" name="respondent_contact" id="respondent_contact" class="form-control" placeholder="Contact number">
          </div>
        </div>

        <!-- Case details -->
        <h6 style="margin:0; font-weight:700; color:var(--primary); border-bottom:1px solid #f3f4f6; padding-bottom:6px;">Hearing &amp; Complaint Details</h6>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Hearing Schedule Date &amp; Time *</label>
          <input type="datetime-local" name="schedule_date" class="form-control" required min="{{ date('Y-m-d\TH:i') }}">
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Complaint Details *</label>
          <textarea name="complain_details" class="form-control" rows="4" required placeholder="Specify details of the complaint / incident..."></textarea>
        </div>
      </div>

      <div style="padding:16px 24px; border-top:1px solid #f3f4f6; background:#f9fafb; display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline-secondary" onclick="closeCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> File Case</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Summon Modal -->
<div class="modal" id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div class="modal-content" style="background:#fff; border-radius:16px; max-width:480px; width:100%; overflow:hidden; box-shadow:var(--shadow-md);">
    <div class="modal-header" style="padding:16px 24px; border-bottom:1px solid #f3f4f6; display:flex; justify-content:between; align-items:center;">
      <h5 style="margin:0; font-weight:600;"><i class="fas fa-edit" style="color:var(--primary); margin-right:8px;"></i>Update Summon Case</h5>
      <button type="button" class="btn-close" onclick="closeEditModal()" style="background:none; border:none; cursor:pointer; font-size:18px; color:var(--gray);">&times;</button>
    </div>
    <form method="POST" action="{{ route('admin.summons.update') }}">
      @csrf
      <input type="hidden" name="summon_id" id="modal_summon_id">
      
      <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Case Number</label>
          <input type="text" id="modal_case_number" class="form-control" readonly style="background:#f9fafb;">
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Hearing Schedule Date &amp; Time *</label>
          <input type="datetime-local" name="schedule_date" id="modal_schedule_date" class="form-control" required>
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Case Status *</label>
          <select name="status" id="modal_status" class="form-select" required>
            <option value="pending">Pending</option>
            <option value="scheduled">Scheduled</option>
            <option value="resolved">Resolved</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Hearing Minutes / Remarks</label>
          <textarea name="hearing_remarks" id="modal_remarks" class="form-control" rows="3" placeholder="Hearing resolution details, notes, etc."></textarea>
        </div>
      </div>

      <div style="padding:16px 24px; border-top:1px solid #f3f4f6; background:#f9fafb; display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

@section('styles')
<style>
  .grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  @media(max-width:480px){
    .grid-2 { grid-template-columns: 1fr; }
  }
</style>
@endsection

<script>
  function autoFillComplainant(select) {
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
      document.getElementById('complainant_name').value = opt.getAttribute('data-name');
      document.getElementById('complainant_contact').value = opt.getAttribute('data-contact') || '';
    } else {
      document.getElementById('complainant_name').value = '';
      document.getElementById('complainant_contact').value = '';
    }
  }

  function autoFillRespondent(select) {
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
      document.getElementById('respondent_name').value = opt.getAttribute('data-name');
      document.getElementById('respondent_contact').value = opt.getAttribute('data-contact') || '';
    } else {
      document.getElementById('respondent_name').value = '';
      document.getElementById('respondent_contact').value = '';
    }
  }

  function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
  }
  function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
  }

  function openEditModal(summon) {
    document.getElementById('modal_summon_id').value = summon.id;
    document.getElementById('modal_case_number').value = summon.case_number;
    
    // format datetime-local input compatibility (YYYY-MM-DDTHH:MM)
    if(summon.schedule_date) {
      const d = new Date(summon.schedule_date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      const hours = String(d.getHours()).padStart(2, '0');
      const minutes = String(d.getMinutes()).padStart(2, '0');
      document.getElementById('modal_schedule_date').value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    document.getElementById('modal_status').value = summon.status;
    document.getElementById('modal_remarks').value = summon.hearing_remarks || '';

    document.getElementById('editModal').style.display = 'flex';
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }
</script>
@endsection
