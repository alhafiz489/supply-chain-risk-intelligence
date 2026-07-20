<aside id="adminSidebar" class="sidebar" aria-label="Navigasi administrator">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <span class="brand-mark">S</span>
        <div>
            <p class="brand-title">SupplyGuard</p>
            <p class="brand-subtitle">Admin Control Center</p>
        </div>
    </a>

    <div class="menu-label">
        {{ app()->getLocale() === 'id' ? 'Menu Utama' : 'Main Menu' }}
    </div>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Dashboard Admin' : 'Admin Dashboard' }}</span>
    </a>

    <a href="{{ route('dashboard') }}"
       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       @if (request()->routeIs('dashboard')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 11 9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Dashboard Utama' : 'Main Dashboard' }}</span>
    </a>

    <div class="menu-label">
        {{ app()->getLocale() === 'id' ? 'Pengelolaan' : 'Management' }}
    </div>

    <a href="{{ route('admin.users.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.users.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Kelola Pengguna' : 'Manage Users' }}</span>
    </a>

    <a href="{{ route('admin.ports.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.ports.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.ports.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v17m-4-9h8M9 6h6M5 14a7 7 0 0 0 14 0M3 14h4m10 0h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Kelola Pelabuhan' : 'Manage Ports' }}</span>
    </a>

    <a href="{{ route('admin.news.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.news.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5V4Zm3 4h8M8 12h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Kelola Berita' : 'Manage News' }}</span>
    </a>

    <a href="{{ route('admin.sentiment-words.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.sentiment-words.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.sentiment-words.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5.5A3.5 3.5 0 0 1 7.5 2H20v17H7.5A3.5 3.5 0 0 0 4 22V5.5Zm0 0V19a3 3 0 0 1 3-3h13M8 6h8m-8 4h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Kamus Sentimen' : 'Sentiment Dictionary' }}</span>
    </a>

    <a href="{{ route('admin.risks.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.risks.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.risks.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 3.8 7v5c0 5 3.5 8 8.2 9 4.7-1 8.2-4 8.2-9V7L12 3Zm0 5v5m0 4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Riwayat Risiko' : 'Risk History' }}</span>
    </a>

    <a href="{{ route('admin.api-logs.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.api-logs.*') ? 'active' : '' }}"
       @if (request()->routeIs('admin.api-logs.*')) aria-current="page" @endif>
        <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m8 9-4 3 4 3m8-6 4 3-4 3m-2-9-4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>{{ app()->getLocale() === 'id' ? 'Log API' : 'API Logs' }}</span>
    </a>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="logout-button">
            <span class="sidebar-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m10-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            {{ app()->getLocale() === 'id' ? 'Keluar dari Sistem' : 'Sign Out' }}
        </button>
    </form>
</aside>