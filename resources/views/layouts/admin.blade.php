<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Mosnoky') }} - @yield('title', 'Administration')</title>
        @fonts
        <style>
            :root { color-scheme: light; --ink: #221f1d; --muted: #857b72; --paper: #f3efe8; --card: #ffffff; --accent: #221f1d; --brand-red: #b1372f; --danger: #b1372f; --line: #e6ddd0; }
            * { box-sizing: border-box; }
            body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Instrument Sans", ui-sans-serif, sans-serif; }
            a { color: inherit; text-decoration: none; }
            .shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
            .topbar { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 20px 0; }
            .brand { display: flex; align-items: center; gap: 10px; font-size: 1.2rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
            .brand img { height: 36px; width: 36px; object-fit: contain; border-radius: 8px; }
            .brand-tag { display: inline-flex; align-items: center; margin-left: 4px; padding: 3px 9px; border-radius: 999px; background: var(--brand-red); color: #fff; font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
            .nav-links { display: flex; align-items: center; gap: 18px; font-size: .92rem; font-weight: 600; flex-wrap: wrap; }
            .nav-links a:hover { color: var(--brand-red); }
            .nav-links a.active { color: var(--brand-red); font-weight: 800; }
            .badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 5px; margin-left: 4px; border-radius: 999px; background: var(--brand-red); color: #fff; font-size: .7rem; font-weight: 800; }
            .button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--line); border-radius: 999px; padding: 10px 18px; font-size: .88rem; font-weight: 700; transition: .2s ease; cursor: pointer; background: transparent; }
            .button:hover { border-color: var(--ink); transform: translateY(-1px); }
            .button.primary { border-color: var(--ink); background: var(--ink); color: white; }
            .button.danger { border-color: var(--danger); color: var(--danger); }
            .button.danger:hover { background: var(--danger); color: #fff; }
            .button.small { padding: 6px 13px; font-size: .8rem; }
            .user-dropdown { position: relative; display: inline-block; }
            .dropdown-menu { display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 200px; background: var(--card); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); z-index: 50; overflow: hidden; padding: 6px 0; }
            .user-dropdown:focus-within .dropdown-menu, .user-dropdown:hover .dropdown-menu { display: block; }
            .dropdown-item { display: block; width: 100%; padding: 10px 16px; text-align: left; font-size: .9rem; color: var(--ink); border: none; background: none; cursor: pointer; }
            .dropdown-item:hover { background: var(--paper); color: var(--brand-red); }
            .dropdown-divider { height: 1px; background: var(--line); margin: 4px 0; }
            main { padding: 20px 0 90px; }
            .page-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 24px; flex-wrap: wrap; }
            h1.page-title { margin: 0; font-family: Georgia, serif; font-size: 2.1rem; font-weight: 400; }
            .page-subtitle { margin: 4px 0 0; color: var(--muted); font-size: .92rem; }
            .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: .9rem; font-weight: 600; }
            .alert-success { background: #ece9e2; color: var(--ink); border: 1px solid #d8d2c3; }
            .alert-danger { background: #fbe9e6; color: var(--danger); border: 1px solid #f0c3ba; }
            .panel { border: 1px solid var(--line); border-radius: 10px; background: var(--card); overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 13px 16px; text-align: left; font-size: .9rem; border-bottom: 1px solid var(--line); }
            th { color: var(--muted); font-size: .75rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; background: #fbfaf7; }
            tr:last-child td { border-bottom: none; }
            .row-actions { display: flex; gap: 8px; justify-content: flex-end; }
            .pill { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: .75rem; font-weight: 700; background: #f6e6e2; color: var(--brand-red); }
            .pill.muted { background: #ece7dd; color: var(--muted); }
            .empty { border: 1px dashed #d9cfc0; border-radius: 10px; padding: 40px 20px; text-align: center; color: var(--muted); }
            .form-panel { max-width: 640px; border: 1px solid var(--line); border-radius: 10px; background: var(--card); padding: 28px; }
            .field { margin-bottom: 18px; }
            .field label { display: block; margin-bottom: 6px; font-size: .85rem; font-weight: 700; }
            .field input[type=text], .field input[type=number], .field input[type=date], .field select, .field textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: .92rem; font-family: inherit; background: #fff; }
            .field textarea { min-height: 100px; resize: vertical; }
            .field .hint { margin-top: 5px; font-size: .78rem; color: var(--muted); }
            .field .error { margin-top: 5px; font-size: .8rem; color: var(--danger); font-weight: 600; }
            .checkbox-field { display: flex; align-items: center; gap: 8px; }
            .form-actions { display: flex; gap: 10px; margin-top: 24px; }
            footer { border-top: 1px solid var(--line); padding: 24px 0 40px; color: var(--muted); font-size: .85rem; }
            @media (max-width: 760px) { .shell { width: min(100% - 28px, 600px); } .nav-links { display: none; } table { display: block; overflow-x: auto; } }

            .mobile-toggle { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--ink); padding: 6px; line-height: 1; }
            .mobile-nav-overlay { display: none; position: fixed; inset: 0; background: var(--paper); z-index: 200; padding: 24px; overflow-y: auto; }
            .mobile-nav-overlay.open { display: block; }
            .mobile-nav-overlay .mobile-nav-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
            .mobile-nav-overlay a { display: block; padding: 14px 4px; font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid var(--line); }
            .mobile-nav-overlay a.active { color: var(--brand-red); }
            @media (max-width: 760px) {
                .mobile-toggle { display: block; }
                #desktop-user-dropdown { display: none; }
            }
        </style>
    </head>
    <body>
        @php
            $adminUnreadNotifCount = auth()->check() ? \App\Models\AppNotification::where('user_id', auth()->id())->where('is_read', false)->count() : 0;
        @endphp
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="brand-logo">
                    <span class="brand-tag">Admin</span>
                </a>

                <nav class="nav-links" aria-label="Navigation administrateur">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Tableau de bord</a>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Produits</a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Catégories</a>
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Commandes</a>
                    <a href="{{ route('admin.clients.index') }}" class="{{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">Clients</a>
                    <a href="{{ route('admin.deliveries.index') }}" class="{{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">Livraisons</a>
                    <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">Avis</a>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Signalements</a>
                    <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">Messages</a>
                    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        Notifications
                        @if ($adminUnreadNotifCount > 0)
                            <span class="badge">{{ $adminUnreadNotifCount }}</span>
                        @endif
                    </a>
                </nav>

                <div class="user-dropdown" id="desktop-user-dropdown">
                    <button class="button">
                        <span>{{ auth()->user()->first_name ?? auth()->user()->name }}</span>
                    </button>
                    <div class="dropdown-menu">
                        @if (Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">Mon profil</a>
                        @endif
                        <div class="dropdown-divider"></div>
                        @if (Route::has('logout'))
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color: #b1372f;">Déconnexion</button>
                            </form>
                        @endif
                    </div>
                </div>

                <button type="button" class="mobile-toggle" onclick="document.getElementById('mobile-nav').classList.add('open')" aria-label="Ouvrir le menu">☰</button>
            </header>

            <!-- Menu plein écran mobile -->
            <div class="mobile-nav-overlay" id="mobile-nav">
                <div class="mobile-nav-head">
                    <a class="brand" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" style="height:32px; width:auto;">
                    </a>
                    <button type="button" class="mobile-toggle" style="display:block;" onclick="document.getElementById('mobile-nav').classList.remove('open')" aria-label="Fermer le menu">✕</button>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Tableau de bord</a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Produits</a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Catégories</a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Commandes</a>
                <a href="{{ route('admin.clients.index') }}" class="{{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">Clients</a>
                <a href="{{ route('admin.deliveries.index') }}" class="{{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">Livraisons</a>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">Avis</a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Signalements</a>
                <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">Messages</a>
                <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    Notifications
                    @if ($adminUnreadNotifCount > 0)
                        <span class="badge">{{ $adminUnreadNotifCount }}</span>
                    @endif
                </a>
                @if (Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}">Mon profil</a>
                @endif
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" style="margin-top:20px;">
                        @csrf
                        <button type="submit" class="button danger" style="width:100%;">Déconnexion</button>
                    </form>
                @endif
            </div>

            <main>
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">Veuillez corriger les erreurs ci-dessous.</div>
                @endif

                @yield('content')
            </main>

            <footer style="border-top:1px solid var(--line); padding:20px 0 30px; color:var(--muted); font-size:.82rem; display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <span>© {{ date('Y') }} {{ config('app.name', 'Mosnoky') }} · Espace d'administration.</span>
                <span>
                    <a href="tel:+22674919133" style="color:inherit; margin-right:14px;">📞 74 91 91 33</a>
                    <a href="https://wa.me/22674919133" target="_blank" rel="noopener" style="color:inherit; display:inline-flex; align-items:center; gap:6px;"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.3c1.5.8 3.1 1.3 4.8 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3C4.2 15 3.8 13.5 3.8 12c0-4.5 3.7-8.2 8.2-8.2s8.2 3.7 8.2 8.2-3.7 8.2-8.2 8.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.2.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.2-.9.9-.9 2.2s.9 2.5 1.1 2.7c.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.4-.3z"/></svg> WhatsApp</a>
                </span>
            </footer>
        </div>
    </body>
</html>
