# Ambassade de la République du Congo au Kenya

Site officiel et back-office de l'Ambassade, en Laravel 13.

## Installation locale

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
# créer la base MySQL « congocms », renseigner SUPER_ADMIN_* dans .env
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder   # facultatif : contenu de démonstration
php artisan storage:link
php artisan serve
```

- Site : http://127.0.0.1:8000 (redirige vers `/fr` ou `/en`)
- Administration : http://127.0.0.1:8000/admin — en local, le code de
  connexion arrive dans `storage/logs/laravel.log` (`MAIL_MAILER=log`).

## Reprendre l'ancien site

```bash
php artisan congo:import-legacy
```

Lit la base `LEGACY_DB_DATABASE` et copie `LEGACY_UPLOADS_PATH/uploads`.

## Tests

```bash
php artisan test
```

Voir `docs/architecture.md` et `docs/deploiement.md`.
