/**
 * history.js – Logic untuk halaman history CanCap Detect
 */

function applyFilter() {
  const status = document.getElementById('filterStatus').value;
  const model  = document.getElementById('filterModel').value;
  const params = new URLSearchParams();
  if (status) params.set('status', status);
  if (model)  params.set('model', model);
  window.location.href = '/history?' + params.toString();
}

async function deleteItem(id) {
  if (!confirm('Delete this inspection record?')) return;

  const resp = await fetch(`/detection/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  });

  const data = await resp.json();
  if (data.success) {
    const card = document.getElementById(`card-${id}`);
    card.style.opacity = '0';
    card.style.transform = 'scale(0.95)';
    card.style.transition = 'all 0.3s ease';
    setTimeout(() => card.remove(), 300);
  } else {
    alert('Failed to delete.');
  }
}

async function viewDetail(id) {
  const resp = await fetch(`/detection/${id}`, {
    headers: { 'Accept': 'application/json' }
  });
  const data = await resp.json();
  if (!data.success) return;

  const d = data.detection;
  const detections = data.detections ?? [];

  const resultImgUrl   = d.result_image   ? `/storage/${d.result_image}`   : null;
  const originalImgUrl = d.original_image ? `/storage/${d.original_image}` : null;

  document.getElementById('modalContent').innerHTML = `
    <h3 style="margin:0 0 1rem; font-size:1.1rem; font-weight:700;">Inspection Detail</h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.8rem; margin-bottom:1rem;">
      ${originalImgUrl ? `
        <div>
          <div style="font-size:0.75rem; color:#6B7280; margin-bottom:4px;">Original</div>
          <img src="${originalImgUrl}" style="width:100%; border-radius:8px; object-fit:cover; max-height:200px;">
        </div>` : ''}
      ${resultImgUrl ? `
        <div>
          <div style="font-size:0.75rem; color:#6B7280; margin-bottom:4px;">Detected</div>
          <img src="${resultImgUrl}" style="width:100%; border-radius:8px; object-fit:cover; max-height:200px;">
        </div>` : ''}
    </div>
    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1rem; font-size:0.85rem; color:#6B7280;">
      <span>Model: <strong style="color:#111827">${(d.model_used || '').toUpperCase()}</strong></span>
      <span>Status: <strong style="color:${d.status === 'defective' ? '#E24B4A' : '#639922'}">${d.status === 'defective' ? 'Defective' : 'Good'}</strong></span>
      <span>Defects: <strong style="color:#111827">${d.total_defects}</strong></span>
      <span>Time: <strong style="color:#111827">${d.processing_time ? d.processing_time + 's' : 'N/A'}</strong></span>
    </div>
    ${detections.length > 0 ? `
      <div style="font-size:0.85rem; font-weight:600; color:#374151; margin-bottom:0.5rem;">Detected Objects</div>
      <div style="display:flex; flex-direction:column; gap:0.5rem;">
        ${detections.map(det => {
          const isDefect = !det.label.toLowerCase().includes('no defect');
          const conf = Math.round(det.confidence * 100);
          return `
            <div style="display:flex; align-items:center; gap:0.8rem; padding:0.6rem 0.8rem; background:#F9FAFB; border-radius:8px; border:1px solid #E5E9F0;">
              <div style="width:8px; height:8px; border-radius:50%; background:${isDefect ? '#E24B4A' : '#639922'}; flex-shrink:0;"></div>
              <span style="flex:1; font-size:0.85rem; color:#111827;">${det.label}</span>
              <span style="font-size:0.8rem; color:#6B7280;">${conf}%</span>
            </div>
          `;
        }).join('')}
      </div>
    ` : '<p style="font-size:0.85rem; color:#9CA3AF;">No detections found.</p>'}
  `;

  document.getElementById('detailModal').style.display = 'flex';
}

function closeModal(e) {
  if (e.target.id === 'detailModal') {
    document.getElementById('detailModal').style.display = 'none';
  }
}