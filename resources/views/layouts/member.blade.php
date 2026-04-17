<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SD Church — My Portal</title>
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
</head>
<body>
   <nav class="navbar navbar-expand-lg member-navbar px-3">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="{{ route('member.dashboard') }}">
                <i class="bi bi-church me-2"></i>SD Church
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('member.dashboard') }}"><i class="bi bi-house me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('member.projects') }}"><i class="bi bi-folder me-1"></i>My Projects</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('member.contributions') }}"><i class="bi bi-clock-history me-1"></i>My History</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>