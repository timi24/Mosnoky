<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — Mosnoky</title>
    <style>
        :root {
            --accent: #b1372f;
            --ink: #221f1d;
            --paper: #faf6f1;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .error-box {
            max-width: 480px;
            text-align: center;
        }
        .error-code {
            font-size: 7rem;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 12px;
        }
        .error-text {
            font-size: 1rem;
            color: #5c5652;
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: .95rem;
            transition: .2s ease;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover {
            background: #8f2b24;
        }
        .btn-secondary {
            background: transparent;
            color: var(--ink);
            border: 1px solid #ddd3c6;
        }
        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .logo {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--ink);
            margin-bottom: 36px;
            letter-spacing: .5px;
        }
        .logo span { color: var(--accent); }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="logo">MOS<span>NOKY</span></div>
        <div class="error-code">404</div>
        <div class="error-title">Page introuvable</div>
        <p class="error-text">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
            Vérifiez l'adresse ou retournez à l'accueil pour continuer vos achats.
        </p>
        <div class="error-actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Retour à l'accueil</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Page précédente</a>
        </div>
    </div>
</body>
</html>
