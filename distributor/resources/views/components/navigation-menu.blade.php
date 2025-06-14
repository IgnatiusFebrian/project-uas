<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
            <li class="nav-item">
                    <a class="nav-link" href="{{ route('items.index') }}">Laporan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('incoming_goods.index') }}">Barang Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('outgoing_goods.index') }}">Barang Keluar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('returns.index') }}">Retur Barang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">Manage Accounts</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <div class="form-check form-switch text-white">
                        <input class="form-check-input" type="checkbox" id="darkModeToggle">
                        <label class="form-check-label" for="darkModeToggle">Dark Mode</label>
                    </div>
                </li>
                @auth
                    <li class="nav-item">
                        <span class="nav-link">Halo, {{ Auth::user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

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
