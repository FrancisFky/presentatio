# Déploiement du site de l'Ambassade

Le site est testé et mis en production par GitLab CI/CD (`.gitlab-ci.yml`), selon
la même chaîne que le Dashboard YaQo : un hébergement cPanel joint en SSH.
L'hébergeur de l'ambassade n'étant pas encore choisi, les exemples ci-dessous
sont ceux de Namecheap (port SSH 21098) : à adapter.

| Job | Rôle | Quand |
|---|---|---|
| `assets` | compile le CSS et le JS (Vite) | à chaque push |
| `tests` | lance toute la suite de tests (PHP 8.4, SQLite en mémoire) | à chaque push |
| `deploy` | envoie le code sur Namecheap par SSH et le met en service (`scripts/deploy.sh`) | sur `main`, si les tests passent |

Une branche autre que `main` ne part jamais en production : on peut y pousser
pour vérifier que les tests passent.

## Ce que fait le déploiement

1. Vérifie que le PHP du serveur est bien la version du CI (8.4).
2. Passe le site en maintenance (`artisan down`).
3. Envoie le code par `rsync` dans `public_html` :
   - les dossiers du projet (`app`, `bootstrap`, `config`, `database`, `resources`,
     `routes`, `vendor`, `public/build`) sont recopiés à l'identique ;
   - le reste de `public/` et les fichiers de la racine (`artisan`, `composer.*`)
     sont envoyés sans rien supprimer ;
   - **jamais touchés** : `.env`, `storage/` (photos, documents, logs, sessions),
     `public/storage`, et tout ce que cPanel met à la racine de `public_html`
     (`.htaccess`, `.well-known`, `cgi-bin`…).
4. Lance les migrations (`migrate --force`), reconstruit les caches (`optimize`),
   crée `public/storage` s'il manque.
5. Remet le site en ligne, puis vérifie que `/up` répond et que `.env`, les logs,
   `composer.lock` et `vendor/` **ne sont pas lisibles depuis internet**. Si l'un
   d'eux l'est, le job échoue.

En cas d'échec en cours de route, le site est remis en ligne et le job apparaît
en rouge dans GitLab.

## Mise en place (une seule fois)

### 1. Clé SSH de déploiement

Sur ton ordinateur :

```bash
ssh-keygen -t ed25519 -C "gitlab-deploy-ambassade" -f ~/.ssh/ambassade_deploy -N ""
```

- Dans cPanel → **SSH Access** → **Manage SSH Keys** → **Import Key** : colle le
  contenu de `~/.ssh/ambassade_deploy.pub`, puis **Authorize**.
- La clé privée (`~/.ssh/ambassade_deploy`) va dans GitLab (étape 2). Elle ne sert
  qu'au déploiement : ne la réutilise pas ailleurs.

Empreinte du serveur (à mettre dans `SSH_KNOWN_HOSTS`) :

```bash
ssh-keyscan -p 21098 serveur.namecheap.com
```

(remplace `serveur.namecheap.com` par le serveur indiqué dans cPanel, rubrique
« Shared IP Address » ou « Server Name »).

### 2. Variables GitLab

GitLab → **Settings → CI/CD → Variables**. Coche **Protect variable** (elles ne
servent qu'à `main`) et **Mask** quand c'est possible.

| Variable | Valeur | Type |
|---|---|---|
| `SSH_PRIVATE_KEY` | contenu de `~/.ssh/ambassade_deploy` | **File** |
| `SSH_KNOWN_HOSTS` | sortie de `ssh-keyscan` | Variable |
| `DEPLOY_HOST` | serveur SSH Namecheap | Variable |
| `DEPLOY_USER` | utilisateur cPanel | Variable |
| `DEPLOY_PORT` | `21098` (port SSH de Namecheap) | Variable |
| `DEPLOY_PATH` | `public_html` | Variable |
| `REMOTE_PHP` | `php` (ou le chemin du PHP 8.4, ex. `/opt/alt/php84/usr/bin/php`) | Variable |
| `APP_URL` | adresse publique du site, ex. `https://www.ambassade-congo.ke` | Variable |

La branche `main` doit être **protégée** (Settings → Repository → Protected
branches) pour avoir accès aux variables protégées.

### 3. Tâches planifiées

Aucune : le site n'a ni file d'attente ni tâche planifiée. Les e-mails
(codes de connexion, rendez-vous) partent pendant la requête.

### Sauvegardes

Sauvegarde de la base avant toute opération risquée (et régulièrement) :

```bash
mysqldump --single-transaction -u UTILISATEUR -p BASE > ~/sauvegardes/ambassade-$(date +%Y%m%d).sql
```

### 4. Le `.htaccess` de la racine de `public_html`

Le projet entier est dans `public_html` : c'est ce `.htaccess` qui envoie les
visiteurs vers `public/` et empêche de lire `.env`, `storage/`, `vendor/`… Il
n'est pas dans le dépôt (propre au serveur) et le déploiement n'y touche pas.
Il doit ressembler à ceci :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Fichiers cachés (.env, .git…) : jamais servis, sauf .well-known (certificats)
    RewriteRule (^|/)\.(?!well-known/) - [F,L]

    # Tout passe par public/, sauf .well-known (certificat HTTPS de cPanel)
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteCond %{REQUEST_URI} !^/\.well-known/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Au cas où mod_rewrite ne s'appliquerait pas
<FilesMatch "^\.env|composer\.(json|lock)$|artisan$">
    Require all denied
</FilesMatch>
```

Le déploiement vérifie ces protections à chaque fois.

## Déployer à la main

En cas de besoin (GitLab indisponible) : GitLab → **Build → Pipelines** →
**Run pipeline** sur `main` relance tout ; ou, depuis un poste avec la clé et les
mêmes variables exportées :

```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build && bash scripts/deploy.sh
```

## Première mise en ligne

Sur le serveur, dans `public_html`, une fois le premier déploiement passé :

1. Créer `.env` à partir de `.env.example` : `APP_ENV=production`,
   `APP_DEBUG=false`, `APP_URL`, `SESSION_SECURE_COOKIE=true`, la base MySQL, un **vrai serveur SMTP** (sans
   e-mail, personne ne peut se connecter : le code de vérification part par
   e-mail), `SUPER_ADMIN_EMAIL` et `SUPER_ADMIN_PASSWORD`, puis
   `php artisan key:generate`.
2. `php artisan migrate --force && php artisan db:seed --force` : premier compte,
   pages « À propos », rubriques et albums de base.
3. Reprise de l'ancien site (facultatif) : copier son dossier `uploads/` sur le
   serveur, renseigner `LEGACY_DB_*` et `LEGACY_UPLOADS_PATH` dans `.env`, puis
   `php artisan congo:import-legacy`. La commande affiche ce qu'elle a repris et
   ce qu'elle a laissé de côté.
4. Taille des envois : PHP limite souvent les fichiers à 2 Mo, alors que l'admin
   accepte des photos de 8 Mo et des PDF de 20 Mo (au-delà de la limite de PHP,
   le message « n'a pas pu être envoyé » s'affiche). Dans cPanel → **MultiPHP INI
   Editor** : `upload_max_filesize = 25M`, `post_max_size = 100M`,
   `memory_limit = 256M`.
5. Retirer `SUPER_ADMIN_PASSWORD` de `.env` et changer ce mot de passe depuis
   « Mon profil ».
