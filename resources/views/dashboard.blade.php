<!doctype html>
<html lang="th" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Amnat Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --green:#18a573;          /* โทนเขียวหลัก */
      --green-2:#21c08b;        /* ไล่สี */
      --blue:#0d6efd;           /* หัวตัวอักษร */
      --bg-1:#e9fbf2;           /* พื้นหลังอ่อน */
      --glass-bg:rgba(255,255,255,.7);
      --glass-bd:rgba(33, 192, 139, .35);
      --shadow:0 10px 30px rgba(24,165,115,.15);
      --radius:22px;
    }
    @media (prefers-color-scheme: dark) {
      html[data-bs-theme="dark"] {
        color-scheme: dark;
      }
    }
    /* พื้นหลัง gradient เคลื่อนไหวเบา ๆ */
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 800px at 10% -10%, rgba(33,192,139,.18), transparent 60%),
        radial-gradient(1000px 600px at 110% 10%, rgba(13,110,253,.14), transparent 60%),
        linear-gradient(135deg, #f6fffb 0%, var(--bg-1) 40%, #ffffff 100%);
      animation: floatBg 24s ease-in-out infinite alternate;
      background-attachment: fixed;
    }
    @keyframes floatBg{
      0%{background-position:0 0, 0 0, 0 0}
      100%{background-position:5% -3%, 95% 2%, 0 0}
    }

    /* เน้นหัวน้ำเงิน */
    .brand-title, h1, h2, h3, h4, .nav-link, .table thead th{ color: var(--blue); }

    /* Glass + Neumorphism */
    .glass{
      background: var(--glass-bg);
      border:1px solid var(--glass-bd);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .glass:hover{ transform: translateY(-2px); box-shadow: 0 16px 40px rgba(24,165,115,.22); border-color: rgba(33,192,139,.55); }

    .chip{
      border-radius: 999px;
      border:1px solid rgba(13,110,253,.2);
      background: rgba(13,110,253,.06);
      padding:.35rem .75rem; font-weight:600; color: var(--blue);
    }
    .btn-neo{
      border-radius: 14px;
      border:1px solid rgba(0,0,0,.06);
      background: linear-gradient(180deg, #ffffff, #f0fff8);
      box-shadow: 0 8px 20px rgba(24,165,115,.18);
      color: var(--green);
    }
    .btn-neo:hover{ filter: brightness(.98); transform: translateY(-1px); }
    .btn-ghost{
      border:1px solid rgba(24,165,115,.35); color: var(--green);
      background: rgba(33,192,139,.08);
    }
    .border-success-soft{ border:1px solid rgba(33,192,139,.45) !important; }
    .text-green{ color: var(--green) !important; }

    /* Progress slim */
    .progress.fine{ height: 8px; border-radius: 999px; background: rgba(33,192,139,.12); }
    .progress.fine .progress-bar{ background: linear-gradient(90deg, var(--green), var(--green-2)); border-radius: 999px; }

    /* Table */
    .table-green-border{ --bs-border-color: rgba(33,192,139,.3); border-color: var(--bs-border-color) !important; }
    .status-badge.success{background:#d1e7dd;color:#0f5132}
    .status-badge.pending{background:#fff3cd;color:#664d03}
    .status-badge.failed{background:#f8d7da;color:#842029}

    /* Dark toggle badge */
    .toggle{
      width:54px;height:30px;border-radius:999px;border:1px solid rgba(0,0,0,.12);
      background:#fff; position:relative; cursor:pointer;
      box-shadow: inset 0 0 0 2px rgba(24,165,115,.08);
    }
    .toggle .dot{
      position:absolute;top:3px;left:3px;width:24px;height:24px;border-radius:50%;
      background: linear-gradient(180deg,#f9fffd,#d5ffe8);
      box-shadow: 0 4px 10px rgba(0,0,0,.12);
      transition: left .25s ease;
    }
    html[data-bs-theme="dark"] .toggle{ background:#0f1214; border-color:#1c2a26; }
    html[data-bs-theme="dark"] .toggle .dot{ background: linear-gradient(180deg,#28312d,#0f1214); }
    .toggle.active .dot{ left:27px; }

    /* Compact container on xl */
    @media (min-width:1200px){
      .container.container-compact{ max-width: 1120px; }
    }
  </style>
</head>
<body>
  <!-- NAV -->
  <nav class="navbar navbar-expand-lg bg-white bg-opacity-75 border-bottom sticky-top glass" style="border-radius:0">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center brand-title fw-bold" href="#">
        <i class="bi bi-shield-check me-2 text-green"></i> Amnat Dashboard
      </a>      
      {{-- <div id="topnav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#summary">ภาพรวม</a></li>
          <li class="nav-item"><a class="nav-link" href="#insights">อินไซต์</a></li>
          <li class="nav-item"><a class="nav-link" href="#table">ตาราง</a></li>
        </ul>
      </div> --}}
    </div>
  </nav>

  <!-- HERO -->
  <header class="py-4">
    <div class="container">
      <form method="POST" action="{{ route('web.index') }}" enctype="multipart/form-data">
      @csrf
        <div class="row g-4 align-items-center">
          <div class="col-lg-9">          
            <h4 class="fw-bold mb-2">ข้อมูลบริการ ปีงบประมาณ {{$budget_year}}</h4>          
          </div>
          {{-- ขวาสุด: select + ปุ่ม ติดกันและชิดขวา --}}
          <div class="col-lg-3 d-flex justify-content-lg-end">
            <div class="d-flex align-items-center gap-2">
              <select class="form-select" name="budget_year">
                @foreach ($budget_year_select as $row)
                  <option value="{{ $row->LEAVE_YEAR_ID }}"
                    {{ (int)$budget_year === (int)$row->LEAVE_YEAR_ID ? 'selected' : '' }}>
                    {{ $row->LEAVE_YEAR_NAME }}
                  </option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-primary">{{ __('ค้นหา') }}</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </header>

<!-- SUMMARY (4 blocks, no foreach) -->
<section id="summary" class="pb-2">
  <div class="container">
    @php
      $fmtInt   = fn($n) => number_format((int)($n ?? 0));
      $fmtMoney = fn($n) => number_format((float)($n ?? 0), 2);
    @endphp

    <div class="row g-3">

      {{-- 1) UC-OP Anywhere : ครั้ง | บาท --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass p-3 h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 text-primary">UC-OP Anywhere</h6>
            <span class="text-success"><i class="bi bi-people fs-5"></i></span>
          </div>
          <div class="d-flex align-items-end gap-4">
            <div class="text-end">
              <div class="small text-secondary text-center">ครั้ง</div>
              <div class="fw-bold" style="font-size:1.5rem;">
                {{ $fmtInt($visit_ucs_outprov ?? 0) }}
              </div>
            </div>
            <div class="vr d-none d-sm-block"></div>
            <div class="text-end">
              <div class="small text-secondary text-center">บาท</div>
              <div class="fw-bold text-success" style="font-size:1.5rem;">
                {{ $fmtMoney($inc_ucs_outprov ?? 0) }}
              </div>
            </div>
          </div>
        </div>
      </div>      

      {{-- 2) UC-บริการเฉพาะ CR : ครั้ง | บาท --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass p-3 h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 text-primary">UC-บริการเฉพาะ CR</h6>
            <span class="text-danger"><i class="bi bi-hospital fs-5"></i></span>
          </div>
          <div class="d-flex align-items-end gap-4">
            <div class="text-end">
              <div class="small text-secondary text-center">ครั้ง</div>
              <div class="fw-bold " style="font-size:1.5rem;">
                {{ $fmtInt($visit_ucs_cr ?? 0) }}
              </div>
            </div>
            <div class="vr d-none d-sm-block"></div>
            <div class="text-end">
              <div class="small text-secondary text-center">บาท</div>
              <div class="fw-bold text-success" style="font-size:1.5rem;">
                {{ $fmtMoney($inc_uccr ?? 0) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 3) UC-สมุนไพร 32 รายการ : ครั้ง | บาท --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass p-3 h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 text-info">UC-สมุนไพร 32 รายการ</h6>
            <span class="text-success"><i class="bi bi-capsule fs-5"></i></span>
          </div>
          <div class="d-flex align-items-end gap-4">
            <div class="text-end">
              <div class="small text-secondary text-center">ครั้ง</div>
              <div class="fw-bold" style="font-size:1.5rem;">
                {{ $fmtInt($visit_ucs_herb ?? 0) }}
              </div>
            </div>
            <div class="vr d-none d-sm-block"></div>
            <div class="text-end">
              <div class="small text-secondary text-center">บาท</div>
              <div class="fw-bold text-success" style="font-size:1.5rem;">
                {{ $fmtMoney($inc_herb ?? 0) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 4) PP Fee Schedule : ครั้ง --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="glass p-3 h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0 text-primary">PP Fee Schedule</h6>
            <span class="text-warning"><i class="bi bi-diagram-3 fs-5"></i></span>
          </div>
          <div class="d-flex align-items-end gap-4">
            <div class="text-end">
              <div class="small text-secondary text-center">ครั้ง</div>
              <div class="fw-bold" style="font-size:1.5rem;">
                {{ $fmtInt($visit_ppfs ?? 0) }}
              </div>
            </div>
            <div class="vr d-none d-sm-block"></div>
            <div class="text-end">
              <div class="small text-secondary text-center">บาท</div>
              <div class="fw-bold text-success" style="font-size:1.5rem;">
                {{ $fmtMoney($inc_ppfs ?? 0) }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>  
</section>

<section id="hospital" class="pb-2">
  <div class="container py-4">
    <hr>
    <!-- NAV PILLS -->
    <ul class="nav nav-pills overflow-auto flex-nowrap" id="hospPills" role="tablist">
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link active" id="tab-10985" data-bs-toggle="pill" data-bs-target="#pane-10985" type="button" role="tab" aria-controls="pane-10985" aria-selected="true">
          รพ.ชานุมาน
        </button>
      </li>
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link" id="tab-10986" data-bs-toggle="pill" data-bs-target="#pane-10986" type="button" role="tab" aria-controls="pane-10986" aria-selected="false">
          รพ.ปทุมราชวงศา
        </button>
      </li>
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link" id="tab-10987" data-bs-toggle="pill" data-bs-target="#pane-10987" type="button" role="tab" aria-controls="pane-10987" aria-selected="false">
          รพ.พนา
        </button>
      </li>
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link" id="tab-10988" data-bs-toggle="pill" data-bs-target="#pane-10988" type="button" role="tab" aria-controls="pane-10988" aria-selected="false">
          รพ.เสนางคนิคม
        </button>
      </li>
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link" id="tab-10989" data-bs-toggle="pill" data-bs-target="#pane-10989" type="button" role="tab" aria-controls="pane-10989" aria-selected="false">
          รพ.หัวตะพาน
        </button>
      </li>
      <li class="nav-item me-2" role="presentation">
        <button class="nav-link" id="tab-10990" data-bs-toggle="pill" data-bs-target="#pane-10990" type="button" role="tab" aria-controls="pane-10990" aria-selected="false">
          รพ.ลืออำนาจ
        </button>
      </li>
    </ul>

    <!-- TAB PANES -->
    <div class="tab-content mt-3" id="hospPillsContent">

      <!-- 10985 -->
      <div class="tab-pane fade show active" id="pane-10985" role="tabpanel" aria-labelledby="tab-10985" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10985 โรงพยาชานุมาน</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th>
                  <th class="text-end">Visit</th>
                  <th class="text-end">OP</th>
                  <th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th>
                  <th class="text-end">UC-CR</th>
                  <th class="text-end">UC-Herb</th>
                  <th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th>
                  <th class="text-end">รายได้ Anywhere</th>
                  <th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td>
                  <td class="text-end">381</td>
                  <td class="text-end">363</td>
                  <td class="text-end">18</td>
                  <td class="text-end">244</td>
                  <td class="text-end">19</td>
                  <td class="text-end">49</td>
                  <td class="text-end">9</td>
                  <td class="text-end">331,657.40</td>
                  <td class="text-end">120,000.00</td>
                  <td class="text-end">25,500.00</td>
                </tr>
                <!-- เพิ่มแถวข้อมูลตามต้องการ -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10986 -->
      <div class="tab-pane fade" id="pane-10986" role="tabpanel" aria-labelledby="tab-10986" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10986 โรงพยาปทุมราชวงศา</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th><th class="text-end">Visit</th><th class="text-end">OP</th><th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th><th class="text-end">UC-CR</th><th class="text-end">UC-Herb</th><th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th><th class="text-end">รายได้ Anywhere</th><th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td><td class="text-end">320</td><td class="text-end">305</td><td class="text-end">15</td>
                  <td class="text-end">210</td><td class="text-end">12</td><td class="text-end">35</td><td class="text-end">7</td>
                  <td class="text-end">280,000.00</td><td class="text-end">95,000.00</td><td class="text-end">18,200.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10987 -->
      <div class="tab-pane fade" id="pane-10987" role="tabpanel" aria-labelledby="tab-10987" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10987 โรงพยาพนา</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th><th class="text-end">Visit</th><th class="text-end">OP</th><th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th><th class="text-end">UC-CR</th><th class="text-end">UC-Herb</th><th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th><th class="text-end">รายได้ Anywhere</th><th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td><td class="text-end">250</td><td class="text-end">238</td><td class="text-end">12</td>
                  <td class="text-end">160</td><td class="text-end">8</td><td class="text-end">22</td><td class="text-end">5</td>
                  <td class="text-end">210,000.00</td><td class="text-end">70,000.00</td><td class="text-end">12,900.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10988 -->
      <div class="tab-pane fade" id="pane-10988" role="tabpanel" aria-labelledby="tab-10988" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10988 โรงพยาเสนางคนิคม</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th><th class="text-end">Visit</th><th class="text-end">OP</th><th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th><th class="text-end">UC-CR</th><th class="text-end">UC-Herb</th><th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th><th class="text-end">รายได้ Anywhere</th><th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td><td class="text-end">290</td><td class="text-end">275</td><td class="text-end">15</td>
                  <td class="text-end">185</td><td class="text-end">10</td><td class="text-end">28</td><td class="text-end">8</td>
                  <td class="text-end">240,000.00</td><td class="text-end">80,000.00</td><td class="text-end">15,400.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10989 -->
      <div class="tab-pane fade" id="pane-22222" role="tabpanel" aria-labelledby="tab-22222" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10989 โรงพยาหัวตะพาน</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th><th class="text-end">Visit</th><th class="text-end">OP</th><th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th><th class="text-end">UC-CR</th><th class="text-end">UC-Herb</th><th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th><th class="text-end">รายได้ Anywhere</th><th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td><td class="text-end">260</td><td class="text-end">246</td><td class="text-end">14</td>
                  <td class="text-end">170</td><td class="text-end">9</td><td class="text-end">25</td><td class="text-end">6</td>
                  <td class="text-end">220,000.00</td><td class="text-end">75,000.00</td><td class="text-end">13,500.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10990 -->
      <div class="tab-pane fade" id="pane-10990" role="tabpanel" aria-labelledby="tab-10990" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-decoration-underline">10990 โรงพยาลืออำนาจ</h6>
            <span class="text-secondary small">ปีงบประมาณ {{$budget_year}}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>วันที่</th><th class="text-end">Visit</th><th class="text-end">OP</th><th class="text-end">PP</th>
                  <th class="text-end">UC-OP Anywhere</th><th class="text-end">UC-CR</th><th class="text-end">UC-Herb</th><th class="text-end">PPFS</th>
                  <th class="text-end">รายได้รวม</th><th class="text-end">รายได้ Anywhere</th><th class="text-end">รายได้ Herb</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-10-01</td><td class="text-end">240</td><td class="text-end">229</td><td class="text-end">11</td>
                  <td class="text-end">150</td><td class="text-end">7</td><td class="text-end">20</td><td class="text-end">4</td>
                  <td class="text-end">195,000.00</td><td class="text-end">60,000.00</td><td class="text-end">10,800.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- FOOTER -->
<footer class="py-4">
  <div class="container container-compact text-center text-secondary small">
    © {{ now()->year }} Amnat Dashboard
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

</body>
</html>
