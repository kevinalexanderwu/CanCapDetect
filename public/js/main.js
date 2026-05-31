/* =====================
   CANCAP DETECT — main.js
   ===================== */

const modalOverlay = document.getElementById('modalOverlay');
const modalContent = document.getElementById('modalContent');
const modalClose   = document.getElementById('modalClose');
const fileInput    = document.getElementById('fileInput');

// ── Helper: open & close modal ──────────────────────────────────────────────
function openModal(html) {
  modalContent.innerHTML = html;
  modalOverlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  // Stop any active camera stream before closing
  const video = modalContent.querySelector('video');
  if (video && video.srcObject) {
    video.srcObject.getTracks().forEach(t => t.stop());
    video.srcObject = null;
  }
  modalOverlay.classList.remove('active');
  document.body.style.overflow = '';
  modalContent.innerHTML = '';
}

modalClose.addEventListener('click', closeModal);
modalOverlay.addEventListener('click', (e) => {
  if (e.target === modalOverlay) closeModal();
});

// ── Open Camera ──────────────────────────────────────────────────────────────
async function startCamera() {
  openModal(`
    <h3>📷 Live Camera Detection</h3>
    <p>Point your camera at a can or bottle cap to detect defects in real time.</p>
    <video id="cameraFeed" autoplay playsinline muted></video>
    <div style="margin-top:14px; display:flex; gap:10px; justify-content:center;">
      <button class="btn btn-primary" id="captureBtn">Capture &amp; Analyse</button>
      <button class="btn btn-outline" id="stopCameraBtn">Stop Camera</button>
    </div>
    <canvas id="snapCanvas" style="display:none"></canvas>
  `);

  const video = document.getElementById('cameraFeed');

  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    video.srcObject = stream;
  } catch (err) {
    modalContent.innerHTML = `
      <h3>Camera Unavailable</h3>
      <p style="color:#e05c5c;">Could not access the camera: <strong>${err.message}</strong>.<br>
      Please check your browser permissions and try again.</p>
    `;
    return;
  }

  document.getElementById('stopCameraBtn').addEventListener('click', closeModal);

  document.getElementById('captureBtn').addEventListener('click', () => {
    const canvas  = document.getElementById('snapCanvas');
    const ctx     = canvas.getContext('2d');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/png');
    showAnalysisResult(dataUrl);
  });
}

document.getElementById('btnOpenCameraHero').addEventListener('click', startCamera);
document.getElementById('btnOpenCameraCTA').addEventListener('click', startCamera);

// ── Upload Photo ─────────────────────────────────────────────────────────────
function triggerUpload() {
  fileInput.click();
}

fileInput.addEventListener('change', () => {
  const file = fileInput.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => showAnalysisResult(e.target.result);
  reader.readAsDataURL(file);

  // Reset so same file can be re-uploaded
  fileInput.value = '';
});

document.getElementById('btnUploadHero').addEventListener('click', triggerUpload);
document.getElementById('btnUploadCTA').addEventListener('click', triggerUpload);

// ── Simulate AI Analysis Result ───────────────────────────────────────────────
function showAnalysisResult(imageDataUrl) {
  // Simulated random detection result
  const results = [
    { label: '✅ No Defect Detected',    color: '#27ae60', detail: 'The can or bottle cap appears to be in good condition. No dents, rust, or integrity issues found.' },
    { label: '⚠️ Dent & Deformation',    color: '#e67e22', detail: 'A dent or shape deformity was detected on the can body. Severity: Medium. Recommended action: Remove from production line.' },
    { label: '🔴 Rust & Corrosion',      color: '#e74c3c', detail: 'Rust spots or corrosion detected on the can surface. Severity: High. Recommended action: Discard immediately.' },
    { label: '⚠️ Bottle Cap Integrity',  color: '#e67e22', detail: 'Improper cap placement or broken seal detected. Severity: Medium. Recommended action: Re-cap or discard.' },
  ];

  const pick    = results[Math.floor(Math.random() * results.length)];
  const elapsed = (Math.random() * 1.5 + 0.3).toFixed(2);

  openModal(`
    <h3>🔍 Analysis Result</h3>
    <img class="preview" src="${imageDataUrl}" alt="Captured image" />
    <div style="
      margin-top:16px;
      padding:14px 18px;
      border-radius:10px;
      border-left:4px solid ${pick.color};
      background:${pick.color}11;
    ">
      <div style="font-weight:700;font-size:1rem;color:${pick.color};margin-bottom:6px;">${pick.label}</div>
      <div style="font-size:0.86rem;color:#6B7A93;">${pick.detail}</div>
    </div>
    <div style="margin-top:14px;font-size:0.8rem;color:#6B7A93;">
      ⏱ Processing time: <strong>${elapsed}s</strong> &nbsp;|&nbsp; Model accuracy: <strong>94.7%</strong>
    </div>
    <div style="margin-top:16px;display:flex;gap:10px;">
      <button class="btn btn-primary" id="analyseAnotherBtn">Analyse Another</button>
      <button class="btn btn-outline" id="closeResultBtn">Close</button>
    </div>
  `);

  document.getElementById('closeResultBtn').addEventListener('click', closeModal);
  document.getElementById('analyseAnotherBtn').addEventListener('click', () => {
    closeModal();
    triggerUpload();
  });
}

// ── Scroll-reveal for feature cards ──────────────────────────────────────────
const revealEls = document.querySelectorAll('.feature-card, .stat, .step');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity    = '1';
      entry.target.style.transform  = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

revealEls.forEach(el => {
  el.style.opacity   = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
  observer.observe(el);
});
