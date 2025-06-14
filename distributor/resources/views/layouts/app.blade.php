<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Aplikasi Gudang') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-navigation-menu />

    <!-- Main Content -->
    <main class="py-2">
    <div class="container">
        @yield('content')
    </div>
</main>

<script>
    const toggle = document.getElementById('darkModeToggle');
    const body = document.body;

    // Load saved mode from localStorage
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('bg-dark', 'text-white');
        toggle.checked = true;
    }

    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            body.classList.add('bg-dark', 'text-white');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            body.classList.remove('bg-dark', 'text-white');
            localStorage.setItem('darkMode', 'disabled');
        }
    });
</script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
