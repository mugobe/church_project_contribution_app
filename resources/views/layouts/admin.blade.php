<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SD Church — Admin</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>
<div class="admin-wrapper">

    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-building-fill"></i>
            <div>
                SD Church
                <span>Admin Panel</span>
            </div>
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Main</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.members.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.members*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Members
            </a>
            <a href="{{ route('admin.projects.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.projects*') ? 'active' : '' }}">
                <i class="bi bi-folder"></i> Projects
            </a>
            <a href="{{ route('admin.contributions.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.contributions*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Contributions
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Reports</div>
            <a href="#" class="sidebar-link">
                <i class="bi bi-bar-chart"></i> Analytics
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-file-earmark-text"></i> Statements
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="text-muted small mb-2">Logged in as</div>
            <div class="text-white fw-semibold small">{{ auth()->user()->name }}</div>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button class="btn btn-sm btn-outline-secondary w-100" style="font-size:12px;">
                    <i class="bi bi-box-arrow-left me-1"></i>Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="admin-main">

        {{-- Top bar --}}
        <div class="admin-topbar">
            <span class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}
            </span>
            <a href="{{ route('public.projects') }}" target="_blank"
                class="btn btn-sm btn-outline-primary">
                <i class="bi bi-globe me-1"></i>Public Wall
            </a>
        </div>

        {{-- Alerts --}}
        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>