<!doctype html>
<html lang="th" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Amnatcharoen Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap 5 (optional for styling DataTables BS5 skin) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- DataTables + Buttons + Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center brand-title fw-bold" href="#">
        <i class="bi bi-shield-check me-2 text-green"></i> Amnatcharoen Dashboard
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
    <div class="container-fluid">      
        <div class="row g-4 align-items-center">
          <div class="col-lg-9">          
            <h6 class="text-success mb-2"><strong>One Province One Hospital (OPOH) </strong></h6>          
          </div>
          {{-- ขวาสุด: select + ปุ่ม ติดกันและชิดขวา --}}
          <div class="col-lg-3 d-flex justify-content-lg-end">
            <span class="text-secondary my-1">
                วันที่ {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY เวลา H:mm') }} น.&nbsp;&nbsp;
            </span>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="location.reload();">
              <i class="bi bi-arrow-clockwise"></i> โหลดใหม่
            </button>
          </div>
        </div>
    </div>
  </header>

<!-- SUMMARY (4 blocks, no foreach) -->
<section id="summary" class="pb-2">
  <div class="container-fluid">
    @php
      $fmtInt   = fn($n) => number_format((int)($n ?? 0));
      $fmtMoney = fn($n) => number_format((float)($n ?? 0), 2);
    @endphp

    <div class="row g-3">
      
      {{--  กำลังรักษาอยู่ ------------------------------------------------------------------------------------------------------------ --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#AdmiitDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-danger"><strong>กำลังรักษาอยู่ </strong></h6>
              <span> <i class="bi bi-hospital fs-5 text-danger"></i> </span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">AN</div>
                <div class="fw-bold text-danger" style="font-size:1.75rem;">
                  {{ $fmtInt($total_bed_qty ?? 0) }}
                </div>
              </div>
              <div class="vr d-none d-sm-block"></div>
              <div class="text-end">
                <div class="small text-secondary text-center">เตียงว่าง</div>
                <div class="fw-bold text-success" style="font-size:1.75rem;">
                  {{ $fmtInt($total_bed_empty ?? 0) }}
                </div>
              </div>
            </div>              
          </div>
        </a>    
      </div>      
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="AdmiitDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                ข้อมูลจำนวนเตียง
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">จำนวนเตียง</th>
                    <th class="text-center">ใช้ไป</th>
                    <th class="text-center">ว่าง</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitals as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ $h->bed_qty }}</td>
                      <td align="right">{{ $h->bed_use }}</td>
                      <td align="right" class="text-success fw-bold">{{ $h->bed_qty - $h->bed_use }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      {{--  ส่งต่อ-------------------------------------------------------------------------------------------------------------- --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#ReferDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary"><strong>การส่งต่อ Refer</strong></h6>              
              <span> <i class="bi bi-truck fs-5 text-danger"></i> </span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">ในจังหวัด</div>
                <div class="fw-bold" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_referout_inprov ?? 0) }}
                </div>
              </div>
              <div class="vr d-none d-sm-block"></div>
              <div class="text-end">
                <div class="small text-secondary text-center">ต่างจังหวัด</div>
                <div class="fw-bold text-primary" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_referout_outprov ?? 0) }}
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="ReferDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                การส่งต่อ Refer
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">ในจังหวัด</th>
                    <th class="text-center">ต่างจังหวัด</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>                     
                      <td align="right">{{ $h->visit_referout_inprov }}</td>
                      <td align="right">{{ $h->visit_referout_outprov }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      {{--  Visit Total : ครั้ง----------------------------------------------------------------------------------------------- --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#VisitDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">จำนวนผู้มารับบริการผู้ป่วยนอก</h6>
              <span><i class="bi bi-person-heart fs-5 text-primary"></i> </span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">visit total</div>
                <div class="fw-bold" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_total ?? 0) }}
                </div>
              </div>
              <div class="vr d-none d-sm-block"></div>
              <div class="text-end">
                <div class="small text-secondary text-center">ปิดสิทธิ สปสช.</div>
                <div class="fw-bold text-primary" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_endpoint ?? 0) }}
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="VisitDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                จำนวนผู้มารับบริการผู้ป่วยนอก
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit</th>
                    <th class="text-center">ปิดสิทธิ สปสช.</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_total) }}</td>
                      <td align="right">{{ number_format($h->visit_endpoint) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      {{--  Visit OP_PP : ครั้ง------------------------------------------------------------------------------------------------ --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#VisitOPDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">จำนวนผู้มารับบริการ OP-PP</h6>
              <span><i class="bi bi-person-heart fs-5 text-success"></i></span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">op visit</div>
                <div class="fw-bold" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_total_op ?? 0) }}
                </div>
              </div>
              <div class="vr d-none d-sm-block"></div>
              <div class="text-end">
                <div class="small text-secondary text-center">pp visit</div>
                <div class="fw-bold text-primary" style="font-size:1.75rem;">
                  {{ $fmtInt($visit_total_pp ?? 0) }}
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="VisitOPDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                จำนวนผู้มารับบริการ OP-PP
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit OP</th>
                    <th class="text-center">Visit PP</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_total_op) }}</td>
                      <td align="right">{{ number_format($h->visit_total_pp) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      {{-- -------------------------------------------------------------------------------------------------------------- --}}
    </div>
  </div>  
</section>
<br>

<!-- SUMMARY (4 blocks, no foreach) -->
<section id="summary" class="pb-2">
  <div class="container-fluid">
    @php
      $fmtInt   = fn($n) => number_format((int)($n ?? 0));
      $fmtMoney = fn($n) => number_format((float)($n ?? 0), 2);
    @endphp

    <div class="row g-3">      

      {{--  PP Fee Schedule : ครั้ง -------------------------------------------------------------------------------}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#PPFSDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">PP Fee Schedule</h6>
              <span><i class="bi bi-diagram-3 fs-5 text-warning"></i></span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">visit</div>
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
        </a>
      </div>
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="PPFSDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                PP Fee Schedule
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit</th>
                    <th class="text-center">ค่ารักษารวม</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_ppfs) }}</td>
                      <td align="right">{{ number_format($h->inc_ppfs,2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>

      {{--  UC-OP Anywhere : ครั้ง | บาท -------------------------------------------------------------------------------------------}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#AnywhereDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">UC-OP Anywhere</h6>
              <span><i class="bi bi-people fs-5 text-info"></i> </span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">visit</div>
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
        </a>
      </div> 
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="AnywhereDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                UC-OP Anywhere
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit</th>
                    <th class="text-center">ค่ารักษารวม</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_ucs_outprov) }}</td>
                      <td align="right">{{ number_format($h->inc_ucs_outprov,2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>     

      {{-- UC-บริการเฉพาะ CR : ครั้ง | บาท ---------------------------------------------------------------------------------------}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#CrDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">UC-บริการเฉพาะ CR </h6>
              <span><i class="bi bi-hospital fs-5 text-success"></i> </span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">visit</div>
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
          </a>
      </div>
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="CrDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                UC-OP Anywhere
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit</th>
                    <th class="text-center">ค่ารักษารวม</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_ucs_cr) }}</td>
                      <td align="right">{{ number_format($h->inc_uccr,2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>  

      {{-- UC-สมุนไพร 32 รายการ : ครั้ง | บาท -----------------------------------------------------------------------------------}}
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="#" data-bs-toggle="modal" data-bs-target="#HerbDetailModal" class="text-decoration-none text-dark">
          <div class="glass p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="mb-0 text-primary">UC-สมุนไพร 32 รายการ</h6>
              <span><i class="bi bi-capsule fs-5 text-danger"></i></span></span>
            </div>
            <div class="d-flex align-items-end gap-4">
              <div class="text-end">
                <div class="small text-secondary text-center">visit</div>
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
        </a>
      </div>   
      {{-- Modal แสดงรายละเอียด รพ. --}}
      <div class="modal fade" id="HerbDetailModal" tabindex="-1" aria-labelledby="hospitalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="hospitalDetailLabel">
                UC-OP Anywhere
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th class="text-center">รหัส</th>
                    <th class="text-center">ชื่อโรงพยาบาล</th>
                    <th class="text-center">Visit</th>
                    <th class="text-center">ค่ารักษารวม</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($hospitalSummary as $h)
                    <tr>
                      <td align="right">{{ $h->hospcode }}</td>
                      <td>{{ $h->hospname }} 
                        <span class="text-secondary small">
                            {{ \Carbon\Carbon::parse($h->last_updated_at)->locale('th')->isoFormat('D MMM YYYY H:mm') }} น.
                        </span>
                      </td>
                      <td align="right">{{ number_format($h->visit_ucs_herb) }}</td>
                      <td align="right">{{ number_format($h->inc_herb,2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
      </div>  
    {{------------------------------------------------------------------------------------------------------------}}

    </div>
  </div>  
</section>
<br>
<hr>

{{-- เลือกปีงบประมาณ ----------------------------------------------------------------------------------------------------------}}
<section id="summary" class="pb-2">
    <div class="container-fluid">
      <form method="POST" action="{{ route('web.index') }}" enctype="multipart/form-data">
      @csrf
        <div class="row g-4 align-items-center">
          <div class="col-lg-9">          
            <h6 class="text-success mb-2"><strong></strong></h6>          
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
</section>

{{-- ข้อมูลบริการผู้ป่วยนอก ----------------------------------------------------------------------------------------------------------}}
<section id="hospital" class="pb-2">
  <div class="container-fluid">
  
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
            <h6>[10985] ข้อมูลบริการผู้ป่วยนอกโรงพยาชานุมาน ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10985}}</span>              
          </div>
          <div class="table-responsive">
            <table id="table10985" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10985 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                </tr>   
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10986 -->
      <div class="tab-pane fade" id="pane-10986" role="tabpanel" aria-labelledby="tab-10986" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6>[10986] ข้อมูลบริการผู้ป่วยนอกโรงพยาปทุมราชวงศา ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10986}}</span>            
          </div>
          <div class="table-responsive">
            <table id="table10986" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10986 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
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
            <h6>[10987] ข้อมูลบริการผู้ป่วยนอกโรงพยาพนา ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10987}}</span>   
          </div>
          <div class="table-responsive">
            <table id="table10987" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10987 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
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
            <h6>[10988] ข้อมูลบริการผู้ป่วยนอกโรงพยาบาลเสนางคนิคม ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10988}}</span>   
          </div>
          <div class="table-responsive">
            <table id="table10988" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10988 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
                </tr>   
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- 10989 -->
      <div class="tab-pane fade" id="pane-10989" role="tabpanel" aria-labelledby="tab-10989" tabindex="0">
        <div class="glass p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6>[10989] ข้อมูลบริการผู้ป่วยนอกโรงพยาบาลหัวตะพาน ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10989}}</span>   
          </div>
          <div class="table-responsive">
            <table id="table10989" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10989 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
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
            <h6>[10990] ข้อมูลบริการผู้ป่วยนอกโรงพยาบาลลืออำนาจ ปีงบประมาณ {{$budget_year}}</h6>
            <span class="text-secondary small">Update {{$update_at10990}}</span>   
          </div>
          <div class="table-responsive">
            <table id="table10990" class="table table-bordered table-striped my-3" width ="100%">
              <thead class="table-light">
                <tr class="table-primary">
                  <th class="text-center" rowspan="2" width ="4%">เดือน</th>
                  <th class="text-center" colspan="7">ทั้งหมด</th>
                  <th class="text-center" colspan="4">UCS ใน CUP</th> 
                  <th class="text-center" colspan="4">UCS ในจังหวัด</th>
                  <th class="text-center" colspan="4">UCS นอกจังหวัด</th>       
                  <th class="text-center" colspan="4">OFC ข้าราชการ</th>  
                  <th class="text-center" colspan="4">BKK กทม.</th>
                  <th class="text-center" colspan="4">BMT ขสมก.</th>
                  <th class="text-center" colspan="4">SSS ประกันสังคม</th>
                  <th class="text-center" colspan="4">LGO อปท.</th>
                  <th class="text-center" colspan="4">FSS ต่างด้าว</th>
                  <th class="text-center" colspan="4">STP Stateless</th>
                  <th class="text-center" colspan="4">ชำระเงิน/พรบ.</th>                 
                </tr>    
                <tr class="table-primary">            
                  <td class="text-center text-primary">HN Total</td>
                  <td class="text-center text-primary">Visit Total</td>
                  <td class="text-center text-primary">Visit OP</td>
                  <td class="text-center text-primary">Visit PP</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                  <td class="text-center text-primary">Visit</td>
                  <td class="text-center text-primary">ค่ารักษารวม</td>
                  <td class="text-center text-primary">ค่า Lab</td>
                  <td class="text-center text-primary">ค่า ยา</td>
                </tr>    
              </thead>
              <tbody>
                <?php $count = 1 ; ?> 
                <?php $sum_hn_total = 0 ; ?> 
                <?php $sum_visit_total = 0 ; ?>   
                <?php $sum_visit_total_op = 0 ; ?>  
                <?php $sum_visit_total_pp = 0 ; ?> 
                <?php $sum_inc_total = 0 ; ?>  
                <?php $sum_inc_lab_total = 0 ; ?>
                <?php $sum_inc_drug_total = 0 ; ?> 
                <?php $sum_visit_ucs_incup = 0 ; ?>  
                <?php $sum_inc_ucs_incup = 0 ; ?>  
                <?php $sum_inc_lab_ucs_incup = 0 ; ?>  
                <?php $sum_inc_drug_ucs_incup = 0 ; ?>  
                <?php $sum_visit_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_ucs_inprov = 0 ; ?>  
                <?php $sum_inc_lab_ucs_inprov = 0 ; ?> 
                <?php $sum_inc_drug_ucs_inprov = 0 ; ?>   
                <?php $sum_visit_ucs_outprov = 0 ; ?>  
                <?php $sum_inc_ucs_outprov = 0 ; ?>
                <?php $sum_inc_lab_ucs_outprov = 0 ; ?>
                <?php $sum_inc_drug_ucs_outprov = 0 ; ?>  
                <?php $sum_visit_ofc = 0 ; ?>  
                <?php $sum_inc_ofc = 0 ; ?>
                <?php $sum_inc_lab_ofc = 0 ; ?>
                <?php $sum_inc_drug_ofc = 0 ; ?>
                <?php $sum_visit_bkk = 0 ; ?>  
                <?php $sum_inc_bkk = 0 ; ?>
                <?php $sum_inc_lab_bkk = 0 ; ?>
                <?php $sum_inc_drug_bkk = 0 ; ?> 
                <?php $sum_visit_bmt = 0 ; ?>  
                <?php $sum_inc_bmt = 0 ; ?>
                <?php $sum_inc_lab_bmt = 0 ; ?>
                <?php $sum_inc_drug_bmt = 0 ; ?>  
                <?php $sum_visit_sss = 0 ; ?>  
                <?php $sum_inc_sss = 0 ; ?>
                <?php $sum_inc_lab_sss = 0 ; ?>
                <?php $sum_inc_drug_sss = 0 ; ?> 
                <?php $sum_visit_lgo = 0 ; ?>  
                <?php $sum_inc_lgo = 0 ; ?>
                <?php $sum_inc_lab_lgo = 0 ; ?>
                <?php $sum_inc_drug_lgo = 0 ; ?> 
                <?php $sum_visit_fss = 0 ; ?>  
                <?php $sum_inc_fss = 0 ; ?>
                <?php $sum_inc_lab_fss = 0 ; ?>
                <?php $sum_inc_drug_fss = 0 ; ?> 
                <?php $sum_visit_stp = 0 ; ?>  
                <?php $sum_inc_stp = 0 ; ?>
                <?php $sum_inc_lab_stp = 0 ; ?>
                <?php $sum_inc_drug_stp = 0 ; ?>
                <?php $sum_visit_pay = 0 ; ?>  
                <?php $sum_inc_pay = 0 ; ?>
                <?php $sum_inc_lab_pay = 0 ; ?>
                <?php $sum_inc_drug_pay = 0 ; ?>  
                @foreach($total_10990 as $row) 
                <tr>
                  <td align="center"width ="4%">{{ $row->month }}</td>
                  <td align="right">{{ number_format($row->hn_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total) }}</td>
                  <td align="right">{{ number_format($row->visit_total_op) }}</td>
                  <td align="right">{{ number_format($row->visit_total_pp) }}</td>
                  <td align="right">{{ number_format($row->inc_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_total,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_total,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_incup) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_incup,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_inprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_inprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ucs_outprov) }}</td>
                  <td align="right">{{ number_format($row->inc_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ucs_outprov,2) }}</td>
                  <td align="right">{{ number_format($row->visit_ofc) }}</td>
                  <td align="right">{{ number_format($row->inc_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_ofc,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bkk) }}</td>
                  <td align="right">{{ number_format($row->inc_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bkk,2) }}</td>
                  <td align="right">{{ number_format($row->visit_bmt) }}</td>
                  <td align="right">{{ number_format($row->inc_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_bmt,2) }}</td>
                  <td align="right">{{ number_format($row->visit_sss) }}</td>
                  <td align="right">{{ number_format($row->inc_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_sss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_sss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_lgo) }}</td>
                  <td align="right">{{ number_format($row->inc_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_lgo,2) }}</td>
                  <td align="right">{{ number_format($row->visit_fss) }}</td>
                  <td align="right">{{ number_format($row->inc_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_fss,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_fss,2) }}</td>
                  <td align="right">{{ number_format($row->visit_stp) }}</td>
                  <td align="right">{{ number_format($row->inc_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_stp,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_stp,2) }}</td>
                  <td align="right">{{ number_format($row->visit_pay) }}</td>
                  <td align="right">{{ number_format($row->inc_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_lab_pay,2) }}</td>
                  <td align="right">{{ number_format($row->inc_drug_pay,2) }}</td>
                </tr>
                <?php $count++; ?>
                <?php $sum_hn_total += $row->hn_total ; ?>
                <?php $sum_visit_total += $row->visit_total ; ?>
                <?php $sum_visit_total_op += $row->visit_total_op ; ?>
                <?php $sum_visit_total_pp += $row->visit_total_pp ; ?>
                <?php $sum_inc_total += $row->inc_total ; ?>
                <?php $sum_inc_lab_total += $row->inc_lab_total ; ?>
                <?php $sum_inc_drug_total += $row->inc_drug_total ; ?>
                <?php $sum_visit_ucs_incup += $row->visit_ucs_incup ; ?>
                <?php $sum_inc_ucs_incup += $row->inc_ucs_incup ; ?>
                <?php $sum_inc_lab_ucs_incup += $row->inc_lab_ucs_incup ; ?>
                <?php $sum_inc_drug_ucs_incup += $row->inc_drug_ucs_incup ; ?>   
                <?php $sum_visit_ucs_inprov += $row->visit_ucs_inprov ; ?>
                <?php $sum_inc_ucs_inprov += $row->inc_ucs_inprov ; ?>
                <?php $sum_inc_lab_ucs_inprov += $row->inc_lab_ucs_inprov ; ?>
                <?php $sum_inc_drug_ucs_inprov += $row->inc_drug_ucs_inprov ; ?>
                <?php $sum_visit_ucs_outprov += $row->visit_ucs_outprov ; ?>
                <?php $sum_inc_ucs_outprov += $row->inc_ucs_outprov ; ?>
                <?php $sum_inc_lab_ucs_outprov += $row->inc_lab_ucs_outprov ; ?>
                <?php $sum_inc_drug_ucs_outprov += $row->inc_drug_ucs_outprov ; ?> 
                <?php $sum_visit_ofc += $row->visit_ofc ; ?>
                <?php $sum_inc_ofc += $row->inc_ofc ; ?>
                <?php $sum_inc_lab_ofc += $row->inc_lab_ofc ; ?>
                <?php $sum_inc_drug_ofc += $row->inc_drug_ofc ; ?> 
                <?php $sum_visit_bkk += $row->visit_bkk ; ?>
                <?php $sum_inc_bkk += $row->inc_bkk ; ?>
                <?php $sum_inc_lab_bkk += $row->inc_lab_bkk ; ?>
                <?php $sum_inc_drug_bkk += $row->inc_drug_bkk ; ?>  
                <?php $sum_visit_bmt += $row->visit_bmt ; ?>
                <?php $sum_inc_bmt += $row->inc_bmt ; ?>
                <?php $sum_inc_lab_bmt += $row->inc_lab_bmt ; ?>
                <?php $sum_inc_drug_bmt += $row->inc_drug_bmt ; ?> 
                <?php $sum_visit_sss += $row->visit_sss ; ?>
                <?php $sum_inc_sss += $row->inc_sss ; ?>
                <?php $sum_inc_lab_sss += $row->inc_lab_sss ; ?>
                <?php $sum_inc_drug_sss += $row->inc_drug_sss ; ?>   
                <?php $sum_visit_lgo += $row->visit_lgo ; ?>
                <?php $sum_inc_lgo += $row->inc_lgo ; ?>
                <?php $sum_inc_lab_lgo += $row->inc_lab_lgo ; ?>
                <?php $sum_inc_drug_lgo += $row->inc_drug_lgo ; ?>
                <?php $sum_visit_fss += $row->visit_fss ; ?>
                <?php $sum_inc_fss += $row->inc_fss ; ?>
                <?php $sum_inc_lab_fss += $row->inc_lab_fss ; ?>
                <?php $sum_inc_drug_fss += $row->inc_drug_fss ; ?>    
                <?php $sum_visit_stp += $row->visit_stp ; ?>
                <?php $sum_inc_stp += $row->inc_stp ; ?>
                <?php $sum_inc_lab_stp += $row->inc_lab_stp ; ?>
                <?php $sum_inc_drug_stp += $row->inc_drug_stp ; ?>   
                <?php $sum_visit_pay += $row->visit_pay ; ?>
                <?php $sum_inc_pay += $row->inc_pay ; ?>
                <?php $sum_inc_lab_pay += $row->inc_lab_pay ; ?>
                <?php $sum_inc_drug_pay += $row->inc_drug_pay ; ?> 
                @endforeach    
                <tr>
                  <td align="right"><strong>รวม</strong></td>
                  <td align="right"><strong>{{number_format($sum_hn_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_total_op)}}</strong></td>     
                  <td align="right"><strong>{{number_format($sum_visit_total_pp)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_inc_total,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_inc_lab_total,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_inc_drug_total,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_incup)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_incup,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_incup,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_ucs_inprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_inprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_visit_ucs_outprov)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ucs_outprov,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ucs_outprov,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_ofc)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_ofc,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_ofc,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bkk)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bkk,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bkk,2)}}</strong></td>   
                  <td align="right"><strong>{{number_format($sum_visit_bmt)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_bmt,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_bmt,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_sss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_sss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_sss,2)}}</strong></td>  
                  <td align="right"><strong>{{number_format($sum_visit_lgo)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_lgo,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_lgo,2)}}</strong></td>       
                  <td align="right"><strong>{{number_format($sum_visit_fss)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_fss,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_fss,2)}}</strong></td>    
                  <td align="right"><strong>{{number_format($sum_visit_stp)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_stp,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_stp,2)}}</strong></td> 
                  <td align="right"><strong>{{number_format($sum_visit_pay)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_lab_pay,2)}}</strong></td>
                  <td align="right"><strong>{{number_format($sum_inc_drug_pay,2)}}</strong></td> 
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
  
  <!-- jQuery + DataTables core + Bootstrap 5 integration -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- Buttons + HTML5 export + JSZip (Excel ต้องมีตัวนี้) -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

  <script>
    $(function () {
      $('#table10985').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลชานุมาน {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10986').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลปทุมราชวงศา {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10987').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลพนา {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10988').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลเสนางคนิคม {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10989').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลหัวตะพาน {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>
  <script>
    $(function () {
      $('#table10990').DataTable({
        dom: '<"d-flex justify-content-end mb-2"B>rt',
        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> ส่งออก Excel',
            className: 'btn btn-success btn-sm',
            title: 'ข้อมูลบริการผู้ป่วยนอก OPD โรงพยาบาลลืออำนาจ {{ $budget_year ?? "" }}'
          }
        ],
        ordering: false,
        paging: false,
        info: false,
        lengthChange: false,
        language: { search: "ค้นหา:" }
      });
    });
  </script>

</body>
</html>
