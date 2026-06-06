/* =====================
   CANCAP DETECT — main.js
   ===================== */

// ── Navigasi ke halaman detect ─────────────────────────────────────────────
function goToDetect(method) {
  sessionStorage.setItem('detect_method', method);
  window.location.href = '/detect';
}

document.getElementById('btnOpenCameraHero').addEventListener('click', () => goToDetect('camera'));
document.getElementById('btnUploadHero').addEventListener('click', () => goToDetect('upload'));
document.getElementById('btnOpenCameraCTA').addEventListener('click', () => goToDetect('camera'));
document.getElementById('btnUploadCTA').addEventListener('click', () => goToDetect('upload'));

const fileInputHome = document.getElementById('fileInput');
if (fileInputHome) {
  fileInputHome.addEventListener('change', () => goToDetect('upload'));
}

// ── Scroll-reveal ──────────────────────────────────────────────────────────
const revealEls = document.querySelectorAll('.feature-card, .stat, .step');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity   = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

revealEls.forEach(el => {
  el.style.opacity    = '0';
  el.style.transform  = 'translateY(20px)';
  el.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
  observer.observe(el);
});