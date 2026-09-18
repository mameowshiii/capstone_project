<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Security Verification (2FA) &mdash; Barangay Pili</title>
  <meta name="description" content="Email OTP Two-Factor Authentication for Barangay Pili Admin Portal">
  <link rel="icon" type="image/png" href="{{ asset('assets/images/pili_logo.png') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --admin-bg: #0b1329;
      --admin-surface: #131f3f;
      --admin-surface-light: #1a2952;
      --admin-primary: #2563eb;
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
      max-width: 480px;
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
      padding: 36px 32px 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(180deg, rgba(37, 99, 235, 0.1) 0%, transparent 100%);
    }

    .icon-shield-wrap {
      width: 76px;
      height: 76px;
      margin: 0 auto 16px;
      background: rgba(37, 99, 235, 0.15);
      border: 2px solid rgba(37, 99, 235, 0.4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #60a5fa;
      font-size: 32px;
      box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
      animation: pulse-border 2s infinite ease-in-out;
    }

    @keyframes pulse-border {
      0%, 100% { box-shadow: 0 0 15px rgba(37, 99, 235, 0.3); transform: scale(1); }
      50% { box-shadow: 0 0 25px rgba(37, 99, 235, 0.6); transform: scale(1.03); }
    }

    .admin-card-header h1 {
      margin: 0;
      font-size: 21px;
      font-weight: 800;
      color: #ffffff;
    }

    .admin-card-header p {
      margin: 8px 0 0;
      font-size: 13.5px;
      color: var(--admin-text-muted);
      line-height: 1.5;
    }

    .masked-email {
      color: #fbbf24;
      font-weight: 600;
      word-break: break-all;
    }

    .admin-card-body {
      padding: 28px 32px 32px;
    }

    /* OTP Segmented Inputs */
    .otp-input-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin: 24px 0;
    }

    .otp-digit {
      width: 50px;
      height: 56px;
      text-align: center;
      font-size: 24px;
      font-weight: 800;
      font-family: 'Courier New', monospace;
      color: #ffffff;
      background: rgba(15, 23, 42, 0.85);
      border: 1.5px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      outline: none;
      transition: all 0.2s;
    }

    .otp-digit:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
      background: rgba(15, 23, 42, 1);
      transform: translateY(-2px);
    }

    .otp-digit.filled {
      border-color: #60a5fa;
      background: rgba(30, 58, 138, 0.3);
    }

    .timer-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 13px;
      color: #cbd5e1;
      margin-bottom: 24px;
    }

    .timer-badge {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 12px;
      font-family: monospace;
      font-size: 13px;
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

    .resend-section {
      margin-top: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      font-size: 13px;
    }

    .btn-resend {
      background: none;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #cbd5e1;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-resend:hover:not(:disabled) {
      background: rgba(255, 255, 255, 0.1);
      border-color: #60a5fa;
      color: #ffffff;
    }

    .btn-resend:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .btn-cancel {
      color: #94a3b8;
      text-decoration: none;
      font-size: 12.5px;
      transition: color 0.2s;
    }

    .btn-cancel:hover {
      color: #f87171;
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
  </style>
</head>
<body class="admin-auth-body">

  <div class="admin-auth-card">
    <div class="admin-card-header">
      <div class="icon-shield-wrap">
        <i class="fas fa-lock"></i>
      </div>
      <h1>Two-Factor Verification</h1>
      <p>
        A 6-digit security code has been dispatched to your email:<br>
        <span class="masked-email">{{ $maskedEmail }}</span>
      </p>
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

      <form method="POST" action="{{ route('admin.otp.verify') }}" id="otp-form">
        @csrf

        <!-- Consolidated hidden code input -->
        <input type="hidden" name="code" id="consolidated-code" required>

        <div class="otp-input-group">
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
        </div>

        <div class="timer-container">
          <i class="fas fa-stopwatch" style="color: #f59e0b;"></i>
          <span>Code expires in:</span>
          <span class="timer-badge" id="otp-countdown">10:00</span>
        </div>

        <button type="submit" class="admin-btn-submit" id="btn-verify">
          <i class="fas fa-shield-check"></i> Authorize &amp; Access Dashboard
        </button>
      </form>

      <div class="resend-section">
        <form method="POST" action="{{ route('admin.otp.resend') }}" id="resend-form">
          @csrf
          <button type="submit" class="btn-resend" id="btn-resend">
            <i class="fas fa-rotate-right"></i> <span id="resend-text">Resend Code</span>
          </button>
        </form>

        <a href="{{ route('admin.otp.cancel') }}" class="btn-cancel">
          <i class="fas fa-arrow-left"></i> Cancel and return to Admin Login
        </a>
      </div>

    </div>
  </div>

  <script>
    // 1. Segmented OTP Input Handling
    const digits = document.querySelectorAll('.otp-digit');
    const consolidatedInput = document.getElementById('consolidated-code');
    const form = document.getElementById('otp-form');

    function updateConsolidatedCode() {
      let code = '';
      digits.forEach(d => code += d.value.trim());
      consolidatedInput.value = code;
      return code;
    }

    digits.forEach((digit, index) => {
      digit.addEventListener('input', (e) => {
        const val = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = val ? val.slice(-1) : '';

        if (e.target.value) {
          e.target.classList.add('filled');
          if (index < digits.length - 1) {
            digits[index + 1].focus();
          }
        } else {
          e.target.classList.remove('filled');
        }

        const code = updateConsolidatedCode();
        if (code.length === 6) {
          form.submit();
        }
      });

      digit.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !digit.value && index > 0) {
          digits[index - 1].focus();
          digits[index - 1].value = '';
          digits[index - 1].classList.remove('filled');
          updateConsolidatedCode();
        }
      });

      digit.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
        if (!pastedData) return;

        pastedData.split('').slice(0, 6).forEach((char, i) => {
          if (digits[i]) {
            digits[i].value = char;
            digits[i].classList.add('filled');
          }
        });

        const lastIndex = Math.min(pastedData.length, digits.length) - 1;
        if (lastIndex >= 0 && lastIndex < digits.length) {
          digits[lastIndex].focus();
        }

        const code = updateConsolidatedCode();
        if (code.length === 6) {
          form.submit();
        }
      });
    });

    form.addEventListener('submit', (e) => {
      const code = updateConsolidatedCode();
      if (code.length !== 6) {
        e.preventDefault();
        alert('Please enter the complete 6-digit verification code.');
        digits[0].focus();
      }
    });

    // 2. OTP Expiry Countdown (10 minutes)
    const expiresTimestamp = {{ $expiresTimestamp ?? (time() + 600) }} * 1000;
    const countdownEl = document.getElementById('otp-countdown');

    function updateExpiryCountdown() {
      const now = new Date().getTime();
      const diff = expiresTimestamp - now;

      if (diff <= 0) {
        countdownEl.innerText = 'EXPIRED';
        countdownEl.style.backgroundColor = 'rgba(239, 68, 68, 0.4)';
        return;
      }

      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);
      countdownEl.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    updateExpiryCountdown();
    setInterval(updateExpiryCountdown, 1000);

    // 3. Resend Button Cooldown (60 seconds)
    const resendCooldownEnd = {{ $resendCooldownEnd ?? (time() + 60) }} * 1000;
    const btnResend = document.getElementById('btn-resend');
    const resendText = document.getElementById('resend-text');

    function updateResendCooldown() {
      const now = new Date().getTime();
      const diff = Math.ceil((resendCooldownEnd - now) / 1000);

      if (diff > 0) {
        btnResend.disabled = true;
        resendText.innerText = `Resend Code in ${diff}s`;
      } else {
        btnResend.disabled = false;
        resendText.innerText = 'Resend Code';
      }
    }
    updateResendCooldown();
    const resendInterval = setInterval(() => {
      updateResendCooldown();
      if (new Date().getTime() >= resendCooldownEnd) {
        clearInterval(resendInterval);
      }
    }, 1000);
  </script>
</body>
</html>
