<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IETI System - Marikina Campus')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-body text-body d-flex flex-column min-vh-100">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2 text-info"></i>IETI System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <!-- Theme Toggle Button -->
                        <button id="themeToggleBtn" class="btn btn-outline-light btn-sm" onclick="toggleTheme()">
                            <i class="fas fa-sun" id="themeIcon"></i>
                        </button>
                    </li>
                    @auth
                        <li class="nav-item me-3">
                            <span class="nav-link text-light small">
                                <i class="fas fa-user-shield me-1"></i> {{ Auth::user()->username }} ({{ ucfirst(Auth::user()->role) }})
                            </span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item me-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-info btn-sm text-white fw-bold">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto small">
        <p class="mb-0">&copy; 2026 IETI College of Science and Technology - Marikina Campus. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle & bfcache Buster Script -->
    <script>
        function toggleTheme() {
            const htmlEl = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = htmlEl.getAttribute('data-bs-theme');

            if (currentTheme === 'dark') {
                htmlEl.setAttribute('data-bs-theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('ieti_theme', 'light');
            } else {
                htmlEl.setAttribute('data-bs-theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('ieti_theme', 'dark');
            }
        }

        // Persist theme choice on load
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('ieti_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                if (savedTheme === 'light') {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                } else {
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                }
            }
        });

        // Defeat Browser Back-Forward Cache (bfcache) to prevent viewing pages post-logout
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>