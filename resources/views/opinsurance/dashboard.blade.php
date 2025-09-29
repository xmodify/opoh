<!doctype html>
<html lang="th" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>OP Insurance • Modern Dashboard</title>

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
    <div class="container container-compact">
      <a class="navbar-brand d-flex align-items-center brand-title fw-bold" href="#">
        <i class="bi bi-shield-check me-2 text-green"></i> OP Insurance
      </a>
      <div class="d-flex align-items-center gap-3 order-lg-2">
        <span class="chip d-none d-md-inline"><i class="bi bi-activity me-1"></i> Realtime-lite</span>
        <div id="themeToggle" class="toggle" role="button" title="สลับ Light/Dark">
          <div class="dot"></div>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
      <div id="topnav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#summary">ภาพรวม</a></li>
          <li class="nav-item"><a class="nav-link" href="#insights">อินไซต์</a></li>
          <li class="nav-item"><a class="nav-link" href="#table">ตาราง</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <header class="py-5">
    <div class="container container-compact">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="chip"><i class="bi bi-stars me-1"></i> Modern UI</span>
            <span class="chip"><i class="bi bi-lightning-charge me-1"></i> Fast</span>
            <span class="chip"><i class="bi bi-shield-lock me-1"></i> Secure</span>
          </div>
          <h1 class="fw-bold mb-2">แดชบอร์ด OP Insurance ที่ <span class="text-green">ทันสมัย</span> และใช้งานง่าย</h1>
          <p class="text-secondary">สรุปข้อมูลจาก <code>op_insurance</code> แสดงผลแบบการ์ด กราฟ โพรเกรส และตารางค้นหาได้</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <div class="d-flex justify-content-lg-end gap-2">
            <button class="btn btn-ghost px-3" onclick="location.reload()">
              <i class="bi bi-arrow-repeat me-1"></i> รีเฟรช
            </button>
            <button class="btn btn-neo px-3" id="exportCsv">
              <i class="bi bi-download me-1"></i> ส่งออก CSV
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- SUMMARY -->
  <section id="summary" class="pb-2">
    <div class="container container-compact">
      @php
        $cards = $cards ?? [];
        $fmt = fn($n)=>number_format((int)($n ?? 0));
      @endphp

      <div class="row g-3">
        @php
          $items = [
            ['label'=>'Total Visit','key'=>'total_visit','icon'=>'bi-people','accent'=>'text-green'],
            ['label'=>'Endpoint','key'=>'endpoint','icon'=>'bi-diagram-3','accent'=>'text-primary'],
            ['label'=>'Non Hmain','key'=>'non_hmain','icon'=>'bi-hospital','accent'=>'text-green'],
            ['label'=>'UC Anywhere','key'=>'uc_anywhere','icon'=>'bi-geo-alt','accent'=>'text-green'],
            ['label'=>'UC CR','key'=>'uc_cr','icon'=>'bi-capsule','accent'=>'text-primary'],
            ['label'=>'UC Herb','key'=>'uc_herb','icon'=>'bi-flower3','accent'=>'text-green'],
            ['label'=>'UC HealthMed','key'=>'uc_healthmed','icon'=>'bi-heart-pulse','accent'=>'text-green'],
            ['label'=>'PPFS','key'=>'ppfs','icon'=>'bi-clipboard2-check','accent'=>'text-primary'],
          ];
        @endphp

        @foreach($items as $it)
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="glass p-3 h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-primary">{{ $it['label'] }}</h6>
                <span class="{{ $it['accent'] }}"><i class="bi {{ $it['icon'] }} fs-5"></i></span>
              </div>
              <div class="d-flex align-items-end justify-content-between">
                <h2 class="fw-bold mb-0 {{ str_contains($it['accent'],'primary') ? 'text-primary':'text-green' }}">
                  {{ $fmt($cards[$it['key']] ?? 0) }}
                </h2>
                @php
                  // สุ่ม % เปลี่ยนเพื่อเดโม (จริงใช้จาก DB)
                  $pct = rand(-5,12);
                @endphp
                <small class="text-secondary">{{ $pct>=0?'+':'' }}{{ $pct }}% vs. prev</small>
              </div>
              <div class="progress fine mt-3" role="progressbar" aria-valuenow="{{ max(0,min(100,($cards[$it['key']] ?? 0)%100)) }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ max(6, min(100, ($cards[$it['key']] ?? 0)%100)) }}%"></div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="text-end mt-2">
        <small class="text-secondary">อัปเดตล่าสุด: <span id="last-updated">-</span></small>
      </div>
    </div>
  </section>

  <!-- INSIGHTS -->
  <section id="insights" class="py-4">
    <div class="container container-compact">
      <div class="row g-3">
        <div class="col-12 col-lg-7">
          <div class="glass p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0 text-primary"><i class="bi bi-graph-up-arrow me-2 text-green"></i>แนวโน้มภาพรวม</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-ghost" id="btn7">7 วัน</button>
                <button class="btn btn-sm btn-ghost" id="btn30">30 วัน</button>
                <button class="btn btn-sm btn-ghost" id="reloadChart"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
            </div>
            <canvas id="lineChart" height="128"></canvas>
          </div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="glass p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0 text-primary"><i class="bi bi-pie-chart me-2 text-green"></i>สัดส่วนหมวด UC</h5>
            </div>
            <canvas id="donutChart" height="128"></canvas>
            <div class="row text-center pt-3">
              <div class="col"><small class="text-secondary">Anywhere</small><div class="fw-bold">{{ $fmt($cards['uc_anywhere'] ?? 0) }}</div></div>
              <div class="col"><small class="text-secondary">CR</small><div class="fw-bold">{{ $fmt($cards['uc_cr'] ?? 0) }}</div></div>
              <div class="col"><small class="text-secondary">Herb</small><div class="fw-bold">{{ $fmt($cards['uc_herb'] ?? 0) }}</div></div>
              <div class="col"><small class="text-secondary">HealthMed</small><div class="fw-bold">{{ $fmt($cards['uc_healthmed'] ?? 0) }}</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- TABLE -->
  <section id="table" class="py-4">
    <div class="container container-compact">
      <div class="glass p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
          <h5 class="mb-2 mb-md-0 text-primary"><i class="bi bi-table me-2 text-green"></i>สรุปแบบตาราง</h5>
          <div class="d-flex gap-2 flex-wrap">
            <input type="text" class="form-control" placeholder="ค้นหา..." id="searchInput" style="max-width:260px" />
            <select class="form-select" id="filterSelect" style="max-width:220px">
              <option value="">กรองตามประเภท</option>
              <option>Total Visit</option><option>Endpoint</option><option>Non Hmain</option>
              <option>UC Anywhere</option><option>UC CR</option><option>UC Herb</option><option>UC HealthMed</option><option>PPFS</option>
            </select>
            <button class="btn btn-ghost" onclick="filterTable()"><i class="bi bi-search"></i></button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle table-hover table-green-border" id="dataTable">
            <thead class="table-light">
              <tr class="border-success-soft">
                <th>ประเภท</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">สัดส่วน (%)</th>
                <th class="text-end">สถานะ</th>
              </tr>
            </thead>
            <tbody>
              @php
                $pairs = [
                  ['ประเภท'=>'Total Visit','จำนวน'=>$cards['total_visit'] ?? 0],
                  ['ประเภท'=>'Endpoint','จำนวน'=>$cards['endpoint'] ?? 0],
                  ['ประเภท'=>'Non Hmain','จำนวน'=>$cards['non_hmain'] ?? 0],
                  ['ประเภท'=>'UC Anywhere','จำนวน'=>$cards['uc_anywhere'] ?? 0],
                  ['ประเภท'=>'UC CR','จำนวน'=>$cards['uc_cr'] ?? 0],
                  ['ประเภท'=>'UC Herb','จำนวน'=>$cards['uc_herb'] ?? 0],
                  ['ประเภท'=>'UC HealthMed','จำนวน'=>$cards['uc_healthmed'] ?? 0],
                  ['ประเภท'=>'PPFS','จำนวน'=>$cards['ppfs'] ?? 0],
                ];
                $totalAll = collect($pairs)->sum('จำนวน') ?: 1;
              @endphp
              @foreach($pairs as $row)
              @php
                $pct = round(($row['จำนวน']/$totalAll)*100,1);
                $status = $pct>=20 ? 'success' : ($pct>=5 ? 'pending' : 'failed');
              @endphp
              <tr>
                <td>{{ $row['ประเภท'] }}</td>
                <td class="text-end">{{ number_format((int)$row['จำนวน']) }}</td>
                <td class="text-end">{{ number_format($pct,1) }}</td>
                <td class="text-end">
                  <span class="badge status-badge {{ $status }}">{{ strtoupper($status) }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="border-success-soft">
                <th class="text-end">รวม</th>
                <th class="text-end">{{ number_format((int)$totalAll) }}</th>
                <th class="text-end">100.0</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="py-4">
    <div class="container container-compact text-center text-secondary small">
      © {{ now()->year }} OP Insurance • Modern Bootstrap UI
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    // เวลาอัปเดต
    document.getElementById('last-updated').textContent = new Date().toLocaleString('th-TH');

    // Light/Dark toggle + จำสถานะ
    const htmlEl = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const saved = localStorage.getItem('theme');
    if(saved){ htmlEl.setAttribute('data-bs-theme', saved); if(saved==='dark') toggle.classList.add('active'); }
    toggle.addEventListener('click', ()=>{
      const cur = htmlEl.getAttribute('data-bs-theme')==='dark' ? 'light' : 'dark';
      htmlEl.setAttribute('data-bs-theme', cur);
      localStorage.setItem('theme', cur);
      toggle.classList.toggle('active', cur==='dark');
    });

    // Data base
    const cardsData = {
      visit: {{ (int)($cards['total_visit'] ?? 0) }},
      endpoint: {{ (int)($cards['endpoint'] ?? 0) }},
      nonh: {{ (int)($cards['non_hmain'] ?? 0) }},
      any: {{ (int)($cards['uc_anywhere'] ?? 0) }},
      cr: {{ (int)($cards['uc_cr'] ?? 0) }},
      herb: {{ (int)($cards['uc_herb'] ?? 0) }},
      hmed: {{ (int)($cards['uc_healthmed'] ?? 0) }},
      ppfs: {{ (int)($cards['ppfs'] ?? 0) }},
    };

    // Line chart
    const ctxL = document.getElementById('lineChart');
    let days = 7;
    function genSeries(n, base){
      const arr=[]; for(let i=0;i<n;i++){ arr.push(Math.max(0, Math.round(base + (Math.random()*base*0.2 - base*0.1)))); } return arr;
    }
    function labels(n){
      const L=[]; const now=new Date();
      for(let i=n-1;i>=0;i--){ const d=new Date(now); d.setDate(now.getDate()-i); L.push(d.toLocaleDateString('th-TH',{day:'2-digit', month:'2-digit'})); }
      return L;
    }
    let lineChart = new Chart(ctxL, {
      type:'line',
      data:{
        labels: labels(days),
        datasets:[{
          label:'Total Visit',
          data: genSeries(days, Math.max(10, cardsData.visit/30)),
          borderColor:'#18a573',
          backgroundColor:'rgba(33,192,139,.18)',
          pointBackgroundColor:'#0d6efd',
          pointBorderColor:'#0d6efd',
          tension:.35, pointRadius:3, fill:true
        }]
      },
      options:{
        responsive:true,
        plugins:{ legend:{ display:false }},
        scales:{ y:{ beginAtZero:true, grid:{ color:'rgba(33,192,139,.12)' }}, x:{ grid:{ display:false }}}
      }
    });

    document.getElementById('btn7').addEventListener('click', ()=>{ days=7; lineChart.data.labels=labels(days); lineChart.data.datasets[0].data=genSeries(days, Math.max(10, cardsData.visit/30)); lineChart.update(); });
    document.getElementById('btn30').addEventListener('click', ()=>{ days=30; lineChart.data.labels=labels(days); lineChart.data.datasets[0].data=genSeries(days, Math.max(10, cardsData.visit/30)); lineChart.update(); });
    document.getElementById('reloadChart').addEventListener('click', ()=>{ lineChart.data.datasets[0].data = lineChart.data.datasets[0].data.map(v => Math.max(0, Math.round(v + (Math.random()*60-30)))); lineChart.update(); });

    // Donut chart
    const ctxD = document.getElementById('donutChart');
    const donut = new Chart(ctxD, {
      type:'doughnut',
      data:{
        labels:['Anywhere','CR','Herb','HealthMed'],
        datasets:[{
          data:[cardsData.any, cardsData.cr, cardsData.herb, cardsData.hmed],
          backgroundColor:['rgba(24,165,115,.85)','rgba(33,192,139,.85)','rgba(13,110,253,.65)','rgba(24,165,115,.35)'],
          borderColor:['#fff','#fff','#fff','#fff'], borderWidth:2, hoverOffset:4
        }]
      },
      options:{ plugins:{ legend:{ position:'bottom' }}, cutout:'58%' }
    });

    // Filter table
    function filterTable(){
      const q = document.getElementById('searchInput').value.toLowerCase();
      const f = (document.getElementById('filterSelect').value || '').toLowerCase();
      document.querySelectorAll('#dataTable tbody tr').forEach(tr=>{
        const text = tr.innerText.toLowerCase();
        const okQ = q ? text.includes(q) : true;
        const okF = f ? tr.children[0].innerText.toLowerCase().includes(f) : true;
        tr.style.display = (okQ && okF) ? '' : 'none';
      });
    }

    // CSV export
    document.getElementById('exportCsv').addEventListener('click', ()=>{
      const rows = [['ประเภท','จำนวน'],
        ['Total Visit', cardsData.visit], ['Endpoint', cardsData.endpoint], ['Non Hmain', cardsData.nonh],
        ['UC Anywhere', cardsData.any], ['UC CR', cardsData.cr], ['UC Herb', cardsData.herb],
        ['UC HealthMed', cardsData.hmed], ['PPFS', cardsData.ppfs],
      ];
      const csv = rows.map(r=>r.join(',')).join('\n');
      const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a'); a.href=url; a.download='op_insurance_summary.csv'; a.click();
      URL.revokeObjectURL(url);
    });
  </script>
</body>
</html>
