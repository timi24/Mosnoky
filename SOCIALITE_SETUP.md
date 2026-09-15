# Social Authentication Setup Guide

L'authentification sociale (Google et Facebook) est maintenant intégrée à votre application!

## Configuration Requise

Vous devez ajouter les variables d'environnement suivantes à votre fichier `.env`:

```env
# Google OAuth
GOOGLE_CLIENT_ID=votre_google_client_id
GOOGLE_CLIENT_SECRET=votre_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=votre_facebook_client_id
FACEBOOK_CLIENT_SECRET=votre_facebook_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

## Configuration Google

1. Accédez à [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez un existant
3. Accédez à **APIs & Services** > **Credentials**
4. Créez une nouvelle **OAuth 2.0 Client ID** (type: Web Application)
5. Ajoutez les URL autorisées:
   - **Authorized JavaScript origins**: `http://localhost:8000` (et vos domaines en production)
   - **Authorized redirect URIs**: `http://localhost:8000/auth/google/callback`
6. Copiez le **Client ID** et **Client Secret** dans votre `.env`

## Configuration Facebook

1. Accédez à [Facebook Developers](https://developers.facebook.com/)
2. Créez une nouvelle application ou sélectionnez une existante
3. Accédez à **Settings** > **Basic**
4. Notez votre **App ID** et **App Secret**
5. Allez à **Facebook Login** > **Settings**
6. Sous **Valid OAuth Redirect URIs**, ajoutez: `http://localhost:8000/auth/facebook/callback`
7. Copiez l'**App ID** (Client ID) et **App Secret** (Client Secret) dans votre `.env`

## Fichiers Modifiés

- ✅ `config/services.php` - Configuration des providers OAuth
- ✅ `app/Http/Controllers/Auth/SocialiteController.php` - Contrôleur pour gérer les callbacks
- ✅ `routes/web.php` - Routes OAuth (redirect et callback)
- ✅ `resources/views/pages/auth/login.blade.php` - Boutons Google/Facebook
- ✅ `resources/views/pages/auth/register.blade.php` - Boutons Google/Facebook

## Fonctionnement

1. L'utilisateur clique sur le bouton Google ou Facebook
2. Il est redirigé vers le service OAuth
3. Après authentification, il est redirigé vers le callback
4. Si l'utilisateur existe (par email), il est connecté
5. Si l'utilisateur n'existe pas, un nouveau compte est créé automatiquement
6. L'utilisateur est ensuite redirigé vers son dashboard

## Notes de Sécurité

- En production, remplacez `http://localhost:8000` par votre domaine réel
- Gardez vos `client_id` et `client_secret` secrets (ne les commitez jamais dans Git)
- Utilisez des variables d'environnement pour tous les secrets
- Vérifiez que vos URLs de callback correspondent exactement entre `.env` et les configurations OAuth

## Test Local

Pour tester en développement:
1. Assurez-vous que votre URL est `http://localhost:8000` (ou votre domaine local)
2. Mettez à jour les **Redirect URIs** dans Google Cloud Console et Facebook Developers
3. Les boutons sociaux devraient maintenant fonctionner

## Améliorations Futures

- Ajouter d'autres providers (GitHub, LinkedIn, Discord, etc.)
- Stocker les tokens sociaux pour les actions futures
- Permettre la liaison de comptes existants avec les comptes sociaux
- Ajouter des scopes personnalisés pour les données utilisateur
