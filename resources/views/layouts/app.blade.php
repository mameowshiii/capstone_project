<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Barangay Pili') }} System</title>
  <meta name="description" content="Barangay Pili Clearance and Certificate Processing System">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @yield('styles')

  @if(str_contains(request()->header('User-Agent', ''), 'BrgyPiliApp'))
  <style>
    /* Force tables to render as stacked vertical lists (cards) inside the mobile app */
    .native-mobile-app .table,
    .native-mobile-app .table thead,
    .native-mobile-app .table tbody,
    .native-mobile-app .table th,
    .native-mobile-app .table td,
    .native-mobile-app .table tr,
    .native-mobile-app .table-wrapper {
      display: block !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    
    /* Hide the table headers completely */
    .native-mobile-app .table thead {
      display: none !important;
    }
    
    /* Each table row becomes a single card */
    .native-mobile-app .table tbody tr {
      background: #ffffff !important;
      border: 1.5px solid #e2e8f0 !important;
      border-radius: 14px !important;
      padding: 16px !important;
      margin-bottom: 16px !important;
      box-shadow: 0 2px 8px rgba(0,0,0,.04) !important;
      display: flex !important;
      flex-direction: column !important;
      gap: 8px !important;
      text-align: left !important;
    }
    
    /* Each table cell stacks vertically left-aligned */
    .native-mobile-app .table tbody td {
      border: none !important;
      padding: 6px 0 !important;
      text-align: left !important;
      width: 100% !important;
      display: block !important;
      box-sizing: border-box !important;
      border-bottom: 1px dashed #f1f5f9 !important;
    }

    .native-mobile-app .table tbody td:last-child {
      border-bottom: none !important;
    }

    /* Make sure all data items inside columns align cleanly to the left */
    .native-mobile-app .table tbody td * {
      text-align: left !important;
      justify-content: flex-start !important;
    }
    
    /* Specific override for My Requests mobile cards to also stack vertically in the app */
    .native-mobile-app .request-card-meta {
      grid-template-columns: 1fr !important;
      text-align: left !important;
    }
    
    .native-mobile-app .request-card-meta-item {
      text-align: left !important;
    }

    .native-mobile-app .request-card-meta-item * {
      text-align: left !important;
    }
  </style>
  @endif
</head>
<body class="{{ str_contains(request()->header('User-Agent', ''), 'BrgyPiliApp') ? 'native-mobile-app' : '' }}">
  <!-- Mobile sidebar overlay -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <div class="wrapper">
    <!-- ── Sidebar ─────────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="brand-logo" style="background: transparent;">
          <img src="{{ asset('assets/images/pili_logo.png') }}" alt="Pili Logo" style="width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        </div>
        <div class="brand-text">
          <strong>Brgy. Pili</strong>
          <small>Clearance System</small>
        </div>
      </div>

      <nav class="sidebar-nav">
        @if(in_array(Auth::user()->role, ['admin', 'staff']))
          <span class="nav-section-label">Main</span>
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
          </a>
          <span class="nav-section-label">Management</span>
          <a href="{{ route('admin.requests') }}" class="nav-link {{ Route::is('admin.requests') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Requests
          </a>
          <a href="{{ route('admin.payments') }}" class="nav-link {{ Route::is('admin.payments') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> Payments
          </a>
          <a href="{{ route('admin.summons') }}" class="nav-link {{ Route::is('admin.summons') ? 'active' : '' }}">
            <i class="fas fa-gavel"></i> Summons / Blotters
          </a>
          <a href="{{ route('admin.bulletins') }}" class="nav-link {{ Route::is('admin.bulletins') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i> Bulletins / Notices
          </a>
          <a href="{{ route('admin.borrows') }}" class="nav-link {{ Route::is('admin.borrows') ? 'active' : '' }}">
            <i class="fas fa-hand-holding"></i> Borrow Requests
          </a>
          <a href="{{ route('admin.residents') }}" class="nav-link {{ Route::is('admin.residents') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Residents
          </a>
          <a href="{{ route('admin.certificates') }}" class="nav-link {{ Route::is('admin.certificates') ? 'active' : '' }}">
            <i class="fas fa-certificate"></i> Certificate Types
          </a>
          <a href="{{ route('admin.officials') }}" class="nav-link {{ Route::is('admin.officials') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Officials
          </a>
          @if(Auth::user()->role === 'admin')
            <span class="nav-section-label">Administration</span>
            <a href="{{ route('admin.users') }}" class="nav-link {{ Route::is('admin.users') ? 'active' : '' }}">
              <i class="fas fa-user-shield"></i> User Management
            </a>
            <a href="{{ route('admin.archive') }}" class="nav-link {{ Route::is('admin.archive') ? 'active' : '' }}">
              <i class="fas fa-box-archive"></i> Archive
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-link {{ Route::is('admin.reports') ? 'active' : '' }}">
              <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="{{ route('admin.activity_logs') }}" class="nav-link {{ Route::is('admin.activity_logs') ? 'active' : '' }}">
              <i class="fas fa-history"></i> Activity Logs
            </a>
          @endif
        @else
          <span class="nav-section-label">My Account</span>
          <a href="{{ route('resident.request') }}" class="nav-link {{ Route::is('resident.request') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> New Request
          </a>
          <a href="{{ route('resident.my_requests') }}" class="nav-link {{ Route::is('resident.my_requests') ? 'active' : '' }}">
            <i class="fas fa-list"></i> My Requests
          </a>
          <a href="{{ route('resident.borrows') }}" class="nav-link {{ Route::is('resident.borrows') ? 'active' : '' }}">
            <i class="fas fa-hand-holding"></i> Borrow Equipment
          </a>
          <a href="{{ route('resident.summons') }}" class="nav-link {{ Route::is('resident.summons') ? 'active' : '' }}">
            <i class="fas fa-gavel"></i> My Summons
          </a>
          <a href="{{ route('resident.bulletins') }}" class="nav-link {{ Route::is('resident.bulletins') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i> Announcements
          </a>
          <a href="{{ route('resident.profile') }}" class="nav-link {{ Route::is('resident.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i> My Profile
          </a>
        @endif

        <span class="nav-section-label">General</span>
        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin:0;">
          @csrf
          <button type="submit" class="nav-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;"
            onclick="return confirm('Log out?')">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </nav>

      <div class="sidebar-footer">
        <a class="user-info" href="{{ in_array(Auth::user()->role, ['admin', 'staff']) ? route('admin.profile') : route('resident.profile') }}" style="text-decoration:none;">
          <div class="user-avatar">
            @php
              $photo = Auth::user()->role === 'resident' 
                ? (Auth::user()->resident->photo ?? '') 
                : Auth::user()->photo;
            @endphp
            @if($photo)
              <img src="{{ asset('assets/uploads/' . $photo) }}" alt="Profile photo">
            @else
              {{ strtoupper(substr(Auth::user()->resident ? Auth::user()->resident->first_name : Auth::user()->username, 0, 1)) }}
            @endif
          </div>
          <div>
            <div class="user-name">
              {{ Auth::user()->resident ? Auth::user()->resident->full_name : Auth::user()->username }}
            </div>
            <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
          </div>
        </a>
      </div>
    </aside>

    <!-- ── Main ───────────────────────────────────────────── -->
    <div class="main-content">
      <!-- Topbar -->
      <header class="topbar no-print">
        <div class="topbar-left">
          <button id="sidebar-toggle" style="background:none;border:none;cursor:pointer;font-size:20px;color:#374151;">
            <i class="fas fa-bars"></i>
          </button>
          <span class="topbar-title">@yield('title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
          <a href="{{ in_array(Auth::user()->role, ['admin', 'staff']) ? route('admin.profile') : route('resident.profile') }}" class="topbar-profile-link" title="Profile">
            <span class="topbar-avatar">
              @if($photo)
                <img src="{{ asset('assets/uploads/' . $photo) }}" alt="Profile photo">
              @else
                {{ strtoupper(substr(Auth::user()->resident ? Auth::user()->resident->first_name : Auth::user()->username, 0, 1)) }}
              @endif
            </span>
            <span class="topbar-profile-name">
              {{ Auth::user()->resident ? Auth::user()->resident->first_name : Auth::user()->username }}
            </span>
          </a>
          <a href="{{ in_array(Auth::user()->role, ['admin', 'staff']) ? route('admin.dashboard') : route('resident.my_requests') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-home"></i>
          </a>
        </div>
      </header>

      <!-- Flash messages -->
      @if(session('success'))
        <div id="flash-message" style="margin:16px 24px 0;">
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
          </div>
        </div>
      @endif
      @if(session('error'))
        <div id="flash-message" style="margin:16px 24px 0;">
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
          </div>
        </div>
      @endif
      @if($errors->any())
        <div id="flash-message" style="margin:16px 24px 0;">
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 20px;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <div class="page-content">
        @yield('content')
      </div>
    </div>
  </div>

  <!-- Mobile Bottom Navigation -->
  <nav class="mobile-bottom-nav" id="mobile-bottom-nav">
    @if(in_array(Auth::user()->role, ['admin', 'staff']))
      <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('admin.requests') }}" class="mobile-nav-item {{ Route::is('admin.requests') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <span>Requests</span>
      </a>
      <a href="{{ route('admin.residents') }}" class="mobile-nav-item {{ Route::is('admin.residents') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Residents</span>
      </a>
      <a href="{{ route('admin.payments') }}" class="mobile-nav-item {{ Route::is('admin.payments') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span>Payments</span>
      </a>
      <button class="mobile-nav-item" onclick="document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sidebar-overlay').classList.toggle('show');">
        <i class="fas fa-bars"></i>
        <span>More</span>
      </button>
    @else
      <a href="{{ route('resident.request') }}" class="mobile-nav-item {{ Route::is('resident.request') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i>
        <span>New</span>
      </a>
      <a href="{{ route('resident.my_requests') }}" class="mobile-nav-item {{ Route::is('resident.my_requests') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        <span>Requests</span>
      </a>
      <a href="{{ route('resident.bulletins') }}" class="mobile-nav-item {{ Route::is('resident.bulletins') ? 'active' : '' }}">
        <i class="fas fa-bullhorn"></i>
        <span>Bulletins</span>
      </a>
      <a href="{{ route('resident.profile') }}" class="mobile-nav-item {{ Route::is('resident.profile') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <button class="mobile-nav-item" onclick="document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sidebar-overlay').classList.toggle('show');">
        <i class="fas fa-bars"></i>
        <span>More</span>
      </button>
    @endif
  </nav>

  <script src="{{ asset('assets/js/main.js') }}"></script>
  @yield('scripts')
</body>
</html>
