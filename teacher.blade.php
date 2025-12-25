<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="@yield('description', 'Teacher Dashboard - Takniki Shiksha Careers')">
    <meta name="keywords" content="@yield('keywords', 'yoga teacher, dashboard, classes, earnings')">

    <title>@yield('title', 'Teacher Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('photos/tsvc.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('photos/tsvc.png') }}">
    <link rel="shortcut icon" href="{{ asset('photos/tsvc.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom Teacher Dashboard CSS -->
    <link href="{{ asset('css/teacher-dashboard.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light">

<div class="d-flex">

    {{-- =========================
        SIDEBAR
    ========================= --}}
    <aside class="bg-white border-end"
           style="width:260px; min-height:100vh;">

        {{-- Sidebar Partial --}}
        @include('teacher.partials.sidebar')

    </aside>

    {{-- =========================
        MAIN CONTENT AREA
    ========================= --}}
    <main class="flex-fill">

        {{-- Optional Top Navbar (if exists) --}}
        @includeWhen(view()->exists('partials.teacher-nav'), 'partials.teacher-nav')

        {{-- Page Content --}}
        <div class="p-4">
            @yield('content')
        </div>

    </main>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="{{ asset('js/teacher-dashboard.js') }}"></script>

@stack('scripts')

</body>
</html>
