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
            --sidebar-bg: #0b0f19;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #1f2937;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-light: #f8fafc;
            --border: #e2e8f0;
            --header-height: 70px;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            grid-template-rows: var(--header-height) 1fr;
            overflow: hidden;
            transition: var(--transition);
        }

        body.collapsed {
            grid-template-columns: var(--sidebar-collapsed-width) 1fr;
        }

        /* Sidebar */
        aside {
            grid-row: 1 / 3;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: var(--transition);
            overflow-x: hidden;
        }

        .sidebar-header {
            height: var(--header-height);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            white-space: nowrap;
        }

        .brand-logo {
            width: 35px;
            height: 35px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            margin-left: 0.75rem;
            transition: opacity 0.2s;
        }

        body.collapsed .brand-name {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin: 1.5rem 0 0.75rem 0.5rem;
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        body.collapsed .nav-label {
            opacity: 0;
        }

        .nav-list { list-style: none; }
        .nav-item { margin-bottom: 0.4rem; }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 12px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-item.active a { background: var(--primary); color: white; box-shadow: 0 10px 24px rgba(37, 99, 235, 0.24); }
        .nav-item:not(.active) a:hover { background: var(--sidebar-active); color: white; }
        .nav-item a svg { width: 20px; height: 20px; stroke-width: 2.5; flex-shrink: 0; }

        .nav-text {
            transition: opacity 0.2s;
        }

        body.collapsed .nav-text {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            white-space: nowrap;
        }

        body.collapsed .footer-text {
            display: none;
        }

        /* Header */
        header {
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 900;
        }

        .header-left { display: flex; align-items: center; gap: 1rem; }

        .sidebar-toggle {
            cursor: pointer;
            padding: 0.5rem;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        button,
        a {
            transition: all 0.2s;
        }

        button[style*="background: var(--primary)"],
        a[style*="background: var(--primary)"] {
            background: var(--primary) !important;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.18);
        }

        button[style*="background: var(--primary)"]:hover,
        a[style*="background: var(--primary)"]:hover {
            background: var(--primary-hover) !important;
        }

        .mobile-toggle {
            display: none;
            cursor: pointer;
            padding: 0.5rem;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
        }

        .header-right { display: flex; align-items: center; gap: 1.5rem; }

        .view-site-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 1.5rem;
            border-left: 1px solid var(--border);
        }

        .user-avatar { width: 38px; height: 38px; border-radius: 50%; }

        /* Content Area */
        main {
            overflow-y: auto;
            padding: 2.5rem;
            background-color: var(--bg-light);
        }

        /* Mobile Adjustments */
        @media (max-width: 991px) {
            body {
                grid-template-columns: 1fr;
            }
            body.collapsed {
                grid-template-columns: 1fr;
            }
            aside {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: var(--sidebar-width) !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            aside.open { transform: translateX(0); }
            header { grid-column: 1 / 2; }
            main { grid-column: 1 / 2; }
            .mobile-toggle { display: block; }
            .sidebar-toggle { display: none; }
            .close-sidebar { display: block; }
            
            body.collapsed .brand-name,
            body.collapsed .nav-label,
            body.collapsed .nav-text {
                opacity: 1 !important;
                pointer-events: auto !important;
            }
        }

        .close-sidebar {
            display: none;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        .sidebar-overlay.show { display: block; }

        .hidden { display: none; }
        @media (min-width: 640px) { .sm\:block { display: block; } }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside id="adminSidebar">
        <div class="sidebar-header">
            <div style="display: flex; align-items: center;">
                
                <span class="brand-name">Bandos Komar</span>
            </div>
            <button id="closeSidebar" class="close-sidebar">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">General</div>
            <ul class="nav-list">
                <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i> 
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i data-lucide="users"></i> 
                        <span class="nav-text">Users</span>
                    </a>
                </li>
            </ul>

            <div class="nav-label">Management</div>
            <ul class="nav-list">
                <li class="nav-item {{ Request::routeIs('admin.pages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pages.index') }}">
                        <i data-lucide="files"></i>
                        <span class="nav-text">Page Management</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::routeIs('admin.donations.*') || Request::is('admin/donations*') ? 'active' : '' }}">
                    <a href="{{ route('admin.donations.index') }}">
                        <i data-lucide="heart-handshake"></i>
                        <span class="nav-text">Donations</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('logout') }}" style="color: var(--sidebar-text); text-decoration: none; display: flex; align-items: center; gap: 0.75rem; font-weight: 600;">
                <i data-lucide="log-out"></i> 
                <span class="nav-text footer-text">Logout</span>
            </a>
        </div>
    </aside>

    <header>
        <div class="header-left">
            <button class="sidebar-toggle" id="toggleSidebar">
                <i data-lucide="menu"></i>
            </button>
            <button class="mobile-toggle" id="openSidebar">
                <i data-lucide="menu"></i>
            </button>
            <h2 style="font-size: 1rem; font-weight: 700; color: #64748b;">Admin Control Panel</h2>
        </div>

        <div class="header-right">
            <a href="{{ route('home') }}" class="view-site-btn">
                <i data-lucide="globe"></i>
                <span class="hidden md:inline">Visit Website</span>
            </a>
            
            <div class="user-profile">
                <div style="text-align: right; margin-right: 0.75rem;" class="hidden sm:block">
                    <div style="font-weight: 800; font-size: 0.85rem;">Administrator</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">SUPER ADMIN</div>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=f68b1e&color=fff&bold=true" class="user-avatar" alt="Admin">
            </div>
        </div>
    </header>

    <main>
        @yield('admin_content')
    </main>

    <script>
        lucide.createIcons();

        const body = document.body;
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('toggleSidebar');
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');

        // Check for saved sidebar state
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            body.classList.add('collapsed');
        }

        const toggleSidebarCollapse = () => {
            body.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', body.classList.contains('collapsed'));
        };

        const toggleMobileSidebar = (state) => {
            if (state) {
                sidebar.classList.add('open');
                overlay.classList.add('show');
            } else {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        };

        toggleBtn.addEventListener('click', toggleSidebarCollapse);
        openBtn.addEventListener('click', () => toggleMobileSidebar(true));
        if(closeBtn) closeBtn.addEventListener('click', () => toggleMobileSidebar(false));
        overlay.addEventListener('click', () => toggleMobileSidebar(false));
    </script>
    @yield('scripts')
</body>
</html>
