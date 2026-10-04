<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Mosnoky') }} — Chaussures de luxe & maroquinerie, Made in Burkina Faso</title>
        <meta name="description" content="Mosnoky, atelier spécialisé dans la fabrication artisanale de chaussures de luxe et d'articles de maroquinerie au Burkina Faso.">
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
            .brand-logo { height: 40px; width: auto; }
            .nav-links { display: flex; align-items: center; gap: 22px; font-size: .92rem; font-weight: 600; }
            .nav-links a:hover { color: var(--accent); }
            .button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--line); border-radius: 999px; padding: 11px 20px; font-size: .9rem; font-weight: 700; transition: .2s ease; cursor: pointer; background: transparent; }
            .button:hover { border-color: var(--ink); transform: translateY(-1px); }
            .button.primary { border-color: var(--accent); background: var(--accent); color: white; }

            /* Hero vidéo */
            .hero { position: relative; margin-top: 6px; border-radius: 18px; overflow: hidden; min-height: 560px; display: flex; align-items: center; background: var(--dark); }
            .hero video, .hero .hero-fallback { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .55; }
            .hero-fallback { background: linear-gradient(135deg, #221f1d, #3a332c); }
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
            .product-card .thumb { aspect-ratio: 4/3; background: #eee7dc; overflow: hidden; }
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

            @media (max-width: 900px) {
                .about-grid, .services-grid, .products-grid, .contact-grid { grid-template-columns: 1fr; }
                .hero-content { padding: 36px; }
                .contact-section { padding: 36px; }
            }
            @media (max-width: 760px) {
                .shell { width: min(100% - 28px, 600px); }
                .nav-links { display: none; }
                .hero { min-height: 460px; }
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

            .testimonial-section { text-align: center; }
            .testimonial-quote { max-width: 760px; margin: 0 auto; font-family: Georgia, serif; font-style: italic; font-size: 1.3rem; line-height: 1.7; color: var(--ink); position: relative; padding: 0 30px; }

            @media (max-width: 900px) {
                .why-grid, .mission-grid { grid-template-columns: 1fr; }
                .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="brand-logo">
                </a>
                <nav class="nav-links">
                    <a href="#apropos">À propos</a>
                    <a href="#services">Services</a>
                    <a href="#galerie">Atelier</a>
                    <a href="#produits">Produits</a>
                    <a href="#contact">Contact</a>
                </nav>
                @auth
                    <a class="button primary" href="{{ route('client.dashboard') }}">Mon espace</a>
                @else
                    <div style="display:flex; gap:10px;">
                        <a class="button" href="{{ route('login') }}">Connexion</a>
                        <a class="button primary" href="{{ route('register') }}">Créer un compte</a>
                    </div>
                @endauth
            </header>

            <!-- HERO VIDÉO -->
            <section class="hero">
                <video autoplay muted loop playsinline poster="{{ asset('a.jpeg') }}">
                    <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
                </video>
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
                        <h2>Élégance, authenticité et savoir-faire artisanal</h2>
                        <p>Chez Mosnoky, nous célébrons l'élégance et l'authenticité à travers nos créations. Spécialisés dans la fabrication artisanale de chaussures de luxe et d'articles de maroquinerie, nous allions savoir-faire traditionnel et design moderne.</p>
                        <p>Chaque pièce que nous créons est pensée pour refléter la personnalité de celui qui la porte, avec style, confort et durabilité.</p>
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
                <h2>Ce que nous proposons</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="num">1</div>
                        <h3>Création sur mesure</h3>
                        <p>Des modèles uniques adaptés à votre style et à vos besoins, pour les pieds larges ou les tailles rares.</p>
                    </div>
                    <div class="service-card">
                        <div class="num">2</div>
                        <h3>Maroquinerie</h3>
                        <p>Sacs, ceintures, porte-monnaie et accessoires en cuir, élégants et durables.</p>
                    </div>
                    <div class="service-card">
                        <div class="num">3</div>
                        <h3>Vente en Gros et Detaille</h3>
                        <p>Nous redonnons vie à vos chaussures et articles en cuir avec .</p>
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
                            <h3>Savoir-faire artisanal unique</h3>
                            <p>Chaque chaussure est conçue à la main par des artisans passionnés, garantissant une finition impeccable et une qualité exceptionnelle.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">2</div>
                        <div>
                            <h3>Produits Made in Burkina Faso 🇧🇫</h3>
                            <p>Nous valorisons le savoir-faire local et fabriquons nos modèles avec des matériaux soigneusement sélectionnés.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">3</div>
                        <div>
                            <h3>Confort et élégance réunis</h3>
                            <p>Nos créations ne sont pas seulement belles, elles sont pensées pour offrir un confort optimal au quotidien ou lors d'occasions spéciales.</p>
                        </div>
                    </div>
                    <div class="why-item">
                        <div class="why-num">4</div>
                        <div>
                            <h3>Personnalisation et exclusivité</h3>
                            <p>Avec Mosnoky, chaque client peut avoir une paire qui correspond à son style, rendant chaque modèle unique comme celui qui le porte.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- NOTRE MISSION -->
            <section class="block" id="mission" style="background:var(--card); border-radius:18px; padding:60px;">
                <div class="eyebrow">Notre mission</div>
                <h2>Ce que nous voulons accomplir</h2>
                <div class="mission-grid">
                    <div class="mission-item">
                        <div class="why-num">1</div>
                        <p>Offrir des chaussures et accessoires de qualité, alliant élégance, confort et durabilité.</p>
                    </div>
                    <div class="mission-item">
                        <div class="why-num">2</div>
                        <p>Valoriser le savoir-faire artisanal burkinabè à travers des créations uniques et authentiques.</p>
                    </div>
                    <div class="mission-item">
                        <div class="why-num">3</div>
                        <p>Répondre aux besoins de nos clients en leur proposant des produits qui reflètent leur style et leur identité.</p>
                    </div>
                </div>
            </section>

            <!-- GALERIE ATELIER -->
            <section class="block" id="galerie">
                <div class="eyebrow">Notre savoir-faire</div>
                <h2>Directement de notre atelier</h2>
                <div class="gallery-grid">
                    <img src="{{ asset('images/products/oxford-noir.jpg') }}" alt="Chaussures Oxford noires">
                    <img src="{{ asset('images/products/sac-m-dore.jpg') }}" alt="Sac à main cuir croco et M doré">
                    <img src="{{ asset('images/products/mocassin-noir-vert.jpg') }}" alt="Mocassin noir doublure verte">
                    <img src="{{ asset('images/products/sandale-gladiator-marron.jpg') }}" alt="Sandale gladiator marron">
                    <img src="{{ asset('images/products/sandales-croisees-noir.jpg') }}" alt="Sandales croisées noires">
                    <img src="{{ asset('images/products/sandale-tan-blanc.jpg') }}" alt="Sandale camel et blanc">
                    <img src="{{ asset('images/products/mule-grise-noire.jpg') }}" alt="Mule grise et noire">
                    <img src="{{ asset('images/products/espadrille-marron.jpg') }}" alt="Espadrille marron">
                </div>
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
                                    @if ($primaryImage)
                                        <img src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}">
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
                                        <span style="opacity:.5;">·</span>
                                        {{ $product->sold_count ?? 0 }} vendu(s)
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
    </body>
</html>
