<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Mosnoky') }} - @yield('title', 'Espace Client')</title>
        @fonts
        <style>
            :root { color-scheme: light; --ink: #221f1d; --muted: #857b72; --paper: #faf6f1; --card: #ffffff; --accent: #b1372f; --danger: #7a231d; --line: #ece4d8; }
            * { box-sizing: border-box; }
            body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Instrument Sans", ui-sans-serif, sans-serif; }
            a { color: inherit; text-decoration: none; }
            .shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
            .topbar { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 20px 0; }
            .brand { display: flex; align-items: center; gap: 10px; font-size: 1.2rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
            .brand-logo { height: 38px; width: auto; }
            .nav-links { display: flex; align-items: center; gap: 18px; font-size: .92rem; font-weight: 600; flex-wrap: wrap; }
            .nav-links a:hover { color: var(--accent); }
            .nav-links a.active { color: var(--accent); font-weight: 800; }
            .badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 5px; margin-left: 4px; border-radius: 999px; background: var(--accent); color: #fff; font-size: .7rem; font-weight: 800; }
            .button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--line); border-radius: 999px; padding: 10px 18px; font-size: .88rem; font-weight: 700; transition: .2s ease; cursor: pointer; background: transparent; }
            .button:hover { border-color: var(--ink); transform: translateY(-1px); }
            .button.primary { border-color: var(--accent); background: var(--accent); color: white; }
            .button.danger { border-color: var(--danger); color: var(--danger); }
            .button.small { padding: 6px 13px; font-size: .8rem; }
            .user-dropdown { position: relative; display: inline-block; }
            .dropdown-menu { display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 220px; background: var(--card); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); z-index: 50; overflow: hidden; padding: 6px 0; }
            .user-dropdown:focus-within .dropdown-menu, .user-dropdown:hover .dropdown-menu { display: block; }
            .dropdown-item { display: block; width: 100%; padding: 10px 16px; text-align: left; font-size: .9rem; color: var(--ink); border: none; background: none; cursor: pointer; }
            .dropdown-item:hover { background: var(--paper); color: var(--accent); }
            .dropdown-divider { height: 1px; background: var(--line); margin: 4px 0; }
            main { padding: 20px 0 90px; }
            .page-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 24px; flex-wrap: wrap; }
            h1.page-title { margin: 0; font-family: Georgia, serif; font-size: 2.1rem; font-weight: 400; }
            .page-subtitle { margin: 4px 0 0; color: var(--muted); font-size: .92rem; }
            .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: .9rem; font-weight: 600; }
            .alert-success { background: #fbeae7; color: var(--accent); border: 1px solid #f0cfc7; }
            .alert-danger { background: #fbe9e6; color: var(--danger); border: 1px solid #f0c3ba; }
            .panel { border: 1px solid var(--line); border-radius: 10px; background: var(--card); overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 13px 16px; text-align: left; font-size: .9rem; border-bottom: 1px solid var(--line); }
            th { color: var(--muted); font-size: .75rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; background: #fbfaf6; }
            tr:last-child td { border-bottom: none; }
            .row-actions { display: flex; gap: 8px; justify-content: flex-end; }
            .pill { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: .75rem; font-weight: 700; background: #f6e6e2; color: var(--accent); }
            .pill.muted { background: #f0ede4; color: var(--muted); }
            .empty { border: 1px dashed #d9cfc0; border-radius: 10px; padding: 40px 20px; text-align: center; color: var(--muted); }
            .form-panel { border: 1px solid var(--line); border-radius: 10px; background: var(--card); padding: 28px; }
            .field { margin-bottom: 18px; }
            .field label { display: block; margin-bottom: 6px; font-size: .85rem; font-weight: 700; }
            .field input[type=text], .field input[type=number], .field select { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: .92rem; font-family: inherit; background: #fff; }
            .field .error { margin-top: 5px; font-size: .8rem; color: var(--danger); font-weight: 600; }
            .form-actions { display: flex; gap: 10px; margin-top: 24px; }
            .grid-cats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
            .cat-card { display: flex; flex-direction: column; gap: 6px; padding: 22px; border: 1px solid var(--line); border-radius: 10px; background: var(--card); transition: .2s ease; }
            .cat-card:hover { border-color: var(--accent); transform: translateY(-2px); }
            .products { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
            .product { display: flex; min-height: 100%; flex-direction: column; overflow: hidden; border: 1px solid var(--line); border-radius: 10px; background: var(--card); }
            .product-image { display: grid; aspect-ratio: 4 / 3; place-items: center; overflow: hidden; background: #eee7dc; color: #a0947f; }
            .product-image img { width: 100%; height: 100%; object-fit: cover; }
            .product-content { display: flex; flex: 1; flex-direction: column; gap: 10px; padding: 18px; }
            h3 { margin: 0; font-size: 1.1rem; }
            .description { margin: 0; color: var(--muted); font-size: .88rem; line-height: 1.5; }
            .product-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 10px; }
            .price { font-size: 1.05rem; font-weight: 800; }
            .qty-input { width: 64px; padding: 8px; border: 1px solid var(--line); border-radius: 8px; text-align: center; }
            footer { border-top: 1px solid var(--line); padding: 24px 0 40px; color: var(--muted); font-size: .85rem; }
            @media (max-width: 900px) { .grid-cats, .products { grid-template-columns: repeat(2, 1fr); } }
            @media (max-width: 760px) { .shell { width: min(100% - 28px, 600px); } .nav-links { display: none; } .grid-cats, .products { grid-template-columns: 1fr; } table { display:block; overflow-x:auto; } }

            .mobile-toggle { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--ink); padding: 6px; line-height: 1; }
            .mobile-nav-overlay { display: none; position: fixed; inset: 0; background: var(--paper); z-index: 200; padding: 24px; overflow-y: auto; }
            .mobile-nav-overlay.open { display: block; }
            .mobile-nav-overlay .mobile-nav-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
            .mobile-nav-overlay a { display: block; padding: 14px 4px; font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid var(--line); }
            .mobile-nav-overlay a.active { color: var(--accent); }
            .mobile-nav-overlay form { margin-top: 20px; }
            .mobile-search { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 999px; font-size: .95rem; margin-bottom: 10px; }
            @media (max-width: 760px) {
                .mobile-toggle { display: block; }
                .topbar form[action*="search"] { display: none; }
                #desktop-user-dropdown { display: none; }
            }

            .site-footer-full { border-top: 1px solid var(--line); padding: 40px 0 30px; margin-top: 20px; }
            .footer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 24px; }
            .footer-grid h4 { margin: 0 0 12px; font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--accent); }
            .footer-grid a, .footer-muted { display: block; margin-bottom: 8px; font-size: .88rem; color: var(--ink); }
            .footer-grid a:hover { color: var(--accent); }
            .footer-muted { color: var(--muted); }
            .footer-copy { margin: 0; padding-top: 20px; border-top: 1px solid var(--line); color: var(--muted); font-size: .82rem; text-align: center; }
            @media (max-width: 700px) { .footer-grid { grid-template-columns: 1fr; gap: 20px; } }

            .footer-grid a { display: flex; align-items: center; gap: 9px; }
            .footer-grid .icon { width: 17px; height: 17px; flex-shrink: 0; fill: currentColor; }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('client.dashboard') }}">
                    <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="brand-logo">
                </a>

                <form method="GET" action="{{ route('search.index') }}" style="flex:1; max-width:320px; margin:0 20px;">
                    <input type="text" name="q" placeholder="Rechercher…" value="{{ request('q') }}"
                           style="width:100%; padding:9px 14px; border:1px solid var(--line); border-radius:999px; font-size:.88rem;">
                </form>

                <nav class="nav-links" aria-label="Navigation client">
                    <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">Accueil</a>
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Catégories</a>
                    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">Panier</a>
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Mes commandes</a>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Signalements</a>
                    <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a>
                    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        Notifications
                        @if (($unreadNotificationCount ?? 0) > 0)
                            <span class="badge">{{ $unreadNotificationCount }}</span>
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
                                <button type="submit" class="dropdown-item" style="color:#7a231d;">Déconnexion</button>
                            </form>
                        @endif
                    </div>
                </div>

                <button type="button" class="mobile-toggle" onclick="document.getElementById('mobile-nav').classList.add('open')" aria-label="Ouvrir le menu">☰</button>
            </header>

            <!-- Menu plein écran mobile -->
            <div class="mobile-nav-overlay" id="mobile-nav">
                <div class="mobile-nav-head">
                    <a class="brand" href="{{ route('client.dashboard') }}">
                        <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" style="height:32px; width:auto;">
                    </a>
                    <button type="button" class="mobile-toggle" style="display:block;" onclick="document.getElementById('mobile-nav').classList.remove('open')" aria-label="Fermer le menu">✕</button>
                </div>

                <form method="GET" action="{{ route('search.index') }}">
                    <input type="text" name="q" class="mobile-search" placeholder="Rechercher…" value="{{ request('q') }}">
                </form>

                <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">Accueil</a>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Catégories</a>
                <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">Panier</a>
                <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Mes commandes</a>
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Signalements</a>
                <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a>
                <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    Notifications
                    @if (($unreadNotificationCount ?? 0) > 0)
                        <span class="badge">{{ $unreadNotificationCount }}</span>
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

            <footer class="site-footer-full">
                <div class="footer-grid">
                    <div>
                        <h4>Nous contacter</h4>
                        <a href="tel:+22674919133"><svg class="icon" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg> +226 74 91 91 33</a>
                        <a href="tel:+22671622104"><svg class="icon" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg> +226 71 62 21 04</a>
                        <a href="https://wa.me/22674919133" target="_blank" rel="noopener"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.3c1.5.8 3.1 1.3 4.8 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3C4.2 15 3.8 13.5 3.8 12c0-4.5 3.7-8.2 8.2-8.2s8.2 3.7 8.2 8.2-3.7 8.2-8.2 8.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.2.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.2-.9.9-.9 2.2s.9 2.5 1.1 2.7c.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.4-.3z"/></svg> WhatsApp</a>
                        <a href="mailto:Mosnoky@gmail.com"><svg class="icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm16 4-8 5-8-5V6l8 5 8-5v2z"/></svg> Mosnoky@gmail.com</a>
                    </div>
                    <div>
                        <h4>Nous suivre</h4>
                        <a href="https://www.facebook.com/profile.php?id=61572926362409" target="_blank" rel="noopener"><svg class="icon" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg> Mosnoky</a>
                        <a href="https://www.instagram.com/mosnoky" target="_blank" rel="noopener"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2c2.7 0 3.1 0 4.1.1 1.1.1 1.8.2 2.5.5.7.3 1.2.6 1.8 1.2.6.6.9 1.1 1.2 1.8.3.7.4 1.4.5 2.5.1 1 .1 1.4.1 4.1s0 3.1-.1 4.1c-.1 1.1-.2 1.8-.5 2.5-.3.7-.6 1.2-1.2 1.8-.6.6-1.1.9-1.8 1.2-.7.3-1.4.4-2.5.5-1 .1-1.4.1-4.1.1s-3.1 0-4.1-.1c-1.1-.1-1.8-.2-2.5-.5-.7-.3-1.2-.6-1.8-1.2-.6-.6-.9-1.1-1.2-1.8-.3-.7-.4-1.4-.5-2.5C2 15.1 2 14.7 2 12s0-3.1.1-4.1c.1-1.1.2-1.8.5-2.5.3-.7.6-1.2 1.2-1.8.6-.6 1.1-.9 1.8-1.2.7-.3 1.4-.4 2.5-.5C8.9 2 9.3 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zm5.2-8.4a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0z"/></svg> mosnoky</a>
                        <a href="https://www.tiktok.com/@mosnoky" target="_blank" rel="noopener"><svg class="icon" viewBox="0 0 24 24"><path d="M16.6 5.8c-1-1-1.4-2-1.5-3.3h-3.2v13.3c0 1.8-1.5 3.2-3.2 3.2-1.8 0-3.2-1.5-3.2-3.2 0-1.8 1.5-3.2 3.2-3.2.4 0 .7.1 1 .2v-3.3c-.3 0-.7-.1-1-.1-3.5 0-6.4 2.9-6.4 6.4S5.2 22.2 8.7 22.2c3.5 0 6.4-2.9 6.4-6.4V9.2c1.3.9 2.9 1.5 4.7 1.5V7.4c-1.2 0-2.3-.4-3.2-1.6z"/></svg> @mosnoky</a>
                    </div>
                    <div>
                        <h4>Nous trouver</h4>
                        <a href="https://maps.app.goo.gl/RAqEjHLaet6msWpQ8?g_st=aw" target="_blank" rel="noopene"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2C7.6 2 4 5.6 4 10c0 5.5 7 11.5 7.3 11.7.2.2.4.3.7.3s.5-.1.7-.3C13 21.5 20 15.5 20 10c0-4.4-3.6-8-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg> Pouytenga, en face de la Caisse populaire</a>
                        <span class="footer-muted">🇧🇫 Burkina Faso</span>
                    </div>
                </div>
                <p class="footer-copy">© {{ date('Y') }} {{ config('app.name', 'Mosnoky') }} · Chaussures de luxe & maroquinerie, Made in Burkina Faso.</p>
            </footer>
        </div>
    </body>
</html>
