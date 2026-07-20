<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Barangay Pili — Clearance & Certificate System</title>
  <meta name="description" content="Official online portal for Barangay Pili clearance and certificate requests.">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="auth-page">
    <div class="auth-container">

      <!-- Left panel -->
      <div class="auth-left">
        <div class="brgy-seal" style="text-align: left; margin-bottom: 24px;">
          <img src="{{ asset('assets/images/pili_logo.png') }}" alt="Barangay Logo"
            style="width: 120px; height: auto; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
        </div>

        <h1>Barangay Pili<br>Streamlined Clearance and Certificate </h1>
        <p>Your one-stop portal for barangay clearances and certificates.</p>
        <div class="feature-list">
          <div class="feature-item"><i class="fas fa-file-shield"></i> Barangay Clearance</div>
          <div class="feature-item"><i class="fas fa-certificate"></i> Various Certificates</div>
          <div class="feature-item"><i class="fas fa-clock"></i> Real-Time Status Tracking</div>
          <div class="feature-item"><i class="fas fa-print"></i> Print Documents</div>
          <div class="feature-item"><i class="fas fa-lock"></i> Secure &amp; Private</div>
        </div>
        <div style="margin-top:32px;padding-top:20px;border-top:1px solid rgba(255,255,255,.2);font-size:12px;opacity:.6;">
          Barangay Pili, Madridejos ,Cebu &bull; v1.0.0
        </div>
      </div>

      <!-- Right panel -->
      <div class="auth-right">
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your account or register as a new resident.</p>

        <!-- Tabs -->
        <div class="auth-tabs">
          <button class="auth-tab {{ !session('errors') && !session('reg_tab') ? 'active' : '' }}" onclick="switchTab('tab-login',this)" id="btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In
          </button>
          <button class="auth-tab {{ session('reg_tab') || ($errors->any() && old('username') === null) ? 'active' : '' }}" onclick="switchTab('tab-register',this)" id="btn-register">
            <i class="fas fa-user-plus"></i> Register
          </button>
        </div>

        @if (session('error'))
          <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if (session('success'))
          <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 20px;">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Login Tab -->
        <div id="tab-login" class="tab-panel" style="display:{{ !session('reg_tab') && !($errors->any() && old('first_name')) ? 'block' : 'none' }};">
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
              <label class="form-label" for="username">Username or Email</label>
              <input type="text" id="username" name="username" class="form-control"
                placeholder="Enter your username or email" required autocomplete="username" value="{{ old('username') }}">
            </div>
            <div class="form-group">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                <a href="{{ route('password.request') }}" style="font-size: 13px;">Forgot Password?</a>
              </div>
              <div style="position:relative; margin-top: 6px;">
                <input type="password" id="password" name="password" class="form-control"
                  placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" onclick="togglePw('password')"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;">
                  <i class="fas fa-eye" id="pw-icon"></i>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="margin-top:8px;">
              <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
          </form>
          <div style="margin-top: 16px; text-align: center;">
            <a href="{{ route('track') }}" class="btn btn-outline-secondary btn-sm" style="display:inline-flex; align-items:center; gap:8px;">
              <i class="fas fa-search-location"></i> Track Request Status
            </a>
          </div>
        </div>

        <!-- Register Tab -->
        <div id="tab-register" class="tab-panel" style="display:{{ session('reg_tab') || ($errors->any() && old('first_name')) ? 'block' : 'none' }};">
          <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" required placeholder="Juan"
                  value="{{ old('first_name') }}"
                  pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed"
                  oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
              </div>
              <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" required placeholder="Dela Cruz"
                  value="{{ old('last_name') }}"
                  pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed"
                  oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control" placeholder="Optional"
                value="{{ old('middle_name') }}"
                pattern="[A-Za-z\s\-\.,]+" title="Only letters, spaces, hyphens, and periods are allowed"
                oninput="this.value = this.value.replace(/[^A-Za-z\s\-\.,]/g, '')">
            </div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Gender *</label>
                <select name="gender" class="form-select" required>
                  <option value="">Select</option>
                  <option {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                  <option {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                  <option {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Birthdate *</label>
                <input type="date" name="birthdate" class="form-control" min="1900-01-01" max="{{ date('Y-m-d') }}"
                  value="{{ old('birthdate') }}" required>
              </div>

              <div class="form-group">
                <label class="form-label">Civil Status *</label>
                <select name="civil_status" class="form-select" required>
                  <option value="">Select</option>
                  <option {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                  <option {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                  <option {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                  <option {{ old('civil_status') === 'Separated' ? 'selected' : '' }}>Separated</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('contact_number') }}">
              </div>

              <div class="form-group">
                <label class="form-label">Purok</label>
                <input type="text" name="purok" class="form-control" placeholder="e.g. Purok 1" value="{{ old('purok') }}">
              </div>
              <div class="form-group">
                <label class="form-label">Years of Residency</label>
                <input type="number" name="years_of_residency" class="form-control" min="0" value="{{ old('years_of_residency', 0) }}">
              </div>

              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" required placeholder="you@email.com" value="{{ old('email') }}">
              </div>

              <div class="form-group">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required placeholder="username" value="{{ old('username') }}">
              </div>
              <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required placeholder="Min 6 characters" minlength="6">
              </div>

              <button type="submit" class="btn btn-primary w-100" style="grid-column: span 2; margin-top: 8px;">
                <i class="fas fa-user-plus"></i> Create Account
              </button>
            </div>
          </form>
        </div>

      </div><!-- /.auth-right -->
    </div><!-- /.auth-container -->
  </div><!-- /.auth-page -->

  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function togglePw(id) {
      const el = document.getElementById(id);
      const icon = document.getElementById('pw-icon');
      if (el.type === 'password') { el.type = 'text'; icon.className = 'fas fa-eye-slash'; }
      else { el.type = 'password'; icon.className = 'fas fa-eye'; }
    }
  </script>
</body>
</html>
