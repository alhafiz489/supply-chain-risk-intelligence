<button id="userSidebarToggle" class="sg-user-sidebar-toggle" type="button" aria-label="Open navigation" onclick="toggleUserSidebar()">
    <span></span><span></span><span></span>
</button>
<div id="userSidebarBackdrop" class="sg-user-sidebar-backdrop" onclick="toggleUserSidebar(false)"></div>

<aside id="userSidebar" class="sg-user-sidebar" aria-label="User navigation">
    <a href="{{ route('dashboard') }}" class="sg-user-sidebar-brand">
        <span class="sg-brand-mark">S</span>
        <span><strong>SupplyGuard</strong><small>Risk Intelligence Platform</small></span>
    </a>

    <div class="sg-user-sidebar-section-label">{{ app()->getLocale() === 'id' ? 'Menu Utama' : 'Main Menu' }}</div>
    <nav class="sg-user-side-menu">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span>⌂</span><strong>Dashboard</strong>
        </a>
        <a href="{{ route('country.comparison') }}" class="{{ request()->routeIs('country.comparison') ? 'active' : '' }}">
            <span>⇄</span><strong>{{ app()->getLocale() === 'id' ? 'Perbandingan Negara' : 'Country Comparison' }}</strong>
        </a>
        <a href="{{ route('watchlist.index') }}" class="{{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
            <span>★</span><strong>{{ app()->getLocale() === 'id' ? 'Daftar Favorit' : 'Favorite List' }}</strong>
        </a>
        <a href="{{ route('system.overview') }}" class="{{ request()->routeIs('system.overview') ? 'active' : '' }}">
            <span>◎</span><strong>{{ app()->getLocale() === 'id' ? 'Cakupan Sistem' : 'System Overview' }}</strong>
        </a>
    </nav>

    <div class="sg-user-sidebar-section-label">{{ app()->getLocale() === 'id' ? 'Intelijen Risiko' : 'Risk Intelligence' }}</div>
    <nav class="sg-user-side-menu sg-user-side-menu-secondary">
        <a href="{{ route('dashboard') }}#portSection"><span>⌖</span><strong>{{ app()->getLocale() === 'id' ? 'Peta Global' : 'Global Map' }}</strong></a>
        <a href="{{ route('dashboard') }}#riskAnalysisSection"><span>◈</span><strong>{{ app()->getLocale() === 'id' ? 'Analisis & Visualisasi' : 'Analysis & Visualization' }}</strong></a>
        <a href="{{ route('data.countries') }}" class="{{ request()->routeIs('data.countries') ? 'active' : '' }}"><span>◎</span><strong>{{ app()->getLocale() === 'id' ? 'Data Negara' : 'Countries' }}</strong></a>
        <a href="{{ route('data.ports') }}" class="{{ request()->routeIs('data.ports') ? 'active' : '' }}"><span>⚓</span><strong>{{ app()->getLocale() === 'id' ? 'Data Pelabuhan' : 'Ports' }}</strong></a>
        <a href="{{ route('data.sentiments') }}" class="{{ request()->routeIs('data.sentiments') ? 'active' : '' }}"><span>◐</span><strong>{{ app()->getLocale() === 'id' ? 'Data Sentimen' : 'Sentiment' }}</strong></a>
        <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}"><span>▤</span><strong>News</strong></a>
    </nav>

    @auth
        @if (auth()->user()->role === 'admin')
            <div class="sg-user-sidebar-section-label">Administrator</div>
            <nav class="sg-user-side-menu">
                <a href="{{ route('admin.dashboard') }}"><span>▦</span><strong>{{ app()->getLocale() === 'id' ? 'Panel Admin' : 'Admin Panel' }}</strong></a>
            </nav>
        @endif
    @endauth

    <div class="sg-user-sidebar-spacer"></div>
    @auth
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="sg-user-sidebar-logout">⇥ {{ app()->getLocale() === 'id' ? 'Keluar' : 'Logout' }}</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="sg-user-sidebar-login">{{ __('messages.login') }}</a>
    @endauth
</aside>

<header class="sg-user-utility-topbar">
    <div class="sg-user-topbar-language">
        @include('user.partials.language-selector')
    </div>
    @auth
        <div class="sg-user-topbar-profile">
            <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div>
        </div>
    @else
        <a href="{{ route('login') }}" class="sg-user-topbar-login">{{ __('messages.login') }}</a>
    @endauth
</header>

<script>
    function toggleUserSidebar(force) {
        const sidebar = document.getElementById('userSidebar');
        const backdrop = document.getElementById('userSidebarBackdrop');
        const open = typeof force === 'boolean' ? force : !sidebar.classList.contains('show');
        sidebar.classList.toggle('show', open);
        backdrop.classList.toggle('show', open);
        document.body.classList.toggle('user-sidebar-open', open);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sg-user-side-menu a[href*="#"]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    toggleUserSidebar(false);
                }
            });
        });

        if (window.location.hash) {
            window.setTimeout(function () {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 350);
        }
    });
</script>
