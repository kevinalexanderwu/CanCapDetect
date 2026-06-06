<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detect – CanCap Detect</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

  <!-- NAV -->
  <nav>
    <a href="{{ route('home') }}">
      <img class="logo_product" src="{{ asset('images/logo_product.png') }}" alt="CanCap Detect">
    </a>
    <img class="binus_binus" src="{{ asset('images/logo_binus.png') }}" alt="Binus">
  </nav>
      <div class="back-wrapper">
    <button class="btn-back" onclick="window.location.href='{{ route('home') }}'">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
      </svg>
      Back
    </button>
  </div>
  <div class="detect-page"></div>

  <!-- DETECT PAGE -->
  <div class="detect-page">

    <!-- ── Model Selector ── -->
    <div class="detect-section">
      <div class="section-tag">Model Selection</div>
      <h2>Choose Detection Model</h2>
      <div class="model-selector">
        <label class="model-card active" id="modelCardV8">
          <input type="radio" name="model" value="yolov8" checked hidden>
          <div class="model-badge">YOLOv8</div>
          <div class="model-name">YOLOv8</div>
          <div class="model-desc">Stable, balanced speed and accuracy. Recommended for general use.</div>
        </label>
        <label class="model-card" id="modelCardV11">
          <input type="radio" name="model" value="yolov11" hidden>
          <div class="model-badge model-badge-new">YOLOv11</div>
          <div class="model-name">YOLOv11</div>
          <div class="model-desc">Latest architecture. Higher accuracy with improved detection heads.</div>
        </label>
      </div>
    </div>

    <!-- ── Input Method ── -->
    <div class="detect-section">
      <div class="section-tag">Input Method</div>
      <h2>Provide Image</h2>
      <div class="input-tabs">
        <button class="tab-btn active" id="tabUpload" onclick="switchTab('upload')">
          <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
            <path d="M8 2v8M5 7l3-3 3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="2" y="11" width="12" height="2" rx="1" fill="currentColor" opacity="0.4"/>
          </svg>
          Upload Photo
        </button>
        <button class="tab-btn" id="tabCamera" onclick="switchTab('camera')">
          <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="8" cy="8" r="2.5" fill="currentColor"/>
          </svg>
          Live Camera
        </button>
      </div>

      <!-- Upload Tab -->
      <div id="panelUpload" class="input-panel">
        <div class="dropzone" id="dropzone">
          <input type="file" id="fileInputDetect" accept="image/*" hidden>
          <svg width="40" height="40" fill="none" viewBox="0 0 40 40">
            <rect x="4" y="4" width="32" height="32" rx="8" fill="#EBF4FD"/>
            <path d="M20 12v12M14 18l6-6 6 6" stroke="#3A8FD9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="10" y="26" width="20" height="3" rx="1.5" fill="#3A8FD9" opacity="0.3"/>
          </svg>
          <p class="dropzone-text">Drag & drop an image here or <span class="dropzone-link" onclick="document.getElementById('fileInputDetect').click()">browse file</span></p>
          <p class="dropzone-hint">Supported: JPG, PNG, WEBP — max 10 MB</p>
        </div>
        <div id="previewContainer" class="preview-container" style="display:none">
          <img id="previewImage" class="preview-img" src="" alt="Preview">
          <button class="preview-remove" onclick="removePreview()">✕ Remove</button>
        </div>
      </div>

      <!-- Camera Tab -->
      <div id="panelCamera" class="input-panel" style="display:none">
        <div class="camera-container">
          <video id="cameraFeed" class="camera-feed" autoplay playsinline></video>
          <canvas id="cameraCanvas" style="display:none"></canvas>
          <div class="camera-controls">
            <button class="btn btn-primary" id="btnStartCamera" onclick="startCamera()">Start Camera</button>
            <button class="btn btn-outline" id="btnCapture" onclick="captureFrame()" style="display:none">Capture</button>
            <button class="btn btn-outline" id="btnStopCamera" onclick="stopCamera()" style="display:none">Stop</button>
          </div>
        </div>
        <div id="capturedContainer" class="preview-container" style="display:none">
          <img id="capturedImage" class="preview-img" src="" alt="Captured">
          <button class="preview-remove" onclick="removeCaptured()">✕ Retake</button>
        </div>
      </div>
    </div>

    <!-- ── Run Detection ── -->
    <div class="detect-section detect-run">
      <button class="btn btn-primary btn-detect" id="btnDetect" onclick="runDetection()" disabled>
        <svg width="18" height="18" fill="none" viewBox="0 0 18 18">
          <circle cx="9" cy="9" r="7" stroke="#fff" stroke-width="1.8"/>
          <circle cx="9" cy="9" r="3" fill="#fff"/>
        </svg>
        Run Detection
      </button>
      <p class="detect-hint" id="detectHint">Select a model and provide an image first</p>
    </div>

    <!-- ── Loading ── -->
    <div id="loadingSection" class="detect-section loading-section" style="display:none">
      <div class="spinner"></div>
      <p>Analyzing image with <span id="loadingModel"></span>…</p>
    </div>

    <!-- ── Results ── -->
    <div id="resultSection" class="detect-section" style="display:none">
      <div class="section-tag">Results</div>
      <h2>Detection Results</h2>

      <div class="result-summary" id="resultSummary"></div>

      <div class="result-images">
        <div class="result-image-box">
          <div class="result-image-label">Original</div>
          <img id="resultOriginal" class="result-img" src="" alt="Original">
        </div>
        <div class="result-image-box">
          <div class="result-image-label">Detected</div>
          <img id="resultDetected" class="result-img" src="" alt="Detected">
        </div>
      </div>

      <div class="detections-list" id="detectionsList"></div>
      <div id="detectionInfo"></div>

      <div class="result-actions">
        <button class="btn btn-primary" onclick="resetDetection()">Detect Another</button>
        <a href="{{ route('history') }}" class="btn btn-outline">View History</a>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <footer>
    &copy; 2026 CanCap Detect. AI-powered defect detection
  </footer>

  <script src="{{ asset('js/detect.js') }}"></script>
</body>
</html>