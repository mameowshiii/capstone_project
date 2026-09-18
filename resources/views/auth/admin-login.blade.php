<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal Login &mdash; Barangay Pili</title>
  <meta name="description" content="Secure administrative authentication for Barangay Pili Officials and Staff.">
  <link rel="icon" type="image/png" href="{{ asset('assets/images/pili_logo.png') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    :root {
      --admin-bg: #0b1329;
      --admin-surface: #131f3f;
      --admin-surface-light: #1a2952;
      --admin-primary: #2563eb;
      --admin-primary-hover: #1d4ed8;
      --admin-accent: #f59e0b;
      --admin-border: rgba(255, 255, 255, 0.12);
      --admin-text-main: #f8fafc;
      --admin-text-muted: #94a3b8;
    }

    body.admin-auth-body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0b1329 100%);
      font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--admin-text-main);
      padding: 24px;
      box-sizing: border-box;
    }

    .admin-auth-card {
      width: 100%;
      max-width: 460px;
      background: rgba(19, 31, 63, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--admin-border);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
      overflow: hidden;
      position: relative;
    }

    .admin-card-header {
      padding: 36px 32px 24px;
      text-align: center;
      position: relative;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(180deg, rgba(37, 99, 235, 0.1) 0%, transparent 100%);
    }

    .admin-seal {
      width: 86px;
      height: 86px;
      margin: 0 auto 16px;
      background: rgba(255, 255, 255, 0.06);
      border-radius: 50%;
      padding: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid rgba(245, 158, 11, 0.4);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .admin-seal img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .admin-domain-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid rgba(245, 158, 11, 0.5);
      color: #fbbf24;
      font-size: 11.5px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 12px;
    }

    .admin-domain-badge i {
      font-size: 10px;
    }

    .admin-card-header h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.3px;
      color: #ffffff;
    }

    .admin-card-header p {
      margin: 8px 0 0;
      font-size: 13.5px;
      color: var(--admin-text-muted);
    }

    .admin-card-body {
      padding: 28px 32px 32px;
    }

    .admin-form-group {
      margin-bottom: 20px;
    }

    .admin-form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 8px;
    }

    .admin-input-wrapper {
      position: relative;
    }

    .admin-input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-size: 14px;
      transition: color 0.2s;
    }

    .admin-input {
      width: 100%;
      box-sizing: border-box;
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      padding: 12px 14px 12px 42px;
      color: #ffffff;
      font-size: 14px;
      outline: none;
      transition: all 0.2s;
    }

    .admin-input:focus {
      border-color: var(--admin-primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
      background: rgba(15, 23, 42, 1);
    }

    .admin-input:focus + .admin-input-icon {
      color: #60a5fa;
    }

    .admin-input-toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #64748b;
      font-size: 14px;
      padding: 4px;
    }

    .admin-input-toggle-pw:hover {
      color: #94a3b8;
    }

    .recaptcha-wrapper {
      margin: 22px 0;
      display: flex;
      justify-content: center;
    }

    .admin-btn-submit {
      width: 100%;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      padding: 13px 20px;
      font-size: 14.5px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
      transition: all 0.2s;
    }

    .admin-btn-submit:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.6);
      transform: translateY(-1px);
    }

    .admin-btn-submit:active {
      transform: translateY(0);
    }

    .admin-security-badges {
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      display: flex;
      justify-content: space-around;
      font-size: 11.5px;
      color: #94a3b8;
    }

    .admin-badge-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .admin-badge-item i {
      color: #10b981;
    }

    .admin-resident-link {
      margin-top: 20px;
      text-align: center;
      font-size: 13px;
    }

    .admin-resident-link a {
      color: #60a5fa;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .admin-resident-link a:hover {
      color: #93c5fd;
      text-decoration: underline;
    }

    .alert-custom {
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13.5px;
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .alert-danger-custom {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
    }

    .alert-success-custom {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #6ee7b7;
    }

    .alert-info-custom {
      background: rgba(59, 130, 246, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.4);
      color: #93c5fd;
    }
  </style>
</head>
<body class="admin-auth-body">

  <div class="admin-auth-card">
    <div class="admin-card-header">
      <div class="admin-seal">
        <img src="{{ asset('assets/images/pili_logo.png') }}" alt="Barangay Pili Seal">
      </div>
      <div class="admin-domain-badge">
        <i class="fas fa-shield-halved"></i> admin.brgypilieclearance.com
      </div>
      <h1>Barangay Pili</h1>
      <p>Administrative Management Portal</p>
    </div>

    <div class="admin-card-body">

      @if (session('error'))
        <div class="alert-custom alert-danger-custom">
          <i class="fas fa-circle-exclamation" style="margin-top: 2px;"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if (session('success'))
        <div class="alert-custom alert-success-custom">
          <i class="fas fa-circle-check" style="margin-top: 2px;"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if (session('info'))
        <div class="alert-custom alert-info-custom">
          <i class="fas fa-circle-info" style="margin-top: 2px;"></i>
          <div>{{ session('info') }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert-custom alert-danger-custom">
          <i class="fas fa-circle-exclamation" style="margin-top: 2px;"></i>
          <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="admin-form-group">
          <label class="admin-form-label" for="email">Admin Email Address</label>
          <div class="admin-input-wrapper">
            <input type="email" id="email" name="email" class="admin-input"
              placeholder="admin@brgypilieclearance.com" required autocomplete="email"
              value="{{ old('email') }}" autofocus>
            <i class="fas fa-user-shield admin-input-icon"></i>
          </div>
        </div>

        <div class="admin-form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="admin-form-label" for="password" style="margin-bottom: 0;">Password</label>
            <a href="{{ route('password.request') }}" style="font-size: 12px; color: #94a3b8; text-decoration: none;">Forgot?</a>
          </div>
          <div class="admin-input-wrapper">
            <input type="password" id="password" name="password" class="admin-input"
              placeholder="Enter your administrative password" required autocomplete="current-password">
            <i class="fas fa-lock admin-input-icon"></i>
            <button type="button" class="admin-input-toggle-pw" onclick="toggleAdminPw()">
              <i class="fas fa-eye" id="admin-pw-icon"></i>
            </button>
          </div>
        </div>

        <!-- Google reCAPTCHA v2 Widget -->
        <div class="recaptcha-wrapper">
          <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
        </div>

        <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #cbd5e1;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="remember" style="accent-color: #2563eb;">
            <span>Keep me verified on this device</span>
          </label>
        </div>

        <button type="submit" class="admin-btn-submit">
          <i class="fas fa-key"></i> Proceed to Security OTP Verification
        </button>
      </form>

      <div class="admin-security-badges">
        <div class="admin-badge-item">
          <i class="fas fa-shield-check"></i> Google reCAPTCHA
        </div>
        <div class="admin-badge-item">
          <i class="fas fa-envelope-circle-check"></i> 2FA Email OTP
        </div>
        <div class="admin-badge-item">
          <i class="fas fa-lock"></i> 256-Bit SSL
        </div>
      </div>

      <div class="admin-resident-link">
        <span style="color: #64748b;">Not an official or staff?</span>
        <a href="{{ route('login') }}">Go to Resident Portal &rarr;</a>
      </div>

    </div>
  </div>

  <script>
    function toggleAdminPw() {
      const pwInput = document.getElementById('password');
      const icon = document.getElementById('admin-pw-icon');
      if (pwInput.type === 'password') {
        pwInput.type = 'text';
        icon.className = 'fas fa-eye-slash';
      } else {
        pwInput.type = 'password';
        icon.className = 'fas fa-eye';
      }
    }
  </script>
</body>
</html>
