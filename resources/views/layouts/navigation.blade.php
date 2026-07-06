<div class="mobile-topbar">
    <button id="sidebar-toggle" class="hamburger-btn" aria-label="Open menu">
        <span></span><span></span><span></span>
    </button>
    <span class="barrier-mark" aria-hidden="true"></span>
    <span class="brand-text">
        <strong>VAMS</strong>
    </span>
</div>

<div id="sidebar-overlay" class="sidebar-overlay"></div>

<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="barrier-mark" aria-hidden="true"></span>
        <span class="brand-text">
            <strong>VAMS</strong>
            <small>FUT Minna Gate Control</small>
        </span>
    </div>

    <nav class="sidebar-nav">
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Dashboard
                </a>
                <a href="{{ route('admin.alerts.index') }}" class="nav-link {{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}" style="justify-content: space-between;">
                    <span><span class="nav-dot"></span> Alerts</span>
                    <span id="alert-badge" style="display:none; background: var(--red); color: #fff; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 999px;"></span>
                </a>
                <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Vehicles
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Registered Users
                </a>
                <a href="{{ route('admin.gates.index') }}" class="nav-link {{ request()->routeIs('admin.gates.*') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Gate Points
                </a>
                <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Access Logs
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Reports
                </a>
            @else
                <a href="{{ route('officer.scan') }}" class="nav-link {{ request()->routeIs('officer.scan') ? 'active' : '' }}">
                    <span class="nav-dot"></span> Scan Gate
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <span class="nav-dot"></span> Profile
            </a>
        @endauth
    </nav>

    <div class="sidebar-footer">
        @auth
            <div class="user-chip">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="user-meta">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ ucfirst(auth()->user()->role) }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-link">Log out</button>
            </form>
        @endauth
    </div>

    <script>
        function pollAlertCount() {
            fetch("{{ route('admin.alerts.count') }}")
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('alert-badge');
                    if (!badge) return;
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {});
        }

        @auth
            @if(auth()->user()->role === 'admin')
                pollAlertCount();
                setInterval(pollAlertCount, 15000);
            @endif
        @endauth

        (function () {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            function openSidebar() {
                sidebar.classList.add('sidebar-open');
                overlay.classList.add('sidebar-overlay-visible');
            }

            function closeSidebar() {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('sidebar-overlay-visible');
            }

            toggleBtn?.addEventListener('click', openSidebar);
            overlay?.addEventListener('click', closeSidebar);

            sidebar?.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', closeSidebar);
            });
        })();
    </script>
</aside>