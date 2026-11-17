<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Hanken Grotesk', system-ui, sans-serif; }

        /* ── Sidebar ── */
        #sidebar {
            width: 268px;
            flex: none;
            background: #ffffff;
            border-right: 1px solid rgba(28,26,22,0.10);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            /* Desktop collapse: slide left by pushing margin */
            transition: margin-left 0.32s cubic-bezier(0.4,0,0.2,1);
        }
        #mainContent {
            transition: none; /* layout reflows naturally */
        }

        /* Mobile: sidebar becomes fixed off-canvas drawer */
        @media (max-width: 767px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100%;
                z-index: 40;
                transform: translateX(-100%);
                transition: transform 0.32s cubic-bezier(0.4,0,0.2,1);
                margin-left: 0 !important; /* ignore JS margin on mobile */
            }
            #sidebar.drawer-open {
                transform: translateX(0);
            }
            #mainContent {
                margin-left: 0 !important;
            }
        }

        /* Active nav pill */
        .nav-link-active {
            background: #1d3c68 !important;
            color: #fbfafa !important;
            font-weight: 600;
            box-shadow: 0 6px 16px -8px rgba(17,40,68,0.55);
        }
        .nav-link-active svg { color: #fbfafa !important; }

        /* Brand mark red dot */
        .brand-mark { position: relative; }
        .brand-mark::after {
            content: "";
            position: absolute;
            right: 6px; bottom: 6px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #bc3f2c;
        }

        /* Backdrop */
        #sidebar-backdrop {
            position: fixed; inset: 0;
            background: rgba(17,40,68,0.32);
            backdrop-filter: blur(2px);
            opacity: 0; visibility: hidden;
            transition: opacity 0.28s ease, visibility 0.28s ease;
            z-index: 30;
        }
        #sidebar-backdrop.open { opacity: 1; visibility: visible; }

        /* Thin scrollbar in sidebar nav */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #dfe6ef; border-radius: 99px; }

        /* Nav item base */
        .nav-item {
            display: flex; align-items: center; gap: 13px;
            padding: 11px 12px;
            border-radius: 10px;
            font-size: 15px; font-weight: 500;
            color: #5b554b;
            transition: background 0.16s, color 0.16s;
            margin-bottom: 2px;
            text-decoration: none;
        }
        .nav-item svg { width: 19px; height: 19px; flex: none; color: #8a8478; transition: color 0.16s; }
        .nav-item:hover { background: #f3f2f1; color: #1c1a16; }
        .nav-item:hover svg { color: #5b554b; }
        .nav-item.logout { color: #bc3f2c; }
        .nav-item.logout svg { color: #bc3f2c; }
        .nav-item.logout:hover { background: #f0ddd6; }
    </style>
</head>
<body style="background:#f3f2f1; color:#1c1a16; margin:0; -webkit-font-smoothing:antialiased; line-height:1.5;">

@if(request()->ajax())
    {{ $slot }}
@else

<div style="display:flex; min-height:100vh;" id="app">

    {{-- Backdrop (mobile) --}}
    <div id="sidebar-backdrop" onclick="closeDrawer()"></div>

    {{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
    <aside id="sidebar">

        {{-- Brand --}}
        <div style="display:flex; align-items:center; gap:12px; padding:22px 24px; border-bottom:1px solid rgba(28,26,22,0.10); font-weight:600; font-size:20px; letter-spacing:-0.01em;">
            <span class="brand-mark" style="width:38px; height:38px; border-radius:10px; background:#1d3c68; display:grid; place-items:center; flex:none;">
                <svg style="width:19px;height:19px;color:#fbfafa;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
            </span>
            Inventory
        </div>

        {{-- User --}}
        <div style="display:flex; flex-direction:column; align-items:center; text-align:center; gap:8px; padding:26px 24px; border-bottom:1px solid rgba(28,26,22,0.10);">
            @if(auth()->user()->profile_photo)
                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                     style="width:56px; height:56px; border-radius:14px; object-fit:cover; margin-bottom:4px;" />
            @else
                <div style="width:56px; height:56px; border-radius:14px; background:#dfe6ef; display:grid; place-items:center; color:#1d3c68; margin-bottom:4px;">
                    <svg style="width:26px;height:26px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            @endif
            <div style="font-size:13px; color:#8a8478;">Welcome,</div>
            <div style="font-size:17px; font-weight:600; letter-spacing:-0.01em; line-height:1;">{{ auth()->user()->name }}</div>
            <span style="font-size:12px; font-weight:600; letter-spacing:0.04em; color:#1d3c68; background:#dfe6ef; padding:3px 12px; border-radius:100px;">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav" style="flex:1; overflow-y:auto; padding:18px 16px;">

            <div style="font-size:11px; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:#8a8478; padding:0 12px; margin:0 0 8px;">General</div>

            <a href="{{ route('admin.dashboard') }}" class="nav-link nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>

            <a href="{{ route('admin.products.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.products.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                Products
            </a>

            <a href="{{ route('admin.categories.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.categories.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Categories
            </a>

            <a href="{{ route('admin.brands.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.brands.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Brands
            </a>

            <a href="{{ route('admin.landing.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.landing.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Landing Page
            </a>

            <a href="{{ route('admin.orders.index') }}" class="nav-link nav-item {{ request()->routeIs('admin.orders.*') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Orders
            </a>

            <div style="font-size:11px; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:#8a8478; padding:0 12px; margin:14px 0 8px;">Account</div>

            <a href="{{ route('profile.edit') }}" class="nav-link nav-item {{ request()->routeIs('profile.edit') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile
            </a>

            <a href="{{ route('profile.password') }}" class="nav-link nav-item {{ request()->routeIs('profile.password') ? 'nav-link-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Change Password
            </a>
        </nav>

        {{-- Logout --}}
        <div style="padding:14px 16px; border-top:1px solid rgba(28,26,22,0.10);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item logout" style="width:100%; background:none; border:none; cursor:pointer; font-family:inherit;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════ MAIN ═══════════════════════════ --}}
    <div id="mainContent" style="flex:1; min-width:0; display:flex; flex-direction:column;">

        {{-- Topbar --}}
        <header style="height:68px; background:#ffffff; border-bottom:1px solid rgba(28,26,22,0.10); display:flex; align-items:center; justify-content:space-between; padding:0 32px; position:sticky; top:0; z-index:20;">
            <div style="display:flex; align-items:center; gap:16px;">
                <button onclick="toggleSidebar()"
                        style="width:38px; height:38px; border-radius:10px; border:1px solid rgba(28,26,22,0.10); background:#ffffff; display:grid; place-items:center; cursor:pointer; color:#5b554b; transition:background 0.16s, border-color 0.16s;"
                        onmouseover="this.style.background='#f3f2f1'" onmouseout="this.style.background='#ffffff'"
                        aria-label="Toggle menu">
                    <svg style="width:18px;height:18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1 style="font-size:19px; font-weight:600; letter-spacing:-0.015em; white-space:nowrap;">Admin Panel</h1>
            </div>
            <span style="font-size:14px; color:#8a8478; font-weight:500;">{{ now()->format('D, d M Y') }}</span>
        </header>

        {{-- ✅ Script BEFORE slot so all functions exist when slot scripts run --}}
        <script>
            const CSRF = '{{ csrf_token() }}';
            let sidebarOpen = true;

            function toggleSidebar() {
                const isMobile = window.matchMedia('(max-width: 767px)').matches;
                if (isMobile) {
                    const sidebar = document.getElementById('sidebar');
                    const isOpen  = sidebar.classList.contains('drawer-open');
                    isOpen ? closeDrawer() : openDrawer();
                } else {
                    sidebarOpen = !sidebarOpen;
                    const sidebar = document.getElementById('sidebar');
                    sidebar.style.marginLeft = sidebarOpen ? '0' : '-268px';
                }
            }

            function openDrawer() {
                document.getElementById('sidebar').classList.add('drawer-open');
                document.getElementById('sidebar-backdrop').classList.add('open');
            }

            function closeDrawer() {
                document.getElementById('sidebar').classList.remove('drawer-open');
                document.getElementById('sidebar-backdrop').classList.remove('open');
            }

            // Reset on breakpoint cross
            window.matchMedia('(max-width: 767px)').addEventListener('change', function () {
                closeDrawer();
                document.getElementById('sidebar').style.marginLeft = '';
                sidebarOpen = true;
            });

            function loadPage(url) {
                document.querySelector('main').style.opacity = '0.5';
                document.querySelector('main').style.pointerEvents = 'none';

                return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    document.querySelector('main').innerHTML = html;
                    document.querySelector('main').querySelectorAll('script').forEach(old => {
                        const s = document.createElement('script');
                        s.textContent = old.textContent;
                        old.replaceWith(s);
                    });
                    document.querySelector('main').style.opacity = '1';
                    document.querySelector('main').style.pointerEvents = 'auto';
                    history.pushState({}, '', url);
                    updateActiveLinks(url);
                    if (typeof window.initValidation === 'function') window.initValidation();
                });
            }

            function updateActiveLinks(url) {
                document.querySelectorAll('.nav-link').forEach(link => {
                    const isActive = link.href === url;
                    link.classList.toggle('nav-link-active', isActive);
                });
            }

            function initNavLinks() {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (window.matchMedia('(max-width: 767px)').matches) closeDrawer();
                        loadPage(this.href);
                    });
                });

                document.addEventListener('click', function (e) {
                    const link = e.target.closest('a');
                    if (!link) return;
                    if (link.classList.contains('nav-link')) return;
                    if (!link.href) return;
                    if (link.target === '_blank') return;
                    if (link.hostname !== window.location.hostname) return;
                    if (e.target.closest('form')) return;
                    const skip = ['/logout', '/login', '/register'];
                    if (skip.some(path => link.pathname.startsWith(path))) return;
                    e.preventDefault();
                    loadPage(link.href);
                });
            }

            window.addEventListener('popstate', () => loadPage(window.location.href));
            initNavLinks();
        </script>

        {{-- Page Content --}}
        <main class="p-6" style="flex:1;">
            {{ $slot }}
        </main>

    </div>
</div>

<script src="{{ asset('js/validate.js') }}"></script>

@endif
</body>
</html>