<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StudentMS') — Student Management System</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a2332;
            --sidebar-accent: #3b82f6;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --topbar-height: 60px;
        }

        body { background: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1040; transition: transform .3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            color: #fff; font-size: 1.15rem; font-weight: 700;
            letter-spacing: .02em;
        }
        .sidebar-brand span { color: var(--sidebar-accent); }

        .sidebar-section {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #475569;
            padding: .9rem 1.5rem .3rem;
        }

        .sidebar-nav .nav-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .55rem 1.5rem; color: var(--sidebar-text);
            font-size: .875rem; border-radius: 0;
            transition: background .15s, color .15s;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(59,130,246,.12);
            color: var(--sidebar-text-active);
        }
        .sidebar-nav .nav-link.active {
            border-left: 3px solid var(--sidebar-accent);
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.2rem; text-align: center; }

        /*  Top Bar */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-width);
            right: 0; height: var(--topbar-height);
            background: #fff; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 1.5rem; gap: 1rem; z-index: 1030;
        }
        #topbar .page-title { font-weight: 600; font-size: 1rem; color: #1e293b; }

        /*  Main Content */
        #main-content {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--topbar-height) + 1.5rem);
            min-height: 100vh;
        }
        .content-wrapper { padding: 0 1.5rem 2rem; }

        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: .75rem; }
        .stat-card { border-radius: .75rem; color: #fff; }
        .stat-card .stat-icon { font-size: 2.5rem; opacity: .25; }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label  { font-size: .8rem; opacity: .85; }

        .badge { font-size: .72rem; }

        .capacity-bar { height: 6px; border-radius: 3px; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar, #main-content { left: 0; margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-mortarboard-fill me-2" style="color:var(--sidebar-accent)"></i>
        <span>MS</span>
    </div>

    <ul class="sidebar-nav nav flex-column mt-2">

        {{-- Dashboard --}}
        <div class="sidebar-section">Main</div>
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
            </a>
        </li>

        {{-- Courses (visible to all) --}}
        <div class="sidebar-section">Academic</div>
        <li class="nav-item">
            <a href="{{ route('courses.index') }}"
               class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
            </a>
        </li>

        {{-- Enrolments --}}
        <li class="nav-item">
            <a href="{{ route('enrolments.index') }}"
               class="nav-link {{ request()->routeIs('enrolments.*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i> Enrolments
                @php $pending = auth()->user()->isStudent()
                    ? auth()->user()->enrolments()->where('status','pending')->count()
                    : \App\Models\Enrolment::pending()
                        ->when(auth()->user()->isInstructor(), fn($q) =>
                            $q->whereHas('course', fn($c) => $c->where('instructor_id', auth()->id()))
                        )->count();
                @endphp
                @if($pending > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pending }}</span>
                @endif
            </a>
        </li>

        {{-- admin/instructor only --}}
        @unless(auth()->user()->isStudent())
        <div class="sidebar-section">Reports</div>
        <li class="nav-item">
            <a href="{{ route('reports.overview') }}"
               class="nav-link {{ request()->routeIs('reports.overview') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Overview
            </a>
        </li>
        @endunless

        {{-- Admin tools --}}
        @if(auth()->user()->isAdmin())
        <div class="sidebar-section">Administration</div>
        <li class="nav-item">
            <a href="{{ route('courses.create') }}"
               class="nav-link {{ request()->routeIs('courses.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i>
            </a>
        </li>
        @endif
    </ul>

    {{-- Sidebar footer --}}
    <div class="mt-auto p-3 border-top border-secondary">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                 style="width:34px;height:34px;font-size:.85rem;font-weight:600">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div style="line-height:1.2">
                <div style="font-size:.8rem;color:#fff;font-weight:500">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:var(--sidebar-text)">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <a href="{{ route('logout') }}" class="ms-auto text-secondary"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</nav>

{{-- TOP BAR --}}
<header id="topbar">
    <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
        <i class="bi bi-list fs-5"></i>
    </button>
    <span class="page-title">@yield('page-title', 'Dashboard')</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        {{-- Role badge --}}
        <span class="badge
            {{ auth()->user()->isAdmin() ? 'bg-danger' : (auth()->user()->isInstructor() ? 'bg-success' : 'bg-primary') }}">
            {{ ucfirst(auth()->user()->role) }}
        </span>
    </div>
</header>

<main id="main-content">
    <div class="content-wrapper">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar mobile toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
@stack('scripts')
</body>
</html>
