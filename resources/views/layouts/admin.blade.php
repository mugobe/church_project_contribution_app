<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SD Church — Admin</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-building me-2"></i>SD Church Admin
        </a>
        <div class="collapse navbar-collapse">
<ul class="navbar-nav me-auto">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.members.index') }}">
            <i class="bi bi-people me-1"></i>Members
        </a>
    </li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.projects.index') }}">
        <i class="bi bi-folder me-1"></i>Projects
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.contributions.index') }}">
        <i class="bi bi-cash-stack me-1"></i>Contributions
    </a>
</li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-bar-chart me-1"></i>Reports
        </a>
    </li>
</ul>
        </div>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>