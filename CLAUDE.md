# Site de l'Ambassade du Congo au Kenya

Site public bilingue (`/fr`, `/en`) et back-office (`/admin`) de l'Ambassade de la République du Congo à Nairobi. Laravel 13, Livewire 4, Tailwind 4, Trix, icônes Phosphor, tests Pest (SQLite en mémoire). Réécriture de l'ancien site PHP (`../presentatio`), calquée sur le Dashboard YaQo.

- Architecture, tables, droits, langues : `docs/architecture.md`.
- Déploiement (GitLab CI → cPanel par SSH) : `docs/deploiement.md`.
- Champs traduits = colonnes `*_fr` / `*_en`, lus avec `$model->t('champ')` ; règles de validation avec `Translated::rules()`.
- Réglages uniques (coordonnées, accueil, ambassadeur, contacts) : `App\Support\SettingGroups` + `Setting::get()`.
- Composants de l'admin : `resources/views/components/admin/*` (`<x-admin.translated>`, `<x-admin.image-field>`…) et ceux repris de YaQo (`<x-button>`, `<x-status-badge>`…).
- Textes, commentaires et messages d'erreur en français ; textes du site public dans `lang/{fr,en}/site.php`.
- `php artisan test` avant de livrer ; `php artisan migrate:fresh --seed` pour repartir de zéro, puis `db:seed --class=DemoSeeder` pour un site rempli (photos dans `database/seeders/demo`), `php artisan congo:import-legacy` pour reprendre l'ancien site.
