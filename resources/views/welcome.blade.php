<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome — Barangay Pili Digital Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Official online services portal for Barangay Pili. Request certificates, track status, and view announcements.">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #b91c1c, #450a0a);
      --secondary-gradient: linear-gradient(135deg, #10b981, #047857);
      --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      --font-family: 'Plus Jakarta Sans', sans-serif;
    }
    body {
      font-family: var(--font-family);
      margin: 0;
      padding: 0;
      color: #1e293b;
      background-color: #f8fafc;
      overflow-x: hidden;
      scroll-behavior: smooth;
    }
    /* Navigation Bar */
    .navbar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid #e2e8f0;
      padding: 16px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      box-sizing: border-box;
      z-index: 1000;
    }
    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .logo-badge {
      background: #b91c1c;
      color: #fff;
      font-weight: 800;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }
    .logo-text {
      font-weight: 800;
      font-size: 18px;
      color: #0f172a;
      letter-spacing: -0.5px;
    }
    .nav-links {
      display: flex;
      gap: 32px;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .nav-links a {
      text-decoration: none;
      color: #475569;
      font-weight: 600;
      font-size: 14px;
      transition: color 0.2s ease;
    }
    .nav-links a:hover {
      color: #b91c1c;
    }
    .nav-actions {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    .btn-nav-primary {
      background: #b91c1c;
      color: white !important;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 13px;
      text-decoration: none;
      transition: transform 0.2s ease, background 0.2s ease;
    }
    .btn-nav-primary:hover {
      background: #991b1b;
      transform: translateY(-1px);
    }
    .btn-nav-secondary {
      border: 1px solid #cbd5e1;
      color: #334155;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 13px;
      text-decoration: none;
      transition: background 0.2s ease;
    }
    .btn-nav-secondary:hover {
      background: #f1f5f9;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(135deg, rgba(185, 28, 28, 0.45), rgba(69, 10, 10, 0.65)), url("{{ asset('assets/images/background.jpg') }}") no-repeat center center / cover;
      padding: 180px 20px 120px 20px;
      text-align: center;
      color: white;
      position: relative;
    }
    .hero-badge {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 6px 16px;
      border-radius: 100px;
      font-weight: 700;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 24px;
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    .hero h1 {
      font-size: 48px;
      font-weight: 800;
      max-width: 800px;
      margin: 0 auto 20px auto;
      line-height: 1.2;
      letter-spacing: -1.5px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.45);
    }
    .hero p {
      font-size: 18px;
      max-width: 600px;
      margin: 0 auto 36px auto;
      opacity: 0.95;
      font-weight: 400;
      line-height: 1.6;
      text-shadow: 0 1px 5px rgba(0, 0, 0, 0.45);
    }
    .hero-ctas {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }
    .btn-hero-primary {
      background: #10b981;
      color: white;
      padding: 16px 32px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
      transition: transform 0.2s, background 0.2s;
    }
    .btn-hero-primary:hover {
      background: #059669;
      transform: translateY(-2px);
    }
    .btn-hero-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 16px 30px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      transition: background 0.2s, transform 0.2s;
    }
    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }

    /* Wave Divider */
    .hero-wave {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      overflow: hidden;
      line-height: 0;
    }
    .hero-wave svg {
      position: relative;
      display: block;
      width: calc(100% + 1.3px);
      height: 60px;
    }
    .hero-wave .shape-fill {
      fill: #f8fafc;
    }

    /* Services Grid */
    .section {
      padding: 100px 40px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .section-header {
      text-align: center;
      margin-bottom: 60px;
    }
    .section-header h2 {
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -1px;
      color: #0f172a;
      margin: 0 0 12px 0;
    }
    .section-header p {
      color: #64748b;
      max-width: 500px;
      margin: 0 auto;
      font-size: 16px;
    }
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }
    .service-card {
      background: white;
      border-radius: 16px;
      padding: 30px;
      box-shadow: var(--card-shadow);
      border: 1px solid #e2e8f0;
      transition: transform 0.3s, box-shadow 0.3s;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }
    .service-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 24px;
    }
    .service-icon.blue { background: #fef2f2; color: #b91c1c; }
    .service-icon.green { background: #ecfdf5; color: #10b981; }
    .service-icon.yellow { background: #fffbeb; color: #d97706; }
    .service-icon.purple { background: #faf5ff; color: #7c3aed; }
    .service-card h3 {
      font-size: 18px;
      font-weight: 700;
      margin: 0 0 10px 0;
      color: #0f172a;
    }
    .service-card p {
      color: #64748b;
      font-size: 14px;
      line-height: 1.5;
      margin: 0 0 20px 0;
      flex: 1;
    }
    .service-link {
      color: #b91c1c;
      text-decoration: none;
      font-weight: 700;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .service-link:hover {
      text-decoration: underline;
    }

    /* Track Document section */
    .track-section {
      background: white;
      border-radius: 24px;
      box-shadow: var(--card-shadow);
      border: 1px solid #e2e8f0;
      padding: 60px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }
    .track-info h2 {
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -1px;
      margin: 0 0 16px 0;
      color: #0f172a;
    }
    .track-info p {
      color: #64748b;
      font-size: 16px;
      line-height: 1.6;
      margin: 0 0 24px 0;
    }
    .feature-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .feature-list li {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      color: #475569;
    }
    .feature-list i {
      color: #10b981;
    }
    .track-box-widget {
      background: #f8fafc;
      border-radius: 16px;
      padding: 32px;
      border: 1px solid #e2e8f0;
    }
    .track-form label {
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #475569;
      display: block;
      margin-bottom: 8px;
    }
    .track-form input {
      width: 100%;
      background: white;
      border: 2px solid #e2e8f0;
      padding: 14px 16px;
      border-radius: 8px;
      font-family: monospace;
      font-size: 16px;
      box-sizing: border-box;
      margin-bottom: 16px;
      transition: border-color 0.2s;
    }
    .track-form input:focus {
      border-color: #b91c1c;
      outline: none;
    }
    .btn-track-submit {
      width: 100%;
      background: #b91c1c;
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.2s;
    }
    .btn-track-submit:hover {
      background: #991b1b;
    }

    /* Bulletins Section */
    .bulletins-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }
    .bulletin-item {
      background: white;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      box-shadow: var(--card-shadow);
      padding: 24px;
    }
    .bulletin-badge {
      background: #fffbeb;
      color: #b45309;
      font-weight: 700;
      font-size: 10px;
      padding: 4px 10px;
      border-radius: 100px;
      text-transform: uppercase;
      display: inline-block;
      margin-bottom: 12px;
    }
    .bulletin-item h4 {
      font-size: 18px;
      font-weight: 700;
      margin: 0 0 10px 0;
      color: #0f172a;
    }
    .bulletin-item p {
      color: #64748b;
      font-size: 14px;
      line-height: 1.5;
      margin: 0 0 16px 0;
    }
    .bulletin-meta {
      font-size: 12px;
      color: #94a3b8;
      font-weight: 500;
    }

    /* Footer */
    .footer {
      background: #0f172a;
      color: white;
      padding: 80px 40px 40px 40px;
    }
    .footer-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 60px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 40px;
      margin-bottom: 40px;
    }
    .footer-col h3 {
      font-size: 16px;
      font-weight: 700;
      text-transform: uppercase;
      margin: 0 0 20px 0;
      letter-spacing: 0.5px;
    }
    .footer-col ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .footer-col ul a {
      color: #94a3b8;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.2s;
    }
    .footer-col ul a:hover {
      color: white;
    }
    .footer-bottom {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #64748b;
      font-size: 13px;
      flex-wrap: wrap;
      gap: 16px;
    }

    @media(max-width: 768px) {
      .navbar { padding: 16px 20px; }
      .nav-links { display: none; }
      .track-section { grid-template-columns: 1fr; padding: 40px; gap: 40px; }
      .footer-grid { grid-template-columns: 1fr; gap: 40px; }
      .hero h1 { font-size: 36px; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-container">
      <img src="{{ asset('assets/images/pili_logo.png') }}" alt="Barangay Pili Logo" style="height: 40px; width: auto; object-fit: contain;">
    </div>
    <ul class="nav-links">
      <li><a href="#">HOME</a></li>
      <li><a href="#services">BARANGAY SERVICES</a></li>
      <li><a href="#tracking">TRACK APPLICATION</a></li>
      <li><a href="#bulletins">ANNOUNCEMENT</a></li>
      <li><a href="{{ asset('downloads/brgy-pili-portal.apk') }}" download>DOWNLOAD APP</a></li>
    </ul>
    <div class="nav-actions">
      <a href="{{ route('login') }}" class="btn-nav-secondary">Resident Login</a>
      <a href="{{ route('register') }}" class="btn-nav-primary">Register Account</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <header class="hero">
    <div class="hero-badge">
      <i class="fas fa-sparkles" style="color:#f59e0b;"></i> Barangay Pili Digital Services
    </div>
    <h1>Modern, Streamlined Public Services for Every Resident</h1>
    <p>Request documents, certificates, check official announcements, and resolve issues online. Fast, secure, and hassle-free.</p>
    <div class="hero-ctas">
      <a href="{{ route('login') }}" class="btn-hero-primary"><i class="fas fa-id-card" style="margin-right:8px;"></i> Request Certifications</a>
      <a href="#tracking" class="btn-hero-secondary"><i class="fas fa-search" style="margin-right:8px;"></i> Track Request</a>
    </div>
    
    <!-- Wave -->
    <div class="hero-wave">
      <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
      </svg>
    </div>
  </header>

  <!-- Services Grid -->
  <section class="section" id="services">
    <div class="section-header">
      <h2>Document &amp; Certification Services</h2>
      <p>Request official barangay documents online from the comfort of your home. Approved documents can be printed securely.</p>
    </div>
    <div class="services-grid">
      
      <div class="service-card">
        <div class="service-icon blue"><i class="fas fa-file-shield"></i></div>
        <h3>Barangay Clearance</h3>
        <p>Official clearance certification issued by the barangay for employment, banking, or business requirements.</p>
        <a href="{{ route('login') }}" class="service-link">File Request <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="service-card">
        <div class="service-icon green"><i class="fas fa-heart-pulse"></i></div>
        <h3>Certificate of Indigency</h3>
        <p>Free certification issued to indigent residents seeking social services, scholarships, or medical assistance.</p>
        <a href="{{ route('login') }}" class="service-link">File Request <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="service-card">
        <div class="service-icon yellow"><i class="fas fa-house-chimney-user"></i></div>
        <h3>Certificate of Residency</h3>
        <p>Certifies legitimacy of residency in Barangay Pili, commonly used for government and bank registrations.</p>
        <a href="{{ route('login') }}" class="service-link">File Request <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="service-card">
        <div class="service-icon purple"><i class="fas fa-award"></i></div>
        <h3>Good Moral Character</h3>
        <p>Formal document certifying a resident is in good standing and has no pending disputes or criminal records.</p>
        <a href="{{ route('login') }}" class="service-link">File Request <i class="fas fa-arrow-right"></i></a>
      </div>

    </div>
  </section>



  <!-- Realtime tracking -->
  <section class="section" id="tracking" style="padding-top:0;">
    <div class="track-section">
      <div class="track-info">
        <h2>No Account Required for Tracking</h2>
        <p>Track your certificate requests instantly in real-time. Just enter your 10-digit application tracking number below to see the status of your document.</p>
        <ul class="feature-list">
          <li><i class="fas fa-check-circle"></i> Live review status updates</li>
          <li><i class="fas fa-check-circle"></i> Verification of payment records</li>
          <li><i class="fas fa-check-circle"></i> Administrative messages &amp; notices</li>
        </ul>
      </div>
      <div class="track-box-widget">
        <form method="POST" action="{{ route('track') }}" class="track-form">
          @csrf
          <label>Enter Tracking Number</label>
          <input type="text" name="tracking" placeholder="e.g. PILI-20260730-XXXX" required>
          <button type="submit" class="btn-track-submit"><i class="fas fa-search"></i> Track Application</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Announcements / Bulletins -->
  <section class="section" id="bulletins" style="padding-top:0;">
    <div class="section-header">
      <h2>Announcements &amp; Advisories</h2>
      <p>Stay up to date with official programs, schedules, and alerts from the Barangay Pili administration.</p>
    </div>
    <div class="bulletins-row">
      
      <div class="bulletin-item">
        <span class="bulletin-badge">Health</span>
        <h4>Monthly Medical Mission</h4>
        <p>Free check-ups, pediatric consultations, and generic vitamin distribution at the Barangay Pili Session Hall starting this weekend.</p>
        <div class="bulletin-meta"><i class="fas fa-calendar" style="margin-right:6px;"></i> August 05, 2026</div>
      </div>

      <div class="bulletin-item">
        <span class="bulletin-badge" style="background:#ecfdf5; color:#059669;">Environment</span>
        <h4>Oplan Linis Barangay</h4>
        <p>Join our youth and barangay tanods for the weekly community-wide clean-up drive. Meet up at Purok 2 crossroads at 6:00 AM.</p>
        <div class="bulletin-meta"><i class="fas fa-calendar" style="margin-right:6px;"></i> August 08, 2026</div>
      </div>

      <div class="bulletin-item">
        <span class="bulletin-badge" style="background:#fee2e2; color:#b91c1c;">Alert</span>
        <h4>Typhoon Preparation Advisory</h4>
        <p>All purok leaders are advised to conduct canal clearing and ensure evacuation routes are mapped out ahead of incoming weather systems.</p>
        <div class="bulletin-meta"><i class="fas fa-calendar" style="margin-right:6px;"></i> August 12, 2026</div>
      </div>

    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-grid">
      <div class="footer-col" style="padding-right:40px;">
        <h3 style="color:white; font-size:18px; text-transform:none; margin-bottom:12px;"><i class="fas fa-landmark" style="color:#10b981; margin-right:8px;"></i> Barangay Pili Digital Services</h3>
        <p style="color:#94a3b8; font-size:14px; line-height:1.6;">Our mission is to establish a transparent, digital, and streamlined administrative portal empowering residents with reliable public service document deliveries and complaint conciliation facilities.</p>
      </div>
      <div class="footer-col">
        <h3>Portal Access</h3>
        <ul>
          <li><a href="{{ route('login') }}">Resident Sign In</a></li>
          <li><a href="{{ route('register') }}">Resident Registration</a></li>
          <li><a href="{{ route('login') }}">Admin Login Portal</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Contact Info</h3>
        <ul style="color:#94a3b8; font-size:14px; gap:8px;">
          <li><i class="fas fa-map-marker-alt" style="margin-right:6px; color:#10b981;"></i> Barangay Pili Hall, Minalabac, Camarines Sur</li>
          <li><i class="fas fa-envelope" style="margin-right:6px; color:#10b981;"></i> admin@brgy-pili.gov.ph</li>
          <li><i class="fas fa-phone-alt" style="margin-right:6px; color:#10b981;"></i> +63 917 123 4567</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div>&copy; 2026 Barangay Pili Online Services Portal. All Rights Reserved.</div>
      <div>Designed with <i class="fas fa-heart" style="color:#ef4444;"></i> for our community</div>
    </div>
  </footer>



</body>
</html>
