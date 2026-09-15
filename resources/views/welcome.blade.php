<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Monosky') }} - Votre boutique en ligne</title>
        @fonts
        <style>
            :root { color-scheme: light; --ink: #18221f; --muted: #66736e; --paper: #f7f5ef; --card: #fffdf8; --accent: #d96842; --line: #e5e1d8; }
            * { box-sizing: border-box; }
            body { margin: 0; background: var(--paper); color: var(--ink); font-family: "Instrument Sans", ui-sans-serif, sans-serif; }
            a { color: inherit; text-decoration: none; }
            .shell { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
            .topbar { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 24px 0; }
            .brand { font-size: 1.35rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
            .nav { display: flex; align-items: center; gap: 12px; }
            .button { display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--line); border-radius: 999px; padding: 11px 18px; font-size: .9rem; font-weight: 700; transition: .2s ease; }
            .button:hover { border-color: var(--ink); transform: translateY(-1px); }
            .button.primary { border-color: var(--accent); background: var(--accent); color: white; }
            .hero { display: grid; grid-template-columns: 1.15fr .85fr; gap: 40px; align-items: end; padding: 72px 0 58px; }
            .eyebrow { color: var(--accent); font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
            h1 { max-width: 700px; margin: 14px 0 18px; font-family: Georgia, serif; font-size: clamp(3.3rem, 7vw, 6.6rem); font-weight: 400; line-height: .94; letter-spacing: 0; }
            .hero p { max-width: 540px; color: var(--muted); font-size: 1.1rem; line-height: 1.7; }
            .hero-note { border-left: 1px solid var(--accent); padding: 10px 0 10px 22px; color: var(--muted); line-height: 1.6; }
            .catalogue { border-top: 1px solid var(--line); padding: 42px 0 90px; }
            .catalogue-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
            h2 { margin: 0; font-family: Georgia, serif; font-size: 2.5rem; font-weight: 400; }
            .categories { color: var(--muted); font-size: .9rem; }
            .products { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
            .product { display: flex; min-height: 100%; flex-direction: column; overflow: hidden; border: 1px solid var(--line); border-radius: 10px; background: var(--card); }
            .product-image { display: grid; aspect-ratio: 4 / 3; place-items: center; overflow: hidden; background: #e9e5da; color: #928c7f; }
            .product-image img { width: 100%; height: 100%; object-fit: cover; }
            .product-image span { font-size: .85rem; }
            .product-content { display: flex; flex: 1; flex-direction: column; gap: 12px; padding: 20px; }
            .product-category { color: var(--accent); font-size: .75rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
            h3 { margin: 0; font-size: 1.25rem; }
            .description { margin: 0; color: var(--muted); line-height: 1.5; }
            .product-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 10px; }
            .price { font-size: 1.15rem; font-weight: 800; }
            .empty { border: 1px dashed #cfc8b9; border-radius: 10px; padding: 48px 20px; text-align: center; color: var(--muted); }
            footer { border-top: 1px solid var(--line); padding: 24px 0 40px; color: var(--muted); font-size: .85rem; }
            @media (max-width: 760px) { .shell { width: min(100% - 28px, 600px); } .hero { grid-template-columns: 1fr; gap: 18px; padding: 44px 0; } h1 { font-size: 3.8rem; } .products { grid-template-columns: 1fr; } .catalogue-head { align-items: start; flex-direction: column; } .nav .button:first-child { display: none; } }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">{{ config('app.name', 'Monosky') }}</a>
                <nav class="nav" aria-label="Navigation principale">
                    @auth
                        <a class="button" href="{{ route('client.dashboard') }}">Mon espace</a>
                    @else
                        <a class="button" href="{{ route('login') }}">Se connecter</a>
                        @if (Route::has('register'))
                            <a class="button primary" href="{{ route('register') }}">Créer un compte</a>
                        @endif
                    @endauth
                </nav>
            </header>

            <main>
                <section class="hero">
                    <div>
                        <div class="eyebrow">La sélection du moment</div>
                        <h1>Des objets choisis pour durer.</h1>
                        <p>Découvrez une collection pensée avec soin, des produits utiles et une expérience d’achat simple, claire et humaine.</p>
                        <a class="button primary" href="#produits">Découvrir les produits</a>
                    </div>
                    <p class="hero-note">Livraison soignée, paiement sécurisé et accompagnement client à chaque étape.</p>
                </section>

                <section id="produits" class="catalogue" aria-labelledby="catalogue-title">
                    <div class="catalogue-head">
                        <h2 id="catalogue-title">Nos produits</h2>
                        @if ($categories->isNotEmpty())
                            <div class="categories">{{ $categories->implode(' · ') }}</div>
                        @endif
                    </div>

                    @if ($products->isEmpty())
                        <div class="empty">Les produits arrivent bientôt. Revenez découvrir la prochaine sélection.</div>
                    @else
                        <div class="products">
                            @foreach ($products as $product)
                                <article class="product">
                                    <div class="product-image">
                                        @if ($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->alternative_text ?: $product->name }}">
                                        @else
                                            <span>Image bientôt disponible</span>
                                        @endif
                                    </div>
                                    <div class="product-content">
                                        <div class="product-category">{{ $product->category_name ?: 'Collection' }}</div>
                                        <h3>{{ $product->name }}</h3>
                                        <p class="description">{{ Str::limit($product->description, 105) }}</p>
                                        <div class="product-footer">
                                            <span class="price">{{ number_format($product->price, 2, ',', ' ') }} FCFA</span>
                                            <a class="button primary" href="{{ route('products.purchase', $product->id) }}">Acheter</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </main>

            <footer>© {{ date('Y') }} {{ config('app.name', 'Monosky') }} · Une boutique pensée pour vous.</footer>
        </div>
    </body>
</html>
