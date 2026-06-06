<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History – CanCap Detect</title>
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
    <img class="logo_binus" src="{{ asset('images/logo_binus.png') }}" alt="Binus">
  </nav>
  <div class="back-wrapper">
    <button class="btn-back" onclick="window.location.href='{{ route('detect') }}'">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 5l-7 7 7 7"/>
      </svg>
      Back
    </button>
  </div>

  <div class="history-page">

    <!-- Header -->
    <div class="history-header">
      <div>
        <div class="section-tag">Detection History</div>
        <h2>All Inspections</h2>
        <p class="history-sub">Total: <strong>{{ $results->total() }}</strong> inspections recorded</p>
      </div>
      <a href="{{ route('detect') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
          <circle cx="8" cy="8" r="6" stroke="#fff" stroke-width="1.6"/>
          <circle cx="8" cy="8" r="2.5" fill="#fff"/>
        </svg>
        New Detection
      </a>
    </div>

    <!-- Filters -->
    <div class="history-filters">
      <select id="filterStatus" onchange="applyFilter()">
        <option value="">All Status</option>
        <option value="defective" {{ request('status') == 'defective' ? 'selected' : '' }}>Defective</option>
        <option value="good"      {{ request('status') == 'good'      ? 'selected' : '' }}>Good / No Defect</option>
      </select>
      <select id="filterModel" onchange="applyFilter()">
        <option value="">All Models</option>
        <option value="yolov8"  {{ request('model') == 'yolov8'  ? 'selected' : '' }}>YOLOv8</option>
        <option value="yolov11" {{ request('model') == 'yolov11' ? 'selected' : '' }}>YOLOv11</option>
      </select>
      @if(request('status') || request('model'))
        <a href="{{ route('history') }}" class="btn btn-outline" style="padding: 0.45rem 1rem; font-size:0.85rem;">Clear Filter</a>
      @endif
    </div>

    <!-- Grid -->
    @if($results->count() > 0)
      <div class="history-grid">
        @foreach($results as $item)
          <div class="history-card" id="card-{{ $item->id }}">

            <!-- Thumbnail -->
            <div class="history-thumb">
              @if($item->result_image)
                <img src="{{ url('storage/' . $item->result_image) }}" alt="Result" loading="lazy">
              @elseif($item->original_image)
                <img src="{{ url('storage/' . $item->original_image) }}" alt="Original" loading="lazy">
              @else
                <div class="history-thumb-placeholder">
                  <svg width="32" height="32" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3" stroke="#9CA3AF" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" fill="#9CA3AF"/><path d="M3 15l5-5 4 4 3-3 6 6" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
              @endif
              <div class="history-status-badge {{ $item->status }}">
                {{ $item->status === 'defective' ? 'Defective' : 'Good' }}
              </div>
            </div>

            <!-- Info -->
            <div class="history-info">
              <div class="history-meta">
                <span class="history-model">{{ strtoupper($item->model_used) }}</span>
                <span class="history-time">{{ $item->created_at->format('d M Y, H:i') }}</span>
              </div>
              <div class="history-stats">
                <div class="history-stat">
                  <div class="history-stat-val">{{ $item->total_defects }}</div>
                  <div class="history-stat-label">Defects</div>
                </div>
                <div class="history-stat">
                  <div class="history-stat-val">{{ count(json_decode($item->detections ?? '[]', true)) }}</div>
                  <div class="history-stat-label">Objects</div>
                </div>
                <div class="history-stat">
                  <div class="history-stat-val">{{ $item->processing_time ? $item->processing_time . 's' : '-' }}</div>
                  <div class="history-stat-label">Time</div>
                </div>
              </div>

              <!-- Detections -->
              @php $detections = json_decode($item->detections ?? '[]', true); @endphp
              @if(count($detections) > 0)
                <div class="history-labels">
                  @foreach(array_slice($detections, 0, 3) as $det)
                    <span class="history-label {{ str_contains(strtolower($det['label']), 'no defect') ? 'good' : 'defect' }}">
                      {{ $det['label'] }}
                    </span>
                  @endforeach
                  @if(count($detections) > 3)
                    <span class="history-label-more">+{{ count($detections) - 3 }} more</span>
                  @endif
                </div>
              @endif

              <!-- Actions -->
              <div class="history-actions">
                <button class="btn-icon" onclick="viewDetail({{ $item->id }})" title="View Detail">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                </button>
                <button class="btn-icon danger" onclick="deleteItem({{ $item->id }})" title="Delete">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><polyline stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" points="3 6 5 6 21 6"/><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Pagination -->
      <div class="history-pagination">
        <p class="pagination-info">
          Showing {{ $results->firstItem() }} to {{ $results->lastItem() }} of {{ $results->total() }} results
        </p>
        <div class="pagination-links">
          {{-- Previous --}}
          @if($results->onFirstPage())
            <span class="page-btn disabled">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
              Previous
            </span>
          @else
            <a href="{{ $results->appends(request()->query())->previousPageUrl() }}" class="page-btn">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
              Previous
            </a>
          @endif

          {{-- Page Numbers --}}
          @foreach($results->getUrlRange(1, $results->lastPage()) as $page => $url)
            @if($page == $results->currentPage())
              <span class="page-btn active">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
            @endif
          @endforeach

          {{-- Next --}}
          @if($results->hasMorePages())
            <a href="{{ $results->appends(request()->query())->nextPageUrl() }}" class="page-btn">
              Next
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </a>
          @else
            <span class="page-btn disabled">
              Next
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </span>
          @endif
        </div>
      </div>

    @else
      <div class="history-empty">
        <svg width="48" height="48" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3" stroke="#D1D5DB" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" fill="#D1D5DB"/><path d="M3 15l5-5 4 4 3-3 6 6" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <p>No inspection history yet</p>
        <a href="{{ route('detect') }}" class="btn btn-primary">Start Detecting</a>
      </div>
    @endif

  </div>

  <!-- Detail Modal -->
  <div class="modal-overlay" id="detailModal" style="display:none" onclick="closeModal(event)">
    <div class="modal">
      <button class="modal-close" onclick="document.getElementById('detailModal').style.display='none'">&times;</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    &copy; 2026 CanCap Detect. AI-powered defect detection
  </footer>

  <script src="{{ asset('js/history.js') }}"></script>
</body>
</html>