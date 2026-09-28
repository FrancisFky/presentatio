# Site de l'Ambassade — architecture et modèle de données

Site officiel de l'Ambassade de la République du Congo au Kenya (Nairobi) :
un site public bilingue (français par défaut, anglais) et un back-office pour
le personnel. Réécriture en Laravel de l'ancien site en PHP natif
(`../presentatio`), sur le modèle du Dashboard YaQo (`~/Work/Maarifa/YAQO`).

| Partie | Adresse | Technique |
|---|---|---|
| Site public | `/fr/…`, `/en/…` | Blade + Tailwind 4, formulaires en Livewire 4 |
| Back-office | `/admin/…` | Contrôleurs classiques + Blade, Alpine (fourni par Livewire) |

## Langues

- Les contenus traduits sont stockés en **colonnes jumelles** : `title_fr`,
  `title_en`. `$model->t('title')` (trait `HasTranslations`) renvoie la langue
  en cours, sinon l'autre : une page pas encore traduite reste lisible.
- Le français est obligatoire dans l'admin, l'anglais facultatif
  (`App\Support\Translated::rules()`). La liste des langues est dans
  `App\Support\Locales` : en ajouter une (portugais…) = une colonne par champ.
- Site public : la langue est dans l'URL (`/fr`, `/en`), posée par le
  middleware `SetLocale` ; `route()` la reprend d'office. `/` redirige selon la
  langue du navigateur. Textes de l'interface : `lang/{fr,en}/site.php`.
- L'admin est en français seulement.

## Tables

### Contenu (un statut `draft` / `published` / `archived`, trait `HasPublicationStatus`)
- **pages** — pages fixes du menu « À propos » (`about-congo`, `about-embassy`,
  `invest-in-congo`) : titre, texte mis en forme, photo d'en-tête.
- **news** / **news_categories** — actualités : `slug` (adresse), rubrique,
  titre, chapeau, texte, photo, PDF joint, auteur, `published_on` (une date
  future programme l'article : `scopeVisible`).
- **announcements** — communiqués : priorité (`normal`, `important`, `urgent`),
  épinglage sur l'accueil, `expires_on` (retiré du site le lendemain).
- **events** — événements : date, heure, lieu, organisateur, lien d'inscription.
- **services** — services consulaires : présentation, conditions, pièces,
  frais, délai, heures de dépôt, icône Phosphor, ordre. Proposés dans le
  formulaire de rendez-vous.
- **documents** — formulaires à télécharger ; le téléchargement passe par le
  site pour compter (`download_count`).
- **holidays** — jours de fermeture, affichés et refusés dans le formulaire de
  rendez-vous.
- **albums** / **photos** — galerie ; `is_featured` met une photo sur l'accueil.

### Demandes reçues
- **appointments** — rendez-vous : `reference` (`RDV-7K2Q9M`, sans 0/O ni 1/I),
  service (ou `service_label` s'il a disparu), date et heure souhaitées,
  `locale` du demandeur (ses e-mails partent dans sa langue), `status`
  (`pending`, `confirmed`, `rescheduled`, `declined`, `done`), notes internes.
- **messages** — formulaire de contact (`unread`, `read`, `archived`).

### Réglages
- **settings** — clé/valeur (`site.address`, `home.hero_title_fr`…) pour tout
  ce qui n'existe qu'en un exemplaire : coordonnées, textes de l'accueil, mot de
  l'ambassadeur, contacts d'urgence. Les groupes et leurs champs sont déclarés
  dans `App\Support\SettingGroups`, qui génère aussi les pages de l'admin. La
  table entière est lue une fois et mise en cache (`Setting::get()`).

### Comptes
- **admins** — repris de YaQo : rôle `super_admin`, `admin` ou `member`
  (affiché « Éditeur »), statut 0 jamais activé / 1 actif / 2 désactivé,
  activation par lien e-mail, code à 6 chiffres à chaque connexion.
- **admin_audit_logs** — chaque action qui modifie (qui, quoi, sur quoi, d'où).

## Droits

| | Éditeur | Administrateur | Super admin |
|---|---|---|---|
| Créer / modifier le contenu, traiter rendez-vous et messages | ✓ | ✓ | ✓ |
| Accueil, ambassadeur, contacts d'urgence | ✓ | ✓ | ✓ |
| Supprimer (middleware `admin.sensitive`) | — | ✓ | ✓ |
| Paramètres du site (coordonnées, réseaux, référencement) | — | ✓ | ✓ |
| Gérer les comptes, journal des actions | — | éditeurs | admins et éditeurs |

Un test (`PermissionsTest`) vérifie que toute route `DELETE` de l'admin est
protégée ; seule exception voulue : retirer une photo de la galerie.

## Sécurité

- Connexion : mot de passe, puis code envoyé par e-mail (valable 10 min,
  5 essais), vérifié **par session**. 5 mauvais mots de passe bloquent le
  compte 15 min, quelle que soit l'adresse IP (`LoginAttempts`).
- Texte mis en forme (éditeur Trix) : nettoyé à l'enregistrement par
  `App\Support\RichText` (liste blanche de balises, liens http(s)/mailto/tel).
- Photos : réencodées en WebP par Intervention (`App\Services\Images`), ce
  qui neutralise tout fichier déguisé. Documents : extensions en liste blanche.
- Formulaires publics : champ piège anti-robots et limite de débit par IP.
- Pas de compte par défaut : le premier compte vient de `.env`.

## Reprise de l'ancien site

`php artisan congo:import-legacy` lit l'ancienne base (connexion `legacy`,
variables `LEGACY_DB_*`) et copie son dossier `uploads/` sous
`storage/app/public/imported/`. Détails dans la commande
(`app/Console/Commands/ImportLegacy.php`) : règle des langues, valeurs
d'exemple ignorées, ancien compte `admin`/`password` importé désactivé, code
Google Analytics non repris, liens `javascript:` rejetés.
