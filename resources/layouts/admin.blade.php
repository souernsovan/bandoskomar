<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - Bandos Komar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #1e293b;
            --sidebar-active-text: #ffffff;
            --primary: #6366f1;
            --primary-light: #818cf8;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #f1f5f9;
            --header-height: 72px;
            --sidebar-width: 260px;
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            left: calc(-1 * var(--sidebar-width));
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        aside.active {
            transform: translateX(var(--sidebar-width));
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
        }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .close-sidebar {
            background: rgba(255,255,255,0.05);
            border: none;
            color: white;
            padding: 5px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
        }

        .nav-section {
            padding: 0 1rem;
            margin-bottom: 2rem;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            padding-left: 1rem;
            margin-bottom: 0.75rem;
            letter-spacing: 1px;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }

        .nav-item.active a {
            background-color: var(--primary);
            color: #ffffff;
        }

        .nav-item a:hover:not(.active) {
            background-color: var(--sidebar-active);
            color: #ffffff;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        /* Main Content */
        main {
            margin-left: 0;
            width: 100%;
            transition: all 0.3s ease;
        }

        header {
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .menu-toggle {
            background: #ffffff;
            border: 1px solid var(--border);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-main);
            display: flex;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .search-bar {
            display: none;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            align-items: center;
            width: 320px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .search-bar:focus-within {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            margin-left: 0.75rem;
            width: 100%;
            font-size: 0.9rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 4px;
            padding-right: 12px;
            border-radius: 100px;
            background: #f8fafc;
            border: 1px solid var(--border);
            cursor: pointer;
        }

        .profile-info img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        .profile-details {
            display: none;
        }

        /* Desktop Adjustments */
        @media (min-width: 1024px) {
            aside {
                left: 0;
            }
            aside.active {
                transform: none;
            }
            .close-sidebar {
                display: none;
            }
            main {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
            header {
                padding: 0 2rem;
            }
            .menu-toggle {
                display: none;
            }
            .search-bar {
                display: flex;
            }
            .profile-details {
                display: flex;
                flex-direction: column;
            }
            .header-actions {
                gap: 1.5rem;
            }
            .profile-info img {
                width: 36px;
                height: 36px;
            }
        }

        .content-body {
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .content-body { padding: 2rem; }
        }

        /* Premium UI Components */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar-overlay" id="overlay"></div>

    <aside id="sidebar">
        <div class="sidebar-header">
            <div class="brand-info">
                <div class="brand-logo">B</div>
                <span class="brand-name">Bandos Komar</span>
            </div>
            <button class="close-sidebar" id="closeSidebar">
                <i data-lucide="x" style="width: 18px;"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-label">Main Menu</div>
                <ul class="nav-list">
                    <li class="nav-item {{ Request::is('admin') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i data-lucide="layout-grid" style="width: 20px;"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i data-lucide="users" style="width: 20px;"></i> Users
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <div class="nav-label">Management</div>
                <ul class="nav-list">
                    <li class="nav-item {{ Request::is('admin/posts*') ? 'active' : '' }}">
                        <a href="{{ route('admin.posts.index') }}">
                            <i data-lucide="file-text" style="width: 20px;"></i> Posts
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}">
                            <i data-lucide="folder-tree" style="width: 20px;"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('admin/donations*') ? 'active' : '' }}">
                        <a href="{{ route('admin.donations.index') }}">
                            <i data-lucide="heart-handshake" style="width: 20px;"></i> Donations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#">
                            <i data-lucide="layers" style="width: 20px;"></i> Pages
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="/logout" style="color: var(--sidebar-text); text-decoration: none; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; font-weight: 500;">
                <i data-lucide="log-out" style="width: 20px;"></i> Logout
            </a>
        </div>
    </aside>

    <main>
        <header>
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <button class="menu-toggle" id="menuToggle">
                    <i data-lucide="menu" style="width: 20px;"></i>
                </button>
                <div class="search-bar">
                    <i data-lucide="search" style="width: 18px; color: var(--text-muted);"></i>
                    <input type="text" placeholder="Search for data, posts...">
                </div>
            </div>
            
            <div class="header-actions">
                <div style="position: relative; cursor: pointer;">
                    <i data-lucide="bell" style="width: 22px; color: var(--text-muted);"></i>
                    <span style="position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid #ffffff;"></span>
                </div>
                
                <div class="profile-info">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff&bold=true" alt="Avatar">
                    <div class="profile-details">
                        <span style="font-weight: 700; font-size: 0.85rem; line-height: 1.2;">Admin User</span>
                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">SUPER ADMIN</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-body">
            @yield('admin_content')
        </div>
    </main>

    <script>
        lucide.createIcons();

        const menuToggle = document.getElementById('menuToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });

        const hideSidebar = () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        };

        closeSidebar.addEventListener('click', hideSidebar);
        overlay.addEventListener('click', hideSidebar);
    </script>
    @yield('scripts')
</body>
</html>
