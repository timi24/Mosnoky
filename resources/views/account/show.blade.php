@extends($layout)

@section('title', 'Mon compte')

@section('content')
    @php
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'Mon compte');
        $initial = strtoupper(mb_substr($fullName, 0, 1));
        $phone = $user->phone ?? $user->telephone ?? null;
    @endphp

    <div class="acc-head">
        <h1 class="acc-title">Mon compte</h1>
        <p class="acc-sub">Retrouvez vos informations et gérez la sécurité de votre accès.</p>
    </div>

    @if (session('account_status'))
        <div class="acc-alert ok">✓ {{ session('account_status') }}</div>
    @endif

    <div class="acc-grid">
        {{-- INFORMATIONS --}}
        <section class="acc-card">
            <div class="acc-id">
                <div class="acc-avatar">{{ $initial }}</div>
                <div>
                    <strong class="acc-name">{{ $fullName }}</strong>
                    <span class="acc-role">{{ $isAdmin ? 'Administrateur' : 'Client' }}</span>
                </div>
            </div>

            <dl class="acc-list">
                <div><dt>Adresse e-mail</dt><dd>{{ $user->email }}</dd></div>
                @if ($phone)
                    <div><dt>Téléphone</dt><dd>{{ $phone }}</dd></div>
                @endif
                @if ($user->created_at)
                    <div><dt>Membre depuis</dt><dd>{{ $user->created_at->translatedFormat('d F Y') }}</dd></div>
                @endif
            </dl>

            @if ($errors->profile->any())
                <div class="acc-alert err">
                    @foreach ($errors->profile->all() as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('account.profile') }}" class="acc-form acc-profile-form">
                @csrf
                @method('PUT')
                <label class="acc-field">
                    <span>Nom complet</span>
                    <input type="text" name="name" value="{{ old('name', $fullName) }}" autocomplete="name" maxlength="255" required>
                </label>
                <label class="acc-field">
                    <span>Téléphone</span>
                    <input type="tel" name="phone" value="{{ old('phone', $phone) }}" autocomplete="tel" inputmode="tel" placeholder="Ex. 70 12 34 56">
                </label>
                <button type="submit" class="acc-btn">Enregistrer mes coordonnées</button>
            </form>
        </section>

        @unless ($isAdmin)
            <section class="acc-card">
                <h2 class="acc-h2">Adresse de livraison</h2>
                <p class="acc-hint">Elle sera proposée automatiquement lors de votre prochaine commande.</p>

                @if ($errors->address->any())
                    <div class="acc-alert err">
                        @foreach ($errors->address->all() as $message)
                            <div>{{ $message }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('account.address') }}" class="acc-form">
                    @csrf
                    @method('PUT')
                    <label class="acc-field">
                        <span>Adresse</span>
                        <input type="text" name="street" value="{{ old('street', $deliveryAddress?->street) }}" maxlength="255" required>
                    </label>
                    <label class="acc-field">
                        <span>Ville</span>
                        <input type="text" name="city" value="{{ old('city', $deliveryAddress?->city) }}" maxlength="100" required>
                    </label>
                    <label class="acc-field">
                        <span>Pays</span>
                        <input type="text" name="country" value="{{ old('country', $deliveryAddress?->country ?? 'Burkina Faso') }}" maxlength="100" required>
                    </label>
                    <label class="acc-field">
                        <span>Code postal (facultatif)</span>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $deliveryAddress?->postal_code) }}" maxlength="20" autocomplete="postal-code">
                    </label>
                    <button type="submit" class="acc-btn">Enregistrer l’adresse</button>
                </form>
            </section>
        @endunless

        {{-- MOT DE PASSE --}}
        <section class="acc-card">
            <h2 class="acc-h2">Changer mon mot de passe</h2>
            <p class="acc-hint">Choisissez un mot de passe que vous n'utilisez nulle part ailleurs.</p>

            @if ($errors->password->any())
                <div class="acc-alert err">
                    @foreach ($errors->password->all() as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('account.password') }}" class="acc-form">
                @csrf
                @method('PUT')

                <label class="acc-field">
                    <span>Mot de passe actuel</span>
                    <div class="acc-pass">
                        <input type="password" name="current_password" autocomplete="current-password" required>
                        <button type="button" class="acc-eye" data-eye aria-label="Afficher ou masquer">👁</button>
                    </div>
                </label>

                <label class="acc-field">
                    <span>Nouveau mot de passe</span>
                    <div class="acc-pass">
                        <input type="password" name="password" autocomplete="new-password" required minlength="10">
                        <button type="button" class="acc-eye" data-eye aria-label="Afficher ou masquer">👁</button>
                    </div>
                    <small>10 caractères minimum, avec majuscule, minuscule, chiffre et symbole.</small>
                </label>

                <label class="acc-field">
                    <span>Confirmer le nouveau mot de passe</span>
                    <div class="acc-pass">
                        <input type="password" name="password_confirmation" autocomplete="new-password" required>
                        <button type="button" class="acc-eye" data-eye aria-label="Afficher ou masquer">👁</button>
                    </div>
                </label>

                <button type="submit" class="acc-btn">Enregistrer le nouveau mot de passe</button>
            </form>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-eye]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = btn.parentElement.querySelector('input');
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });
    </script>

    <style>
        .acc-head { margin-bottom: 22px; }
        .acc-title { font-family: Georgia, serif; font-size: 1.8rem; font-weight: 400; margin: 0 0 6px; }
        .acc-sub { margin: 0; color: var(--muted); font-size: .95rem; }

        .acc-grid { display: grid; grid-template-columns: 1fr 1.25fr; gap: 24px; align-items: start; max-width: 980px; }
        .acc-card { background: var(--card, #fff); border: 1px solid var(--line); border-radius: 16px; padding: 26px; box-shadow: 0 2px 12px rgba(34, 31, 29, .05); }

        .acc-id { display: flex; align-items: center; gap: 14px; padding-bottom: 20px; border-bottom: 1px solid var(--line); margin-bottom: 18px; }
        .acc-avatar { flex-shrink: 0; width: 56px; height: 56px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 800; box-shadow: 0 6px 16px rgba(177, 55, 47, .3); }
        .acc-name { display: block; font-size: 1.1rem; margin-bottom: 4px; }
        .acc-role { display: inline-block; padding: 3px 11px; border-radius: 999px; background: rgba(177, 55, 47, .1); color: var(--accent); font-size: .75rem; font-weight: 800; }

        .acc-list { margin: 0; display: flex; flex-direction: column; gap: 14px; }
        .acc-list dt { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: 2px; }
        .acc-list dd { margin: 0; font-weight: 600; word-break: break-word; }

        .acc-h2 { margin: 0 0 4px; font-size: 1.15rem; }
        .acc-hint { margin: 0 0 18px; color: var(--muted); font-size: .88rem; }

        .acc-form { display: flex; flex-direction: column; gap: 16px; }
        .acc-field { display: flex; flex-direction: column; gap: 6px; }
        .acc-field > span { font-size: .85rem; font-weight: 700; }
        .acc-field input:not([type=password]) { width: 100%; box-sizing: border-box; min-height: 44px; padding: 11px 13px; border: 1.5px solid var(--line); border-radius: 11px; font: inherit; background: #fff; color: var(--ink); }
        .acc-field input:not([type=password]):focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(177,55,47,.1); }
        .acc-profile-form { margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--line); }
        .acc-field small { color: var(--muted); font-size: .78rem; }
        .acc-pass { position: relative; }
        .acc-pass input { width: 100%; box-sizing: border-box; padding: 12px 46px 12px 14px; border: 1.5px solid var(--line); border-radius: 12px; font-size: .95rem; font-family: inherit; background: #fff; color: var(--ink); transition: border-color .2s ease, box-shadow .2s ease; }
        .acc-pass input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(177, 55, 47, .12); }
        .acc-eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: none; background: transparent; border-radius: 50%; cursor: pointer; font-size: 1rem; opacity: .6; }
        .acc-eye:hover { opacity: 1; background: rgba(0, 0, 0, .05); }

        .acc-btn { margin-top: 4px; padding: 14px 20px; border: none; border-radius: 999px; background: var(--accent); color: #fff; font-size: .95rem; font-weight: 800; font-family: inherit; cursor: pointer; box-shadow: 0 6px 16px rgba(177, 55, 47, .28); transition: transform .2s ease, box-shadow .2s ease; }
        .acc-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(177, 55, 47, .35); }

        .acc-alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 18px; font-size: .9rem; font-weight: 600; }
        .acc-alert.ok { background: #eef1ea; color: #2f6f4e; border: 1px solid #d3dcc9; max-width: 980px; }
        .acc-alert.err { background: #fbe9e6; color: #b1372f; border: 1px solid #f0c3ba; }

        @media (max-width: 860px) { .acc-grid { grid-template-columns: 1fr; } .acc-card { padding: 20px; } .acc-btn { width: 100%; min-height: 46px; } }
    </style>
@endsection
