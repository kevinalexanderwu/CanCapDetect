<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CanCap Detect – AI-powered Defect Detection</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- NAV -->
  <nav>
    <img class="logo_product" src="{{ asset('images/logo_product.png') }}">
    <img class="binus_binus" src="{{ asset('images/binus_binus.png') }}">
  </nav>

  <!-- HERO -->
  <div class="hero">
    <div class="hero-left">
      <div class="badge">AI-powered</div>
      <h1>Detect Defects on <span class="highlight">Cans &amp;<br>Bottle Caps</span> Instantly</h1>
      <p>An intelligent detection system for automatically identifying defects such as dents, rust, and damage on bottle caps and cans using live camera feeds or uploaded images.</p>
      <div class="hero-buttons">
        <button class="btn btn-primary" id="btnOpenCameraHero">
          <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#fff" stroke-width="1.6"/><circle cx="8" cy="8" r="2.5" fill="#fff"/></svg>
          Open Camera
        </button>
        <button class="btn btn-outline" id="btnUploadHero">
          <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="11" width="12" height="2" rx="1" fill="currentColor" opacity="0.3"/></svg>
          Upload Photo
        </button>
        <input type="file" id="fileInput" accept="image/*" style="display:none" />
      </div>
    </div>
    <div class="hero-img">
      <svg width="310" height="260" viewBox="0 0 310 260" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="55" y="8" width="200" height="148" rx="14" fill="#EBF4FD" stroke="#C8DFF5" stroke-width="1.5"/>
        <rect x="72" y="26" width="76" height="10" rx="5" fill="#3A8FD9" opacity="0.45"/>
        <rect x="72" y="42" width="56" height="8" rx="4" fill="#4FC3C3" opacity="0.5"/>
        <rect x="72" y="55" width="90" height="8" rx="4" fill="#3A8FD9" opacity="0.2"/>
        <polyline points="162,110 178,88 196,100 212,78 230,94" stroke="#3A8FD9" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        <polyline points="162,122 178,116 196,120 212,112 230,115" stroke="#4FC3C3" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="6" y="56" width="88" height="58" rx="10" fill="#fff" stroke="#DDE3EE" stroke-width="1.2"/>
        <rect x="17" y="70" width="28" height="6" rx="3" fill="#3A8FD9" opacity="0.55"/>
        <rect x="17" y="82" width="58" height="5" rx="2.5" fill="#DDE3EE"/>
        <rect x="17" y="92" width="42" height="5" rx="2.5" fill="#DDE3EE"/>
        <rect x="218" y="140" width="88" height="58" rx="10" fill="#fff" stroke="#DDE3EE" stroke-width="1.2"/>
        <rect x="228" y="154" width="28" height="6" rx="3" fill="#4FC3C3" opacity="0.8"/>
        <rect x="228" y="166" width="58" height="5" rx="2.5" fill="#DDE3EE"/>
        <rect x="228" y="176" width="38" height="5" rx="2.5" fill="#DDE3EE"/>
        <circle cx="242" cy="42" r="14" fill="#3A8FD9"/>
        <text x="242" y="47" text-anchor="middle" fill="#fff" font-size="13" font-family="sans-serif" font-weight="700">T</text>
        <rect x="224" y="68" width="28" height="22" rx="5" fill="#4FC3C3" opacity="0.25" stroke="#4FC3C3" stroke-width="1.2"/>
        <circle cx="232" cy="75" r="2.5" fill="#4FC3C3"/>
        <path d="M224 86l7-5 6 4 4-3 9 7" stroke="#4FC3C3" stroke-width="1.2" fill="none"/>
        <ellipse cx="152" cy="208" rx="28" ry="34" fill="#C8DFF5" opacity="0.45"/>
        <circle cx="152" cy="180" r="13" fill="#C8DFF5"/>
        <path d="M133 210 Q123 236 128 252 Q142 262 152 257 Q162 262 176 252 Q181 236 171 210" fill="#C8DFF5" opacity="0.65"/>
        <line x1="133" y1="210" x2="109" y2="226" stroke="#C8DFF5" stroke-width="7" stroke-linecap="round"/>
        <line x1="171" y1="210" x2="195" y2="228" stroke="#C8DFF5" stroke-width="7" stroke-linecap="round"/>
        <circle cx="252" cy="16" r="5" fill="#F05C5C"/>
      </svg>
    </div>
  </div>

  <!-- STATS -->
  <div class="stats">
    <div class="stat">
      <div class="stat-value">94.7%</div>
      <div class="stat-label">Detection Accuracy</div>
    </div>
    <div class="stat">
      <div class="stat-value">&lt;2s</div>
      <div class="stat-label">Processing Speed</div>
    </div>
    <div class="stat">
      <div class="stat-value">10K+</div>
      <div class="stat-label">Images Processed</div>
    </div>
    <div class="stat">
      <div class="stat-value">&lt;5%</div>
      <div class="stat-label">Error Rate</div>
    </div>
  </div>

  <!-- FEATURES -->
  <div class="section">
    <div class="section-tag">Features</div>
    <h2>What can be detected?</h2>
    <p class="section-sub">Comprehensive defect detection for <a href="#">cans</a> and <a href="#">bottle caps</a></p>
    <div class="features-grid">
      <div class="feature-card">
        <h3>Dent &amp; deformation</h3>
        <p>Detects dents, bends, and shape deformities on can bodies that may compromise product integrity.</p>
      </div>
      <div class="feature-card">
        <h3>Rush &amp; corrosion</h3>
        <p>Detects rust spots, corrosion, or discoloration on can surfaces that indicate material damage.</p>
      </div>
      <div class="feature-card">
        <h3>Bottle cap integrity</h3>
        <p>Checks cap seal tightness, broken seals, and improper cap placement on bottles.</p>
      </div>
    </div>
    <div class="features-row2">
      <div class="feature-card">
        <h3>Real-time detection</h3>
        <p>Enables real-time detection using a live camera for continuous scanning on production lines without interrupting the process.</p>
      </div>
      <div class="feature-card">
        <h3>Photo upload &amp; analysis</h3>
        <p>Upload images for analysis — ideal for manual inspection or field quality audits.</p>
      </div>
    </div>
  </div>

  <!-- HOW IT WORKS -->
  <div class="how-section">
    <div class="section-tag">How it works</div>
    <h2 class="how-title">3 Simple steps</h2>
    <div class="steps">
      <div class="step">
        <div class="step-circle">1</div>
        <div class="step-connector"></div>
        <h4>Choose Method</h4>
        <p>Use live camera or upload a photo of the can or bottle cap</p>
      </div>
      <div class="step">
        <div class="step-circle">2</div>
        <div class="step-connector"></div>
        <h4>AI analysis</h4>
        <p>The system scans and detects defects automatically in seconds</p>
      </div>
      <div class="step">
        <div class="step-circle">3</div>
        <div class="step-connector"></div>
        <h4>View results</h4>
        <p>Results show defect location, severity level, and recommended action</p>
      </div>
    </div>
  </div>

  <!-- CTA BANNER -->
  <div class="cta-banner">
    <div>
      <h2>Ready to start inspecting?</h2>
      <p>No registration needed — open the camera and start detecting right away</p>
    </div>
    <div class="cta-buttons">
      <button class="btn btn-primary" id="btnOpenCameraCTA">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#fff" stroke-width="1.6"/><circle cx="8" cy="8" r="2.5" fill="#fff"/></svg>
        Open Camera
      </button>
      <button class="btn btn-outline" id="btnUploadCTA">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="11" width="12" height="2" rx="1" fill="currentColor" opacity="0.3"/></svg>
        Upload Photo
      </button>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    &copy; 2026 CanCap Detect. AI-powered defect detection
  </footer>

  <!-- MODAL -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal">
      <button class="modal-close" id="modalClose">&times;</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <script src="{{ asset('js/main.js') }}"></script></script>
</body>
</html>
