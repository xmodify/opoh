<!doctype html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Amnatcharoen One Province One Data : AOPOD')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables + Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

    <style>
        :root{
            --green:#18a573; --green-2:#21c08b; --blue:#0d6efd; --bg-1:#e9fbf2;
            --glass-bg:rgba(255,255,255,.7); --glass-bd:rgba(33,192,139,.35);
            --shadow:0 10px 30px rgba(24,165,115,.15); --radius:22px;
        }
        body {
            min-height:100vh;
            background:
                radial-gradient(1200px 800px at 10% -10%, rgba(33,192,139,.18), transparent 60%),
                radial-gradient(1000px 600px at 110% 10%, rgba(13,110,253,.14), transparent 60%),
                linear-gradient(135deg, #f6fffb 0%, var(--bg-1) 40%, #ffffff 100%);
            animation: floatBg 24s ease-in-out infinite alternate;
            background-attachment: fixed;
        }
        @keyframes floatBg{
            0%{background-position:0 0,0 0,0 0}
            100%{background-position:5% -3%,95% 2%,0 0}
        }
        .brand-title, h1, h2, h3, h4, .nav-link, .table thead th{ color: var(--blue); }
        .glass{ background: var(--glass-bg); border:1px solid var(--glass-bd); backdrop-filter: blur(10px); border-radius: var(--radius); box-shadow: var(--shadow); transition: .25s ease; }
        .glass:hover{ transform: translateY(-2px); box-shadow: 0 16px 40px rgba(24,165,115,.22); border-color: rgba(33,192,139,.55); }
        .chip{ border-radius:999px; border:1px solid rgba(13,110,253,.2); background:rgba(13,110,253,.06); padding:.35rem .75rem; font-weight:600; color: var(--blue); }
        .btn-neo{ border-radius:14px; border:1px solid rgba(0,0,0,.06); background: linear-gradient(180deg, #ffffff, #f0fff8); box-shadow:0 8px 20px rgba(24,165,115,.18); color: var(--green); }
        .btn-neo:hover{ filter: brightness(.98); transform: translateY(-1px); }
        .btn-ghost{ border:1px solid rgba(24,165,115,.35); color: var(--green); background: rgba(33,192,139,.08); }
        .border-success-soft{ border:1px solid rgba(33,192,139,.45) !important; }
        .text-green{ color: var(--green) !important; }
        .progress.fine{ height:8px; border-radius:999px; background: rgba(33,192,139,.12); }
        .progress.fine .progress-bar{ background: linear-gradient(90deg,var(--green),var(--green-2)); border-radius:999px; }
        .table-green-border{ --bs-border-color: rgba(33,192,139,.3); border-color: var(--bs-border-color) !important; }
        .status-badge.success{background:#d1e7dd;color:#0f5132}
        .status-badge.pending{background:#fff3cd;color:#664d03}
        .status-badge.failed{background:#f8d7da;color:#842029}
        @media (min-width:1200px){ .container.container-compact{ max-width:1120px; } }
    </style>
</head>
<body>
    <!-- NAV -->
    <nav class="navbar navbar-expand-lg bg-white bg-opacity-75 border-bottom sticky-top glass" style="border-radius:0">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center brand-title fw-bold" href="{{ url('web/') }}">
                <i class="bi bi-shield-check me-2 text-green"></i> Home
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav" 
                    aria-controls="topnav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topnav">
                <ul class="navbar-nav ms-auto">
                    @if(Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-primary" href="#" id="userDropdown" role="button" 
                              data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-primary">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><strong>Login</strong></a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="container-fluid">
        @yield('content')
    </main>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
</body>
</html>
