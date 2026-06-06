/**
 * detect.js – Logic untuk halaman deteksi CanCap Detect
 */

// ── State ──────────────────────────────────────────────────────────────────────
let selectedFile   = null;
let capturedBlob   = null;
let cameraStream   = null;
let activeTab      = 'upload';

// ── Model Selection ────────────────────────────────────────────────────────────
document.querySelectorAll('input[name="model"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.model-card').forEach(c => c.classList.remove('active'));
    radio.closest('.model-card').classList.add('active');
    updateDetectButton();
  });
});

function getSelectedModel() {
  return document.querySelector('input[name="model"]:checked')?.value ?? 'yolov8';
}

// ── Tab Switching ──────────────────────────────────────────────────────────────
function switchTab(tab) {
  activeTab = tab;
  document.getElementById('panelUpload').style.display  = tab === 'upload' ? '' : 'none';
  document.getElementById('panelCamera').style.display  = tab === 'camera' ? '' : 'none';
  document.getElementById('tabUpload').classList.toggle('active', tab === 'upload');
  document.getElementById('tabCamera').classList.toggle('active', tab === 'camera');

  if (tab !== 'camera') stopCamera();
  updateDetectButton();
}

// ── File Upload ────────────────────────────────────────────────────────────────
const fileInput  = document.getElementById('fileInputDetect');
const dropzone   = document.getElementById('dropzone');

fileInput.addEventListener('change', e => {
  if (e.target.files[0]) setUploadFile(e.target.files[0]);
});
dropzone.addEventListener('click', () => {
  document.getElementById('fileInputDetect').click();
});
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
dropzone.addEventListener('dragleave', ()  => dropzone.classList.remove('dragover'));
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  dropzone.classList.remove('dragover');
  if (e.dataTransfer.files[0]) setUploadFile(e.dataTransfer.files[0]);
});

function setUploadFile(file) {
  if (!file.type.startsWith('image/')) {
    alert('Please select a valid image file.');
    return;
  }
  selectedFile = file;
  const reader = new FileReader();
  reader.onload = ev => {
    document.getElementById('previewImage').src = ev.target.result;
    document.getElementById('previewContainer').style.display = '';
    document.getElementById('dropzone').style.display = 'none';
  };
  reader.readAsDataURL(file);
  updateDetectButton();
}

function removePreview() {
  selectedFile = null;
  fileInput.value = '';
  document.getElementById('previewContainer').style.display = 'none';
  document.getElementById('dropzone').style.display = '';
  updateDetectButton();
}

// ── Camera ─────────────────────────────────────────────────────────────────────
async function startCamera() {
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    const video  = document.getElementById('cameraFeed');
    video.srcObject = cameraStream;
    document.getElementById('btnStartCamera').style.display = 'none';
    document.getElementById('btnCapture').style.display    = '';
    document.getElementById('btnStopCamera').style.display = '';
  } catch (err) {
    alert('Cannot access camera: ' + err.message);
  }
}

function captureFrame() {
  const video  = document.getElementById('cameraFeed');
  const canvas = document.getElementById('cameraCanvas');
  canvas.width  = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0);
  canvas.toBlob(blob => {
    capturedBlob = blob;
    const url    = URL.createObjectURL(blob);
    document.getElementById('capturedImage').src = url;
    document.getElementById('capturedContainer').style.display = '';
    stopCamera();
    updateDetectButton();
  }, 'image/jpeg', 0.92);
}

function removeCaptured() {
  capturedBlob = null;
  document.getElementById('capturedContainer').style.display = 'none';
  startCamera();
  updateDetectButton();
}

function stopCamera() {
  if (cameraStream) {
    cameraStream.getTracks().forEach(t => t.stop());
    cameraStream = null;
  }
  document.getElementById('btnStartCamera').style.display = '';
  document.getElementById('btnCapture').style.display    = 'none';
  document.getElementById('btnStopCamera').style.display = 'none';
}

// ── Button State ───────────────────────────────────────────────────────────────
function updateDetectButton() {
  const hasImage = activeTab === 'upload' ? !!selectedFile : !!capturedBlob;
  const btn      = document.getElementById('btnDetect');
  const hint     = document.getElementById('detectHint');
  btn.disabled   = !hasImage;
  hint.textContent = hasImage
    ? `Ready to detect with ${getSelectedModel().toUpperCase()}`
    : 'Select a model and provide an image first';
}

// ── Run Detection ──────────────────────────────────────────────────────────────
async function runDetection() {
  const model   = getSelectedModel();
  const imageData = activeTab === 'upload' ? selectedFile : capturedBlob;
  if (!imageData) return;

  // Simpan preview original
  const originalSrc = activeTab === 'upload'
    ? document.getElementById('previewImage').src
    : document.getElementById('capturedImage').src;

  // Show loading
  document.getElementById('loadingSection').style.display = '';
  document.getElementById('loadingModel').textContent = model.toUpperCase();
  document.getElementById('resultSection').style.display = 'none';
  document.getElementById('btnDetect').disabled = true;

  const formData = new FormData();
  formData.append('image', imageData, 'capture.jpg');
  formData.append('model', model);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

  try {
    const resp = await fetch('/detect', { method: 'POST', body: formData });
    const data = await resp.json();

    document.getElementById('loadingSection').style.display = 'none';

    if (!data.success) {
      alert('Detection failed: ' + (data.message || 'Unknown error'));
      document.getElementById('btnDetect').disabled = false;
      return;
    }

    showResults(data, originalSrc);

  } catch (err) {
    document.getElementById('loadingSection').style.display = 'none';
    alert('Network error: ' + err.message);
    document.getElementById('btnDetect').disabled = false;
  }
}

// ── Show Results ───────────────────────────────────────────────────────────────
function showResults(data, originalSrc) {
  // Images
  document.getElementById('resultOriginal').src  = originalSrc;
  document.getElementById('resultDetected').src  = data.result_image || originalSrc;
  console.log('result_image:', data.result_image); // ← tambah ini
  console.log('full data:', data);                 // ← dan ini
  removePreview();
  document.getElementById('resultOriginal').src  = originalSrc;
  document.getElementById('resultDetected').src  = data.result_image || originalSrc;

  // Summary
  const isDefective = data.status === 'defective';
  const summaryEl   = document.getElementById('resultSummary');
  summaryEl.className = `result-summary ${isDefective ? 'defective' : 'good'}`;
  summaryEl.innerHTML = `
    <div class="summary-icon">${isDefective ? '⚠️' : '✅'}</div>
    <div class="summary-info">
      <div class="summary-status">${isDefective ? 'Defect Detected' : 'No Defect Found'}</div>
      <div class="summary-meta">
        Model: <strong>${(data.model_used || '').toUpperCase()}</strong> &nbsp;|&nbsp;
        Defects: <strong>${data.total_defects}</strong> &nbsp;|&nbsp;
        Time: <strong>${data.processing_time ? data.processing_time + 's' : 'N/A'}</strong>
      </div>
    </div>
  `;

  // Detections list
  const listEl = document.getElementById('detectionsList');
  if (data.detections && data.detections.length > 0) {
    listEl.innerHTML = `
      <h3 class="detections-title">Detected Objects (${data.detections.length})</h3>
      <div class="detections-grid">
        ${data.detections.map(d => {
          const isDefect = !d.label.toLowerCase().includes('no defect');
          const conf = Math.round(d.confidence * 100);
          return `
            <div class="detection-item ${isDefect ? 'defect' : 'good'}">
              <div class="detection-icon">
                ${isDefect
                  ? '<svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path stroke="#E24B4A" stroke-width="2" stroke-linecap="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'
                  : '<svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path stroke="#639922" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline stroke="#639922" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 4 12 14.01 9 11.01"/></svg>'
                }
              </div>
              <div class="detection-label">${d.label}</div>
              <div class="detection-conf-text">Confidence: ${conf}%</div>
              <div class="detection-bar-bg">
                <div class="detection-bar ${isDefect ? 'defect' : 'good'}" data-target="${conf}" style="width:0%"></div>
              </div>
            </div>
          `;
        }).join('')}
      </div>
    `;
  } else {
    listEl.innerHTML = '<p class="no-detections">No objects detected above confidence threshold.</p>';
  }
  // Tampilkan info per kelas
  const infoEl = document.getElementById('detectionInfo');
  const detectedLabels = data.detections.map(d => d.label.toLowerCase());

  const INFO_MAP = {
    'can\ncritical defect': {
      category: 'Kaleng (Can Inspection)',
      title: 'Critical Defect Detected',
      desc: 'Kerusakan kritis terdeteksi pada kaleng, seperti penyok sangat besar, deformasi parah, atau lubang yang mengancam integritas kemasan.',
      action: 'Produk tidak aman untuk didistribusikan. Segera pisahkan dan buang dari lini produksi.',
      isDefect: true
    },
    'can\nmajor defect': {
      category: 'Kaleng (Can Inspection)',
      title: 'Major Defect Detected',
      desc: 'Kerusakan serius terdeteksi pada kaleng, seperti penyok besar, deformasi bentuk, lubang, atau kerusakan yang dapat memengaruhi integritas kemasan.',
      action: 'Produk berpotensi tidak aman untuk didistribusikan. Segera pisahkan dari lini produksi dan lakukan pemeriksaan lebih lanjut.',
      isDefect: true
    },
    'can\nminor defect': {
      category: 'Kaleng (Can Inspection)',
      title: 'Minor Defect Detected',
      desc: 'Kerusakan ringan terdeteksi pada permukaan kaleng, seperti goresan kecil atau penyok ringan yang tidak secara langsung memengaruhi fungsi kemasan.',
      action: 'Disarankan untuk dilakukan inspeksi manual guna memastikan produk masih memenuhi standar kualitas.',
      isDefect: true
    },
    'can\nno defect': {
      category: 'Kaleng (Can Inspection)',
      title: 'No Defect Detected',
      desc: 'Tidak ditemukan kerusakan pada kaleng. Bentuk dan kondisi kemasan sesuai dengan standar kualitas yang ditetapkan.',
      action: 'Produk dapat melanjutkan ke tahap produksi atau distribusi berikutnya.',
      isDefect: false
    },
    'cap\nno defect': {
      category: 'Tutup Botol (Bottle Cap Inspection)',
      title: 'Cap Condition: Good',
      desc: 'Tutup botol terpasang dengan baik dan tidak ditemukan indikasi kerusakan, deformasi, atau ketidaksesuaian.',
      action: 'Produk memenuhi standar kualitas dan aman untuk diproses lebih lanjut.',
      isDefect: false
    },
    'cap\ndefect': {
      category: 'Tutup Botol (Bottle Cap Inspection)',
      title: 'Cap Defect Detected',
      desc: 'Kerusakan atau ketidaksesuaian terdeteksi pada tutup botol, seperti penyok, retak, pemasangan tidak sempurna, atau deformasi bentuk.',
      action: 'Produk sebaiknya dipisahkan untuk inspeksi lanjutan guna mencegah risiko kebocoran atau penurunan kualitas produk.',
      isDefect: true
    },
  };

  const shownCategories = new Set();
  let infoHTML = '<h3 class="detections-title">Inspection Report</h3>';

  data.detections.forEach(d => {
    const key = d.label.toLowerCase();
    const info = INFO_MAP[key];
    console.log('key:', JSON.stringify(key), '| found:', !!info);
    if (!info) return;

    if (!shownCategories.has(info.category)) {
      infoHTML += `<div class="info-category">${info.category}</div>`;
      shownCategories.add(info.category);
    }

    infoHTML += `
      <div class="info-card ${info.isDefect ? 'defect' : 'good'}">
        <div class="info-title ${info.isDefect ? 'defect' : 'good'}">
          ${info.title}
        </div>
        <div class="info-desc">${info.desc}</div>
        <div class="info-action">${info.action}</div>
      </div>
    `;
  });

  infoEl.innerHTML = infoHTML;
  document.getElementById('resultSection').style.display = '';
  document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
  // Animasi confidence bar
  // Animasi confidence bar
  setTimeout(() => {
    document.querySelectorAll('.detection-bar').forEach(bar => {
      const target = bar.getAttribute('data-target');
      bar.style.width = '0%';
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          bar.style.width = target + '%';
        });
      });
    });
  }, 100);
  setTimeout(() => {
    document.querySelectorAll('.detection-bar').forEach(bar => {
      const target = bar.style.width;
      bar.style.width = '0%';
      setTimeout(() => { bar.style.width = target; }, 200);
    });
  }, 300);
}

// ── Reset ──────────────────────────────────────────────────────────────────────
function resetDetection() {
  selectedFile  = null;
  capturedBlob  = null;
  fileInput.value = '';

  document.getElementById('previewContainer').style.display  = 'none';
  document.getElementById('dropzone').style.display          = '';
  document.getElementById('capturedContainer').style.display = 'none';
  document.getElementById('resultSection').style.display     = 'none';
  document.getElementById('btnDetect').disabled              = false;

  switchTab('upload');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Auto-open berdasarkan sessionStorage ───────────────────────────────────
const method = sessionStorage.getItem('detect_method');
if (method) {
  sessionStorage.removeItem('detect_method');
  if (method === 'camera') {
    switchTab('camera');
    startCamera();
  } else if (method === 'upload') {
    switchTab('upload');
    document.getElementById('dropzone').classList.add('dragover');
    setTimeout(() => {
      document.getElementById('dropzone').classList.remove('dragover');
    }, 1500);
  } 
}