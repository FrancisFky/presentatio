#!/usr/bin/env bash
#
# Met le site de l'Ambassade en production sur un hébergement cPanel (SSH).
# Repris tel quel du Dashboard YaQo, qui tourne ainsi chez Namecheap.
# Lancé par le job « deploy » de GitLab CI, après le build des assets et les
# tests, avec les dépendances de production déjà installées (vendor/).
#
# Le projet entier vit dans public_html, à côté de fichiers propres à cPanel
# (.htaccess de la racine, .well-known, cgi-bin…) : on ne supprime jamais rien
# à la racine, seulement à l'intérieur des dossiers du projet. .env et
# storage/ (fichiers envoyés, logs, sessions) restent ceux du serveur.
#
# Variables GitLab (Settings → CI/CD → Variables), voir docs/deploiement.md :
#   SSH_PRIVATE_KEY   clé privée de déploiement (type « File » ou texte)
#   SSH_KNOWN_HOSTS   empreinte du serveur (ssh-keyscan -p 21098 serveur)
#   DEPLOY_HOST       serveur SSH Namecheap
#   DEPLOY_USER       utilisateur cPanel
#   DEPLOY_PORT       port SSH (Namecheap : 21098)
#   DEPLOY_PATH       dossier du projet sur le serveur (public_html)
#   REMOTE_PHP        binaire PHP 8.4 sur le serveur (php par défaut)
#   APP_URL           adresse publique, pour les vérifications finales

set -euo pipefail

: "${SSH_PRIVATE_KEY:?Variable SSH_PRIVATE_KEY manquante (variables protégées : main doit être une branche protégée)}"
: "${SSH_KNOWN_HOSTS:?Variable SSH_KNOWN_HOSTS manquante}"
: "${DEPLOY_HOST:?Variable DEPLOY_HOST manquante}"
: "${DEPLOY_USER:?Variable DEPLOY_USER manquante}"
DEPLOY_PORT="${DEPLOY_PORT:-21098}"
DEPLOY_PATH="${DEPLOY_PATH:-public_html}"
REMOTE_PHP="${REMOTE_PHP:-php}"
# Pas de valeur par défaut : se tromper d'adresse fausserait les vérifications finales
: "${APP_URL:?Variable APP_URL manquante (adresse publique du site)}"

# ── Connexion SSH ────────────────────────────────────────────────────────────
mkdir -p ~/.ssh && chmod 700 ~/.ssh
# Variable GitLab de type File (chemin) ou texte ; dans les deux cas, fins de
# ligne Windows retirées et saut de ligne final garanti : sans lui, OpenSSH
# refuse la clé (« error in libcrypto »)
if [ -f "$SSH_PRIVATE_KEY" ]; then
  KEY_CONTENT=$(cat "$SSH_PRIVATE_KEY")
else
  KEY_CONTENT="$SSH_PRIVATE_KEY"
fi
printf '%s\n' "$KEY_CONTENT" | tr -d '\r' > ~/.ssh/deploy_key
chmod 600 ~/.ssh/deploy_key
unset KEY_CONTENT
ssh-keygen -y -f ~/.ssh/deploy_key > /dev/null \
  || { echo "✗ SSH_PRIVATE_KEY n'est pas une clé privée lisible (copie incomplète ?)." >&2; exit 1; }
printf '%s\n' "$SSH_KNOWN_HOSTS" | tr -d '\r' > ~/.ssh/known_hosts
chmod 644 ~/.ssh/known_hosts

# Une seule connexion SSH, partagée par toutes les commandes et par rsync :
# l'hébergeur bloque une adresse qui ouvre trop de connexions d'affilée, ce
# qui laisserait le site en maintenance au milieu du déploiement
SSH="ssh -i $HOME/.ssh/deploy_key -p $DEPLOY_PORT -o BatchMode=yes -o StrictHostKeyChecking=yes \
  -o ControlMaster=auto -o ControlPath=$HOME/.ssh/cm-%C -o ControlPersist=300 \
  -o ServerAliveInterval=15 -o ServerAliveCountMax=8"
REMOTE="$DEPLOY_USER@$DEPLOY_HOST"

remote() {
  $SSH "$REMOTE" "cd $DEPLOY_PATH && $*"
}

echo "→ Serveur : $REMOTE:$DEPLOY_PATH (port $DEPLOY_PORT)"
remote "$REMOTE_PHP -v | head -1"

# Le PHP du serveur doit être celui avec lequel vendor/ a été installé
REMOTE_VERSION=$(remote "$REMOTE_PHP -r 'echo PHP_MAJOR_VERSION.\".\".PHP_MINOR_VERSION;'")
LOCAL_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
if [ "$REMOTE_VERSION" != "$LOCAL_VERSION" ]; then
  echo "✗ PHP $REMOTE_VERSION sur le serveur, PHP $LOCAL_VERSION dans le CI : réglez REMOTE_PHP ou PHP_IMAGE." >&2
  exit 1
fi

# ── Mode maintenance ─────────────────────────────────────────────────────────
MAINTENANCE=0
bring_back_up() {
  if [ "$MAINTENANCE" = "1" ]; then
    echo "→ Remise en ligne"
    remote "$REMOTE_PHP artisan up" || true
  fi
  $SSH -O exit "$REMOTE" 2> /dev/null || true
}
trap bring_back_up EXIT

if remote "test -f artisan"; then
  remote "$REMOTE_PHP artisan down --retry=30 --refresh=15" && MAINTENANCE=1
fi

# ── Envoi des fichiers ───────────────────────────────────────────────────────
RSYNC="rsync -rlptz --no-perms --chmod=D755,F644 -e \"$SSH\""

# Dossiers du projet : copie exacte (--delete ne touche qu'à l'intérieur)
for dir in app bootstrap config database lang resources routes vendor public/build; do
  [ -d "$dir" ] || continue
  $SSH "$REMOTE" "mkdir -p $DEPLOY_PATH/$dir"
  eval "$RSYNC --delete \
    --exclude='/cache/*.php' \
    --exclude='.DS_Store' \
    \"$dir/\" \"$REMOTE:$DEPLOY_PATH/$dir/\""
done

# public/ : envoyé sans rien y supprimer (fichiers propres au serveur, lien storage)
eval "$RSYNC --exclude='/storage' --exclude='/hot' --exclude='/build' --exclude='.DS_Store' \
  public/ \"$REMOTE:$DEPLOY_PATH/public/\""

# Fichiers de la racine du projet : envoyés, jamais supprimés
eval "$RSYNC artisan composer.json composer.lock \"$REMOTE:$DEPLOY_PATH/\""

# ── Mise en service ──────────────────────────────────────────────────────────
remote "mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache"
# Restes d'un poste de développement : public/hot ferait charger les assets
# depuis le serveur Vite local, et un ancien cache de paquets désigne des
# paquets de développement absents de vendor/ (artisan ne démarre plus)
remote "rm -f public/hot bootstrap/cache/packages.php bootstrap/cache/services.php"
remote "test -f .env" || { echo "✗ Pas de .env sur le serveur dans $DEPLOY_PATH" >&2; exit 1; }

remote "$REMOTE_PHP artisan migrate --force"
remote "$REMOTE_PHP artisan optimize:clear > /dev/null && $REMOTE_PHP artisan optimize"
remote "test -e public/storage || $REMOTE_PHP artisan storage:link"

remote "$REMOTE_PHP -r 'exit(function_exists(\"imagewebp\") ? 0 : 1);'" \
  || echo "⚠ GD sans WebP sur le serveur : l'envoi du logo et de la devanture échouera."

remote "$REMOTE_PHP artisan up"
MAINTENANCE=0

# ── Vérifications ────────────────────────────────────────────────────────────
status() { curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$APP_URL$1"; }

# Tout est dans public_html : .env, les logs et vendor ne doivent jamais être lisibles
for path in /.env /storage/logs/laravel.log /composer.lock /vendor/composer/installed.json; do
  CODE=$(status "$path")
  if [ "$CODE" = "200" ]; then
    echo "✗ $APP_URL$path est lisible depuis internet ! Corrigez le .htaccess de la racine (docs/deploiement.md)." >&2
    exit 1
  fi
  echo "→ $path protégé ($CODE)"
done

UP=$(status /up)
echo "→ $APP_URL/up : $UP"
[ "$UP" = "200" ] || { echo "✗ Le site ne répond pas correctement après le déploiement." >&2; exit 1; }

echo "✓ Site en production : $APP_URL"
