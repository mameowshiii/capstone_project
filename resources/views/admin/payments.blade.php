@extends('layouts.app')

@section('title', 'Payments & OR Monitoring')

@section('content')
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
  <!-- Cash Card -->
  <div class="card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px;">
    <div style="background:#eff6ff; color:#2563eb; width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="fas fa-money-bill-wave"></i>
    </div>
    <div>
      <div style="font-size:12px; color:var(--gray);">Cash Revenue</div>
      <h3 style="margin:0; font-size:20px; font-weight:700;">₱{{ number_format($stats['total_cash'], 2) }}</h3>
    </div>
  </div>
  <!-- GCash Card -->
  <div class="card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px;">
    <div style="background:#f0fdf4; color:#16a34a; width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="fas fa-mobile-alt"></i>
    </div>
    <div>
      <div style="font-size:12px; color:var(--gray);">GCash Revenue</div>
      <h3 style="margin:0; font-size:20px; font-weight:700;">₱{{ number_format($stats['total_gcash'], 2) }}</h3>
    </div>
  </div>
  <!-- Maya Card -->
  <div class="card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px;">
    <div style="background:#faf5ff; color:#9333ea; width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="fas fa-wallet"></i>
    </div>
    <div>
      <div style="font-size:12px; color:var(--gray);">Maya Revenue</div>
      <h3 style="margin:0; font-size:20px; font-weight:700;">₱{{ number_format($stats['total_maya'], 2) }}</h3>
    </div>
  </div>
  <!-- Total Revenue Card -->
  <div class="card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px; background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff;">
    <div style="background:rgba(255,255,255,0.2); color:#fff; width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="fas fa-hand-holding-usd"></i>
    </div>
    <div>
      <div style="font-size:12px; opacity:0.8;">Total Revenue</div>
      <h3 style="margin:0; font-size:20px; font-weight:700;">₱{{ number_format($stats['total_paid'], 2) }}</h3>
    </div>
  </div>
  <!-- Pending Card -->
  <div class="card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px;">
    <div style="background:#fff7ed; color:#ea580c; width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="fas fa-clock"></i>
    </div>
    <div>
      <div style="font-size:12px; color:var(--gray);">Pending / Unpaid</div>
      <h3 style="margin:0; font-size:20px; font-weight:700;">₱{{ number_format($stats['total_pending'], 2) }}</h3>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap: wrap; gap:12px;">
    <h5><i class="fas fa-money-bill-wave" style="color:var(--primary);margin-right:8px;"></i>Transactions &amp; OR Log</h5>
    <form method="GET" action="{{ route('admin.payments') }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
      <select name="status" class="form-select" style="width:130px;" onchange="this.form.submit()">
        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
        <option value="waived" {{ $status === 'waived' ? 'selected' : '' }}>Waived</option>
      </select>
      <select name="method" class="form-select" style="width:130px;" onchange="this.form.submit()">
        <option value="all" {{ $method === 'all' ? 'selected' : '' }}>All Methods</option>
        <option value="cash" {{ $method === 'cash' ? 'selected' : '' }}>Cash</option>
        <option value="gcash" {{ $method === 'gcash' ? 'selected' : '' }}>GCash</option>
        <option value="maya" {{ $method === 'maya' ? 'selected' : '' }}>Maya</option>
      </select>
      <input type="text" name="search" class="form-control" placeholder="Search OR # / Tracking #" value="{{ $search }}" style="width:200px;">
      <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
      @if($search || $status !== 'all' || $method !== 'all')
        <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary"><i class="fas fa-sync"></i></a>
      @endif
    </form>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>OR Number</th>
          <th>Tracking #</th>
          <th>Resident</th>
          <th>Document Type</th>
          <th>Amount</th>
          <th>Payment Method</th>
          <th>Paid Date</th>
          <th>Status</th>
          <th>Processed By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if ($payments->isEmpty())
          <tr>
            <td colspan="10" class="text-center" style="padding:40px;">No transaction logs found.</td>
          </tr>
        @else
          @foreach ($payments as $p)
            <tr>
              <td>
                @if($p->receipt_number)
                  <strong style="color:var(--primary);">{{ $p->receipt_number }}</strong>
                @else
                  <span class="text-muted" style="font-size:12px; font-style:italic;">None Assigned</span>
                @endif
              </td>
              <td><code style="font-size:11px;">{{ $p->request->tracking_number ?? 'N/A' }}</code></td>
              <td>{{ $p->request->resident->full_name ?? 'N/A' }}</td>
              <td>{{ $p->request->certificate->name ?? 'N/A' }}</td>
              <td style="font-weight:600;">₱{{ number_format($p->amount, 2) }}</td>
              <td>
                <span style="display:inline-flex; align-items:center; gap:6px;">
                  @if($p->payment_method === 'cash')
                    <i class="fas fa-money-bill-wave" style="color:#2563eb;"></i> Cash
                  @elseif($p->payment_method === 'gcash')
                    <i class="fas fa-mobile-alt" style="color:#16a34a;"></i> GCash
                  @else
                    <i class="fas fa-wallet" style="color:#9333ea;"></i> Maya
                  @endif
                </span>
              </td>
              <td>
                <small>{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('M d, Y h:i A') : '--' }}</small>
              </td>
              <td>
                <span class="badge bg-{{ $p->payment_status === 'paid' ? 'success' : ($p->payment_status === 'waived' ? 'secondary' : 'danger') }}">
                  {{ ucfirst($p->payment_status) }}
                </span>
              </td>
              <td>
                <small class="text-muted">{{ $p->receivedBy->username ?? 'System' }}</small>
              </td>
              <td>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPaymentModal({{ json_encode($p) }})" title="Edit Payment">
                  <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  @if ($payments->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:center;">
      {{ $payments->appends(request()->query())->links('vendor.pagination.simple-default') }}
    </div>
  @endif
</div>

<!-- Edit Payment Modal -->
<div class="modal" id="paymentModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div class="modal-content" style="background:#fff; border-radius:16px; max-width:480px; width:100%; overflow:hidden; box-shadow:var(--shadow-md);">
    <div class="modal-header" style="padding:16px 24px; border-bottom:1px solid #f3f4f6; display:flex; justify-content:between; align-items:center;">
      <h5 style="margin:0; font-weight:600;"><i class="fas fa-edit" style="color:var(--primary); margin-right:8px;"></i>Update Payment details</h5>
      <button type="button" class="btn-close" onclick="closePaymentModal()" style="background:none; border:none; cursor:pointer; font-size:18px; color:var(--gray);">&times;</button>
    </div>
    <form method="POST" action="{{ route('admin.payments.update') }}">
      @csrf
      <input type="hidden" name="payment_id" id="modal_payment_id">
      
      <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Resident Name</label>
          <input type="text" id="modal_resident" class="form-control" readonly style="background:#f9fafb;">
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Tracking Number</label>
          <input type="text" id="modal_tracking" class="form-control" readonly style="background:#f9fafb;">
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Official Receipt (OR) Number</label>
          <input type="text" name="receipt_number" id="modal_receipt" class="form-control" placeholder="e.g. OR-998877">
        </div>

        <div class="grid-2" style="margin:0;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Amount (₱) *</label>
            <input type="number" step="0.01" name="amount" id="modal_amount" class="form-control" required min="0">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Payment Method *</label>
            <select name="payment_method" id="modal_method" class="form-select" required>
              <option value="cash">Cash</option>
              <option value="gcash">GCash</option>
              <option value="maya">Maya</option>
            </select>
          </div>
        </div>

        <div class="form-group" style="margin:0;">
          <label class="form-label">Payment Status *</label>
          <select name="payment_status" id="modal_status" class="form-select" required>
            <option value="unpaid">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="waived">Waived</option>
          </select>
        </div>
      </div>

      <div style="padding:16px 24px; border-top:1px solid #f3f4f6; background:#f9fafb; display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline-secondary" onclick="closePaymentModal()">Cancel</button>
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
  function openPaymentModal(payment) {
    document.getElementById('modal_payment_id').value = payment.id;
    document.getElementById('modal_resident').value = payment.request && payment.request.resident ? payment.request.resident.full_name : 'N/A';
    document.getElementById('modal_tracking').value = payment.request ? payment.request.tracking_number : 'N/A';
    document.getElementById('modal_receipt').value = payment.receipt_number || '';
    document.getElementById('modal_amount').value = payment.amount;
    document.getElementById('modal_method').value = payment.payment_method;
    document.getElementById('modal_status').value = payment.payment_status;

    const modal = document.getElementById('paymentModal');
    modal.style.display = 'flex';
  }

  function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
  }
</script>
@endsection
