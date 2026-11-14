<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta name="google-site-verification" content="I7rp9O-NEDvrppuhalUy4LkpvlV41xe8mXRJFURNt04" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MOSNOKY') }} — Chaussures & maroquinerie artisanales</title>
        <meta name="description" content="MOSNOKY, marque burkinabè de chaussures et d'articles de maroquinerie, inspirée par le savoir-faire artisanal de Pouytenga.">
        @if ($heroImages->isNotEmpty())
            <link rel="preload" as="image" href="{{ $heroImages->first()->image_url }}" fetchpriority="high">
        @endif
        @fonts
        <style>
            :root { color-scheme: light; --ink: #221f1d; --muted: #857b72; --paper: #faf6f1; --card: #ffffff; --accent: #b1372f; --dark: #17140f; --line: #ece4d8; }
            * { box-sizing: border-box; }
            body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Instrument Sans", ui-sans-serif, sans-serif; }
            a { color: inherit; text-decoration: none; }
            .shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
            img { max-width: 100%; display: block; }

            /* Topbar */
            .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 22px 0; }
            .brand { display: flex; align-items: center; gap: 10px; font-size: 1.15rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
            .brand-logo { height: 50px; width: auto; }
            .topbar-actions { display: flex; align-items: center; gap: 10px; }
            .button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--line); border-radius: 999px; padding: 11px 20px; font-size: .9rem; font-weight: 700; transition: .2s ease; cursor: pointer; background: transparent; }
            .button:hover { border-color: var(--ink); transform: translateY(-1px); }
            .button.primary { border-color: var(--accent); background: var(--accent); color: white; }

            /* Hero vidéo */
            .hero { position: relative; margin-top: 6px; border-radius: 18px; overflow: hidden; min-height: 560px; display: flex; align-items: center; background: var(--dark); }
            .hero video, .hero .hero-fallback { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .55; }
            .hero-fallback { background: linear-gradient(135deg, #221f1d, #3a332c); }
            .hero-slide { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity .5s ease; }
            .hero-slide.active { opacity: .55; }
            .hero-content { position: relative; z-index: 2; padding: 60px; color: #fff; max-width: 640px; }
            .hero-eyebrow { color: #f0b8ac; font-size: .8rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
            .hero h1 { font-family: Georgia, serif; font-size: clamp(2.4rem, 5vw, 3.6rem); font-weight: 400; line-height: 1.05; margin: 16px 0 20px; }
            .hero p { font-size: 1.05rem; line-height: 1.7; color: #e8e3db; margin-bottom: 28px; }
            .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
            .button.on-dark { border-color: rgba(255,255,255,.4); color: #fff; }
            .button.on-dark:hover { border-color: #fff; }
            /* Sections génériques */
            section.block { padding: 70px 0; }
            .eyebrow { color: var(--accent); font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
            h2 { margin: 10px 0 18px; font-family: Georgia, serif; font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 400; }

            /* À propos */
            .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; }
            .about-grid p { color: var(--muted); line-height: 1.8; font-size: 1.02rem; margin-bottom: 16px; }
            .about-badges { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
            .about-badge { padding: 8px 16px; border: 1px solid var(--line); border-radius: 999px; font-size: .82rem; font-weight: 700; background: var(--card); }

            /* Services */
            .services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
            .service-card { padding: 30px 26px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
            .service-card .num { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: var(--accent); color: #fff; font-weight: 800; font-size: .9rem; margin-bottom: 16px; }
            .service-card h3 { margin: 0 0 8px; font-size: 1.1rem; }
            .service-card p { margin: 0; color: var(--muted); font-size: .92rem; line-height: 1.6; }

            /* Produits vedettes */
            .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
            .product-card { display: flex; flex-direction: column; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: var(--card); transition: .2s ease; }
            .product-card:hover { border-color: var(--accent); transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.06); }
            .product-card .thumb { position: relative; aspect-ratio: 4/3; background: #eee7dc; overflow: hidden; }
            .product-slide { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transform: scale(1.04); transition: opacity .5s ease; }
            .product-slide.active { opacity: 1; animation: none; transform: none; }
            @keyframes kenburns { from { transform: scale(1.08); } to { transform: scale(1); } }
            .thumb-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 34px;
                height: 34px;
                border: 1px solid rgba(255,255,255,.35);
                border-radius: 50%;
                background: rgba(255,255,255,.16);
                -webkit-backdrop-filter: blur(8px);
                backdrop-filter: blur(8px);
                color: #fff;
                font-size: 1.2rem;
                line-height: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 2;
                opacity: 0;
                transition: opacity .25s ease, background .2s ease;
            }
            .product-card .thumb:hover .thumb-arrow, .thumb-arrow:focus-visible { opacity: 1; }
            .thumb-arrow:hover { background: rgba(255,255,255,.3); }
            .thumb-arrow-prev { left: 8px; }
            .thumb-arrow-next { right: 8px; }
            .product-card .thumb img { width: 100%; height: 100%; object-fit: cover; }
            .product-card .body { padding: 18px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
            .product-card h3 { margin: 0; font-size: 1.05rem; }
            .rating-line { font-size: .8rem; color: var(--muted); }
            .gold-stars { color: #e8a33d; letter-spacing: 1px; }
            .price-row { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 8px; }
            .price { font-weight: 800; font-size: 1.05rem; }

            /* Contact / Footer */
            .contact-section { background: var(--dark); color: #efe9e0; border-radius: 18px; padding: 60px; margin: 20px 0 40px; }
            .contact-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 36px; }
            .contact-grid h3 { margin: 0 0 14px; font-size: 1rem; color: #f0b8ac; text-transform: uppercase; letter-spacing: .08em; font-size: .78rem; font-weight: 800; }
            .contact-line { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; font-size: .95rem; }
            .contact-line .icon { width: 18px; height: 18px; fill: currentColor; flex-shrink: 0; opacity: .85; }
            .contact-line a:hover { color: #f0b8ac; }
            footer.site-footer { padding: 24px 0 50px; color: var(--muted); font-size: .85rem; text-align: center; }

            /* Sur téléphone/tablette il n'y a pas de survol : les flèches restent visibles */
            @media (hover: none) {
                .thumb-arrow { opacity: 1; }
            }
            .hero, .product-card .thumb { touch-action: pan-y; }
            /* Respect des personnes qui désactivent les animations sur leur appareil */
            @media (prefers-reduced-motion: reduce) {
                .hero-slide, .product-slide { transition-duration: .01s; }
                .gallery-grid img { transition-duration: .01s; }
            }

            @media (max-width: 900px) {
                .about-grid, .services-grid, .products-grid, .contact-grid { grid-template-columns: 1fr; }
                .hero-content { padding: 36px; }
                .contact-section { padding: 36px; }
            }
            @media (max-width: 760px) {
                .shell { width: min(100% - 28px, 600px); }
                .topbar { gap: 10px; padding: 14px 0; }
                .brand-logo { height: 46px; }
                .topbar-actions { gap: 7px; }
                .topbar-actions .button { padding: 9px 12px; font-size: .82rem; }
                .hero { min-height: 460px; border-radius: 14px; }
                .hero-content { padding: 32px 26px; }
                .hero h1 { font-size: clamp(2.25rem, 10vw, 3rem); }
                .hero p { font-size: .98rem; }
            }
            @media (max-width: 420px) {
                .topbar-actions .button { padding: 9px 11px; font-size: .78rem; }
                .hero-content { padding: 28px 22px; }
                .hero-actions { gap: 8px; }
                .hero-actions .button { padding: 10px 14px; font-size: .82rem; }
            }

            .why-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
            .why-item { display: flex; gap: 16px; align-items: flex-start; }
            .why-num { flex-shrink: 0; width: 34px; height: 34px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; }
            .why-item h3 { margin: 0 0 6px; font-size: 1.05rem; }
            .why-item p { margin: 0; color: var(--muted); font-size: .92rem; line-height: 1.6; }

            .mission-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
            .mission-item { display: flex; gap: 14px; align-items: flex-start; }
            .mission-item p { margin: 0; color: var(--muted); font-size: .94rem; line-height: 1.6; }

            .gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
            .gallery-grid img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 12px; }
            .craft-copy { max-width: 900px; margin: 0 0 26px; color: var(--muted); font-size: 1rem; line-height: 1.8; }
            .craft-copy p { margin: 0 0 12px; }
            .craft-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 24px; margin: 0 0 28px; padding: 0; list-style: none; }
            .craft-list li { position: relative; padding-left: 22px; color: var(--muted); line-height: 1.6; }
            .craft-list li::before { content: '•'; position: absolute; left: 4px; color: var(--accent); font-weight: 800; }

            .testimonial-section { text-align: center; }
            .testimonial-quote { max-width: 760px; margin: 0 auto; font-family: Georgia, serif; font-style: italic; font-size: 1.3rem; line-height: 1.7; color: var(--ink); position: relative; padding: 0 30px; }

            @media (max-width: 900px) {
                .why-grid, .mission-grid { grid-template-columns: 1fr; }
                .gallery-grid { grid-template-columns: repeat(2, 1fr); }
                .craft-list { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="brand-logo">
                </a>
                @auth
                    <div class="topbar-actions"><a class="button primary" href="{{ route('client.dashboard') }}">Mon espace</a></div>
                @else
                    <div class="topbar-actions">
                        <a class="button" href="{{ route('login') }}">Se connecter</a>
                        <a class="button primary" href="{{ route('register') }}">Créer un compte</a>
                    </div>
                @endauth
            </header>

            <!-- HERO -->
            <section class="hero">
                @forelse ($heroImages as $index => $img)
                    <img @if ($index === 0) src="{{ $img->image_url }}" fetchpriority="high" @else data-src="{{ $img->image_url }}" @endif alt="Mosnoky" class="hero-slide @if ($index === 0) active @endif" decoding="async">
                @empty
                    <div class="hero-fallback"></div>
                @endforelse
                <div class="hero-content">
                    <div class="hero-eyebrow">Made in Burkina Faso</div>
                    <h1>Votre style notre savoir faire.</h1>
                    <p>Fabrication artisanale et moderne de chaussures et articles en cuir.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="#produits">Découvrir nos créations</a>
                        @guest
                            <a class="button on-dark" href="{{ route('register') }}">Créer un compte</a>
                        @endguest
                    </div>
                </div>
            </section>

            <!-- À PROPOS -->
            <section class="block" id="apropos">
                <div class="about-grid">
                    <div>
                        <div class="eyebrow">À propos de nous</div>
                        <h2>MOSNOKY, l'élégance inspirée du savoir-faire burkinabè</h2>
                        <p>Bienvenue chez MOSNOKY, une marque burkinabè spécialisée dans la conception, la fabrication et la commercialisation de chaussures et d'articles de maroquinerie.</p>
                        <p>Née d'une passion pour la création et le travail du cuir, MOSNOKY propose des produits qui associent élégance, qualité, confort et originalité. Basée à Pouytenga, au Burkina Faso, notre entreprise met en valeur le savoir-faire artisanal local à travers des créations pensées pour celles et ceux qui apprécient le style, le raffinement et le souci du détail.</p>
                        <div class="about-badges">
                            <span class="about-badge">🇧🇫 Made in Burkina Faso</span>
                            <span class="about-badge">✋ Fait main</span>
                            <span class="about-badge">📦 Livraison disponible</span>
                        </div>
                    </div>
                     <div>
                        <img src="{{ asset('images/products/oxford-noir.jpg') }}" alt="Chaussures Oxford Mosnoky" style="border-radius:14px; width:100%; object-fit:cover; aspect-ratio:4/3;">
                    </div>
                </div>
            </section>

            <!-- SERVICES -->
            <section class="block" id="services">
                <div class="eyebrow">Nos services</div>
                        <h2>Des créations pour chaque style</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="num">1</div>
                        <h3>Chaussures et sandales</h3>
                        <p>Des créations pour hommes et femmes, conçues pour allier confort, élégance et qualité.</p>
                    </div>
                    <div class="service-card">
                        <div class="num">2</div>
                        <h3>Maroquinerie et accessoires</h3>
                        <p>Sacs à main, ceintures, sacoches et autres articles en cuir soigneusement réalisés.</p>
                    </div>
                    <div class="service-card">
                        <div class="num">3</div>
                        <h3>Créations personnalisées</h3>
                        <p>Des articles adaptés aux envies de nos clients, avec une attention particulière portée aux détails.</p>
                    </div>
                </div>
            </section>

            <!-- POURQUOI NOUS CHOISIR -->
            <section class="block" id="pourquoi">
                <div class="eyebrow">Pourquoi nous choisir</div>
                <h2>Ce qui fait la différence Mosnoky</h2>
                <div class="why-grid">
                    <div class="why-item">
                        <div class="why-num">1</div>
                        <div>
                        <h3>Un travail minutieux</h3>
                        <p>De la conception aux finitions, chaque création bénéficie d'un travail attentif et d'une recherche constante de qualité.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">2</div>
                        <div>
                        <h3>Le savoir-faire burkinabè 🇧🇫</h3>
                        <p>Basés à Pouytenga, nous mettons en valeur le savoir-faire artisanal local à travers des créations uniques et authentiques.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">3</div>
                        <div>
                        <h3>Confort et élégance réunis</h3>
                        <p>Nous croyons qu'une belle création doit aussi offrir confort, durabilité et satisfaction au quotidien.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">4</div>
                        <div>
                        <h3>À l'écoute de vos envies</h3>
                        <p>Nous sommes attentifs aux besoins de nos clients et proposons des créations personnalisées, originales et élégantes.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- NOTRE MISSION -->
            <section class="block" id="mission" style="background:var(--card); border-radius:18px; padding:60px;">
                <div class="eyebrow">Notre engagement</div>
                <h2>La qualité, dans chaque détail</h2>
                <div class="mission-grid">
                    <div class="mission-item">
                        <div class="why-num">1</div>
                        <p>Privilégier des matières soigneusement sélectionnées et soigner les finitions de chaque produit.</p>
                    </div>
                    <div class="mission-item">
                        <div class="why-num">2</div>
                        <p>Proposer des créations originales et élégantes qui valorisent le savoir-faire artisanal burkinabè.</p>
                    </div>
                    <div class="mission-item">
                        <div class="why-num">3</div>
                        <p>Être à l'écoute de vos attentes et améliorer continuellement la qualité de nos réalisations.</p>
                    </div>
                </div>
            </section>

            <!-- GALERIE ATELIER -->
            <section class="block" id="galerie">
                <div class="eyebrow">Notre savoir-faire</div>
                <h2>Des créations pensées et fabriquées avec soin</h2>
                <div class="craft-copy">
                    <p>Chez MOSNOKY, chaque création est le résultat d'un travail minutieux, d'une sélection attentive des matières et d'une recherche constante de qualité.</p>
                    <p>Nous concevons et fabriquons différentes créations, notamment :</p>
                </div>
                <ul class="craft-list">
                    <li>Des chaussures et sandales pour hommes et femmes.</li>
                    <li>Des sacs à main et accessoires de maroquinerie.</li>
                    <li>Des ceintures, sacoches et autres articles en cuir.</li>
                    <li>Des créations personnalisées adaptées aux envies de nos clients.</li>
                </ul>
                <p class="craft-copy">De la conception aux finitions, nous accordons une attention particulière aux détails afin de proposer des produits qui reflètent notre engagement envers la qualité, le confort et l'élégance.</p>
            </section>

            <!-- TÉMOIGNAGE -->
            <section class="block testimonial-section">
                <div class="testimonial-quote">
                    "Chez Mosnoky, nous offrons la possibilité d'acheter en gros ou au détail, selon vos besoins. Grâce à notre service fiable, nous assurons la livraison partout dans le monde, afin que nos créations 'Made in Burkina Faso' puissent voyager jusqu'à vous."
                </div>
            </section>

            <!-- PRODUITS VEDETTES -->
            <section class="block" id="produits">
                <div class="eyebrow">Notre catalogue</div>
                <h2>Nos créations récentes</h2>

                @if ($featuredProducts->isEmpty())
                    <p style="color:var(--muted);">Nos produits arrivent bientôt. Revenez très vite !</p>
                @else
                    <div class="products-grid">
                        @foreach ($featuredProducts as $product)
                            @php
                                $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                $productLink = auth()->check() ? route('products.show', $product) : route('login');
                            @endphp
                            <a class="product-card" href="{{ $productLink }}">
                                <div class="thumb">
                                    @if ($product->images->isNotEmpty())
                                        @foreach ($product->images as $i => $image)
                                            <img @if ($i === 0) src="{{ $image->image_url }}" fetchpriority="low" @else data-src="{{ $image->image_url }}" @endif alt="{{ $product->name }}" class="product-slide @if ($i === 0) active @endif" @if ($i === 0) loading="eager" decoding="async" @else loading="lazy" decoding="async" @endif>
                                        @endforeach
                                        @if ($product->images->count() > 1)
                                            <button type="button" class="thumb-arrow thumb-arrow-prev" aria-label="Photo précédente">‹</button>
                                            <button type="button" class="thumb-arrow thumb-arrow-next" aria-label="Photo suivante">›</button>
                                        @endif
                                    @else
                                        <div style="display:flex; align-items:center; justify-content:center; height:100%; color:var(--muted); font-size:.85rem;">Photo à venir</div>
                                    @endif
                                </div>
                                <div class="body">
                                    <h3>{{ $product->name }}</h3>
                                    <div class="rating-line">
                                        @if ($product->approved_reviews_count > 0)
                                            <span class="gold-stars">{{ str_repeat('★', round($product->approved_reviews_avg_rating)) }}{{ str_repeat('☆', 5 - round($product->approved_reviews_avg_rating)) }}</span>
                                            {{ number_format($product->approved_reviews_avg_rating, 1) }}
                                        @else
                                            Aucun avis
                                        @endif

                                    </div>
                                    <div class="price-row">
                                        <span class="price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- CONTACT -->
            <section class="contact-section" id="contact">
                <div class="contact-grid">
                    <div>
                        <h3>Nous contacter</h3>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg><a href="tel:+22674919133">+226 74 91 91 33</a></div>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg><a href="tel:+22671622104">+226 71 62 21 04</a></div>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.3c1.5.8 3.1 1.3 4.8 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18c-1.5 0-3-.4-4.3-1.2l-.3-.2-3.1.8.8-3-.2-.3C4.2 15 3.8 13.5 3.8 12c0-4.5 3.7-8.2 8.2-8.2s8.2 3.7 8.2 8.2-3.7 8.2-8.2 8.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.2.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.2-.9.9-.9 2.2s.9 2.5 1.1 2.7c.1.2 1.9 2.9 4.6 4 .6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.4-.3z"/></svg><a href="https://wa.me/22674919133" target="_blank" rel="noopener">Discuter sur WhatsApp</a></div>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm16 4-8 5-8-5V6l8 5 8-5v2z"/></svg><a href="mailto:Mosnoky@gmail.com">Mosnoky@gmail.com</a></div>
                    </div>
                    <div>
                        <h3>Nous trouver</h3>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2C7.6 2 4 5.6 4 10c0 5.5 7 11.5 7.3 11.7.2.2.4.3.7.3s.5-.1.7-.3C13 21.5 20 15.5 20 10c0-4.4-3.6-8-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg><a href="https://maps.app.goo.gl/RAqEjHLaet6msWpQ8?g_st=aw" target="_blank" rel="noopener">Pouytenga, en face de la Caisse populaire</a></div>
                        <div class="contact-line">🇧🇫 Burkina Faso</div>
                    </div>
                    <div>
                        <h3>Suivez-nous</h3>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg><a href="https://www.facebook.com/profile.php?id=61572926362409" target="_blank" rel="noopener">Facebook — Mosnoky</a></div>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2c2.7 0 3.1 0 4.1.1 1.1.1 1.8.2 2.5.5.7.3 1.2.6 1.8 1.2.6.6.9 1.1 1.2 1.8.3.7.4 1.4.5 2.5.1 1 .1 1.4.1 4.1s0 3.1-.1 4.1c-.1 1.1-.2 1.8-.5 2.5-.3.7-.6 1.2-1.2 1.8-.6.6-1.1.9-1.8 1.2-.7.3-1.4.4-2.5.5-1 .1-1.4.1-4.1.1s-3.1 0-4.1-.1c-1.1-.1-1.8-.2-2.5-.5-.7-.3-1.2-.6-1.8-1.2-.6-.6-.9-1.1-1.2-1.8-.3-.7-.4-1.4-.5-2.5C2 15.1 2 14.7 2 12s0-3.1.1-4.1c.1-1.1.2-1.8.5-2.5.3-.7.6-1.2 1.2-1.8.6-.6 1.1-.9 1.8-1.2.7-.3 1.4-.4 2.5-.5C8.9 2 9.3 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zm5.2-8.4a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0z"/></svg><a href="https://www.instagram.com/mosnoky" target="_blank" rel="noopener">Instagram — Mosnoky</a></div>
                        <div class="contact-line"><svg class="icon" viewBox="0 0 24 24"><path d="M16.6 5.8c-1-1-1.4-2-1.5-3.3h-3.2v13.3c0 1.8-1.5 3.2-3.2 3.2-1.8 0-3.2-1.5-3.2-3.2 0-1.8 1.5-3.2 3.2-3.2.4 0 .7.1 1 .2v-3.3c-.3 0-.7-.1-1-.1-3.5 0-6.4 2.9-6.4 6.4S5.2 22.2 8.7 22.2c3.5 0 6.4-2.9 6.4-6.4V9.2c1.3.9 2.9 1.5 4.7 1.5V7.4c-1.2 0-2.3-.4-3.2-1.6z"/></svg><a href="https://www.tiktok.com/@mosnoky" target="_blank" rel="noopener">TikTok — Mosnoky</a></div>
                    </div>
                </div>
            </section>


            <footer class="site-footer">
                © {{ date('Y') }} Mosnoky · Chaussures de luxe & maroquinerie, Made in Burkina Faso.
            </footer>
        </div>
        <script>
(function () {
    function setupSlideshow(container, slideSelector, dotSelector, prevSelector, nextSelector) {
        const slides = container.querySelectorAll(slideSelector);
        const dots = dotSelector ? container.querySelectorAll(dotSelector) : [];
        const prevBtn = prevSelector ? container.querySelector(prevSelector) : null;
        const nextBtn = nextSelector ? container.querySelector(nextSelector) : null;
        if (slides.length <= 1) return;

        let current = 0;
        let timer = null;
        let isVisible = !('IntersectionObserver' in window);

        function loadSlide(slide) {
            if (slide.dataset.src) {
                slide.src = slide.dataset.src;
                delete slide.dataset.src;
            }
        }

        function preloadNext() {
            loadSlide(slides[(current + 1) % slides.length]);
        }

        function show(index) {
            slides[current].classList.remove('active');
            dots[current] && dots[current].classList.remove('active');
            current = (index + slides.length) % slides.length;
            loadSlide(slides[current]);
            slides[current].classList.add('active');
            dots[current] && dots[current].classList.add('active');
            preloadNext();
        }

        function next() { show(current + 1); }
        function prev() { show(current - 1); }

        function start() {
            if (timer || document.hidden || !isVisible) return;
            timer = setInterval(next, 3000);
        }
        function stop() { clearInterval(timer); timer = null; }
        function restart() { stop(); start(); }
        start();

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function (entries) {
                isVisible = entries[0].isIntersecting;
                isVisible ? start() : stop();
            }, { rootMargin: '100px' });
            observer.observe(container);
        }

        // Pause au survol de la souris seulement (sur téléphone, le "survol"
        // resterait bloqué après un toucher et arrêterait le défilement).
        container.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') stop(); });
        container.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') start(); });

        // On économise la batterie quand l'onglet n'est pas visible
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) { stop(); } else { start(); }
        });

        // Glisser du doigt (gauche/droite) pour changer de photo
        let startX = 0, startY = 0, swiped = false;
        container.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            swiped = false;
        }, { passive: true });
        container.addEventListener('touchend', function (e) {
            const dx = e.changedTouches[0].clientX - startX;
            const dy = e.changedTouches[0].clientY - startY;
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                swiped = true;
                dx < 0 ? next() : prev();
                restart();
            }
        }, { passive: true });
        // Un glissement ne doit pas ouvrir la page du produit
        container.addEventListener('click', function (e) {
            if (swiped) { e.preventDefault(); e.stopPropagation(); swiped = false; }
        }, true);

        // Flèches du clavier quand une flèche/un point a le focus
        container.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') { next(); restart(); }
            if (e.key === 'ArrowLeft') { prev(); restart(); }
        });

        // Le client peut cliquer pour voir chaque photo lui-même, sans que
        // ça déclenche le lien de la carte en dessous.
        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                prev();
                restart();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                next();
                restart();
            });
        }
        dots.forEach(function (dot) {
            dot.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                show(parseInt(dot.dataset.index, 10));
                restart();
            });
        });
    }

    // Diaporama du hero
    const hero = document.querySelector('.hero');
    if (hero) {
        setupSlideshow(hero, '.hero-slide', null, null, null);
    }

    // Diaporama de chaque carte produit
    document.querySelectorAll('.product-card .thumb').forEach(function (thumb) {
        setupSlideshow(thumb, '.product-slide', null, '.thumb-arrow-prev', '.thumb-arrow-next');
    });

})();
</script>
    </body>
</html>
