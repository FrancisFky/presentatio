<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Message;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Photo;
use App\Models\Service;
use App\Models\Setting;
use App\Support\RichText;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Reprise du site PHP d'origine : sa base MySQL (connexion « legacy ») et son
 * dossier uploads/. Les fichiers sont copiés sur le disque public sous imported/.
 *
 * Règle des langues : l'ancien site avait des colonnes `_fr`/`_en` et une
 * colonne sans suffixe plus ancienne. Le français reprend `_fr`, sinon la
 * colonne sans suffixe ; l'anglais reprend `_en`. Le site affichant l'autre
 * langue quand une traduction manque, rien ne disparaît.
 */
class ImportLegacy extends Command
{
    protected $signature = 'congo:import-legacy
        {--uploads= : Dossier du site PHP (celui qui contient uploads/), par défaut LEGACY_UPLOADS_PATH}
        {--fresh : Vide d\'abord le contenu déjà présent (actualités, services, rendez-vous…)}';

    protected $description = "Importe la base et les fichiers de l'ancien site PHP";

    /** Valeurs d'exemple de l'ancien schema.sql : inutile de les reprendre */
    const LEGACY_DEFAULTS = [
        'Embassy of the Republic of Congo in Kenya',
        'Monday - Friday: 09:00 - 17:00',
        'Official embassy website for information, services, and public notices.',
        'Official embassy website for news, services, announcements, and public information.',
        'Welcome to the Embassy of the Republic of Congo in Kenya',
        'Serving citizens, partners, and the diplomatic community with professionalism and care.',
        'The embassy is committed to strengthening bilateral ties and serving the Congolese community in Kenya.',
        'To represent the Republic of Congo with dignity, professionalism, and dedication.',
        'To build strong partnerships and provide trusted consular and diplomatic support.',
        'To support citizens, strengthen diplomacy, and promote cooperation across all sectors.',
        'Ambassador of the Republic of Congo to Kenya.',
        'It is my pleasure to serve the people and strengthen relations between our nations.',
        '<p>Ce contenu est un espace réservé éditable depuis le CMS.</p>',
        '<p>This is editable placeholder content managed in the CMS.</p>',
    ];

    private Connection $legacy;
    private string $uploadsRoot;
    private array $counts = [];
    private array $warnings = [];

    public function handle(): int
    {
        try {
            $this->legacy = DB::connection('legacy');
            $this->legacy->getPdo();
        } catch (\Throwable $e) {
            $this->error("Connexion à l'ancienne base impossible (LEGACY_DB_* dans .env) : " . $e->getMessage());

            return self::FAILURE;
        }

        $this->uploadsRoot = rtrim((string) ($this->option('uploads') ?: env('LEGACY_UPLOADS_PATH', '')), '/');
        if ($this->uploadsRoot !== '' && !str_starts_with($this->uploadsRoot, '/')) {
            $this->uploadsRoot = base_path($this->uploadsRoot);
        }
        if (!is_dir($this->uploadsRoot . '/uploads')) {
            $this->warn("Dossier uploads/ introuvable dans « {$this->uploadsRoot} » : les fichiers ne seront pas copiés.");
        }

        if (News::exists() || Service::exists() || Appointment::exists()) {
            if (!$this->option('fresh')) {
                $this->error('Du contenu existe déjà. Relancez avec --fresh pour le remplacer par celui de l\'ancien site.');

                return self::FAILURE;
            }
            if (!$this->confirm('Tout le contenu actuel (hors comptes et réglages) va être remplacé. Continuer ?', true)) {
                return self::FAILURE;
            }
            $this->wipeContent();
        }

        DB::transaction(function () {
            $this->importAdmins();
            $this->importSettings();
            $this->importPages();
            $categories = $this->importNewsCategories();
            $this->importNews($categories);
            $this->importAnnouncements();
            $this->importEvents();
            $services = $this->importServices();
            $this->importDocuments();
            $this->importHolidays();
            $this->importGallery();
            $this->importAppointments($services);
            $this->importMessages();
        });

        Setting::flush();

        $this->newLine();
        $this->table(['Contenu', 'Importés'], collect($this->counts)->map(fn ($count, $label) => [$label, $count])->values()->all());
        foreach (array_unique($this->warnings) as $warning) {
            $this->warn('• ' . $warning);
        }
        $this->info('Import terminé.');

        return self::SUCCESS;
    }

    // ---------- Comptes ----------

    private function importAdmins(): void
    {
        foreach ($this->rows('users') as $user) {
            if (Admin::withTrashed()->where('email', $user->email)->exists()) {
                continue;
            }

            // L'ancien compte par défaut (admin / password) n'est pas repris actif
            $defaultPassword = password_verify('password', (string) $user->password_hash);

            $admin = Admin::create([
                'slug' => (string) Str::orderedUuid(),
                'name' => $user->full_name,
                'username' => $this->uniqueUsername($user->username),
                'email' => $user->email,
                'password' => Str::random(40),
                'role' => match ($user->role) {
                    'Super Administrator' => Admin::ROLE_SUPER_ADMIN,
                    'Administrator' => Admin::ROLE_ADMIN,
                    default => Admin::ROLE_MEMBER,
                },
                'status' => $user->status === 'active' && !$defaultPassword ? Admin::STATUS_ACTIVE : Admin::STATUS_DEACTIVATED,
            ]);

            // Le hash bcrypt de l'ancien site reste valable ; écrit tel quel (le cast
            // « hashed » refuserait un coût différent). Laravel le recalcule à la connexion.
            DB::table('admins')->where('id', $admin->id)->update(['password' => $user->password_hash]);

            if ($defaultPassword) {
                $this->warnings[] = "Compte {$user->email} : mot de passe par défaut « password », importé désactivé.";
            }
            $this->count('Comptes');
        }
    }

    private function uniqueUsername(string $username): string
    {
        $candidate = Str::limit(Str::slug($username, '_') ?: 'agent', 40, '');
        while (Admin::withTrashed()->where('username', $candidate)->exists()) {
            $candidate .= '_' . random_int(0, 9);
        }

        return $candidate;
    }

    // ---------- Réglages (lignes uniques) ----------

    private function importSettings(): void
    {
        if ($site = $this->latest('website_settings')) {
            $this->setting('site.name_en', $site->embassy_name ?? null);
            $this->setting('site.address', $site->address ?? null);
            $this->setting('site.phone', $site->phone_numbers ?? null);
            $this->setting('site.email', $site->emails ?? null);
            $this->setting('site.map_url', $this->url($site->google_maps ?? null));
            $this->setting('site.working_hours_en', $site->working_hours ?? null);
            $this->setting('site.facebook', $this->url($site->facebook ?? null));
            $this->setting('site.instagram', $this->url($site->instagram ?? null));
            $this->setting('site.x', $this->url($site->twitter ?? null));
            $this->setting('site.linkedin', $this->url($site->linkedin ?? null));
            $this->setting('site.footer_en', $site->footer_info ?? null);
            $this->setting('site.seo_description_en', $site->seo_description ?? null);
            $this->setting('site.logo', $this->copy($site->logo_path ?? null));
            if (filled($site->google_analytics_code ?? null)) {
                $this->warnings[] = "Code Google Analytics non repris (du script brut) : à réintégrer proprement s'il est encore utile.";
            }
        }

        if ($home = $this->latest('homepage_content')) {
            foreach (['hero_title' => 'hero_title', 'hero_subtitle' => 'hero_subtitle', 'welcome_message' => 'welcome', 'mission' => 'mission', 'vision' => 'vision', 'objectives' => 'objectives'] as $old => $new) {
                $this->setting("home.{$new}_fr", $this->fr($home, $old));
                $this->setting("home.{$new}_en", $this->en($home, $old));
            }
            $this->setting('home.hero_image', $this->copy($home->hero_image ?? null));
        }

        if ($ambassador = $this->legacy->table('ambassador')->orderByDesc('published')->orderByDesc('id')->first()) {
            $this->setting('ambassador.name', $this->fr($ambassador, 'name'));
            $this->setting('ambassador.position_fr', $this->fr($ambassador, 'position'));
            $this->setting('ambassador.position_en', $this->en($ambassador, 'position'));
            $this->setting('ambassador.message_fr', $this->fr($ambassador, 'welcome_message'));
            $this->setting('ambassador.message_en', $this->en($ambassador, 'welcome_message'));
            $this->setting('ambassador.biography_fr', $this->rich($this->fr($ambassador, 'biography')));
            $this->setting('ambassador.biography_en', $this->rich($this->en($ambassador, 'biography')));
            $this->setting('ambassador.signature', $this->fr($ambassador, 'signature'));
            $this->setting('ambassador.photo', $this->copy($ambassador->photo ?? null));
            Setting::set('ambassador.published', $ambassador->published ? '1' : '0');
        }

        if ($contacts = $this->latest('emergency_contacts')) {
            $this->setting('contacts.hotline', $contacts->hotline ?? null);
            $this->setting('contacts.whatsapp', $contacts->whatsapp ?? null);
            $this->setting('contacts.email', $contacts->embassy_email ?? null);
            $this->setting('contacts.duty_officer', $contacts->duty_officer ?? null);
            $this->setting('contacts.instructions_fr', $contacts->emergency_instructions ?? null);
            if (blank(Setting::get('site.map_url'))) {
                $this->setting('site.map_url', $this->url($contacts->google_maps ?? null));
            }
        }
    }

    /** Écrit un réglage s'il a une vraie valeur (pas vide, pas l'exemple de l'ancien schéma) */
    private function setting(string $key, ?string $value): void
    {
        $value = is_string($value) ? trim($value) : $value;
        if (blank($value) || in_array($value, self::LEGACY_DEFAULTS, true)) {
            return;
        }
        Setting::set($key, $value);
        $this->count('Réglages');
    }

    // ---------- Contenu ----------

    private function importPages(): void
    {
        foreach ($this->rows('about_pages') as $row) {
            Page::updateOrCreate(['slug' => $row->slug], [
                'title_fr' => $row->title_fr,
                'title_en' => $row->title_en,
                'body_fr' => $this->rich($row->content_fr),
                'body_en' => $this->rich($row->content_en),
                'image_path' => $this->copy($row->hero_image),
                'status' => $row->status === 'published' ? Page::STATUS_PUBLISHED : Page::STATUS_DRAFT,
            ]);
            $this->count('Pages');
        }
    }

    private function importNewsCategories(): array
    {
        $map = [];
        foreach ($this->rows('news_categories') as $row) {
            $map[$row->id] = NewsCategory::firstOrCreate(['name_en' => $row->name], ['name_fr' => $row->name])->id;
            $this->count('Rubriques');
        }

        return $map;
    }

    private function importNews(array $categories): void
    {
        foreach ($this->rows('news') as $row) {
            $news = new News([
                'news_category_id' => $categories[$row->category_id] ?? null,
                'title_fr' => $this->fr($row, 'title'),
                'title_en' => $this->en($row, 'title'),
                'excerpt_fr' => $this->fr($row, 'short_description'),
                'excerpt_en' => $this->en($row, 'short_description'),
                'body_fr' => $this->rich($this->fr($row, 'content')),
                'body_en' => $this->rich($this->en($row, 'content')),
                'image_path' => $this->copy($row->featured_image),
                'attachment_path' => $this->copy($row->pdf_attachment),
                'author' => $row->author,
                'published_on' => $row->publication_date,
                'status' => $this->status($row->status),
            ]);
            $news->slug = News::uniqueSlug($row->slug ?: ($news->title_fr ?? 'article'));
            $this->keepDates($news, $row)->save();
            $this->count('Actualités');
        }
    }

    private function importAnnouncements(): void
    {
        foreach ($this->rows('announcements') as $row) {
            $this->keepDates(new Announcement([
                'title_fr' => $this->fr($row, 'title'),
                'title_en' => $this->en($row, 'title'),
                'body_fr' => $this->rich($this->fr($row, 'description')),
                'body_en' => $this->rich($this->en($row, 'description')),
                'category' => $row->category,
                'priority' => match (Str::lower((string) $row->priority)) {
                    'urgent', 'critical' => 'urgent',
                    'high', 'important' => 'important',
                    default => 'normal',
                },
                'image_path' => $this->copy($row->image),
                'attachment_path' => $this->copy($row->pdf_attachment),
                'published_on' => $row->publish_date,
                'expires_on' => $row->expiry_date ?: null,
                'is_pinned' => (bool) $row->pin_to_homepage,
                'status' => $this->status($row->status),
            ]), $row)->save();
            $this->count('Communiqués');
        }
    }

    private function importEvents(): void
    {
        foreach ($this->rows('events') as $row) {
            $this->keepDates(new Event([
                'title_fr' => $this->fr($row, 'title'),
                'title_en' => $this->en($row, 'title'),
                'description_fr' => $this->rich($this->fr($row, 'description')),
                'description_en' => $this->rich($this->en($row, 'description')),
                'venue_fr' => $this->fr($row, 'venue'),
                'venue_en' => $this->en($row, 'venue'),
                'starts_on' => $row->event_date,
                'starts_at' => $row->event_time ? substr($row->event_time, 0, 5) : null,
                'organizer' => $row->organizer,
                'speaker' => $row->speaker,
                'image_path' => $this->copy($row->cover_image),
                'registration_url' => $this->url($row->registration_link),
                'status' => $this->status($row->status),
            ]), $row)->save();
            $this->count('Événements');
        }
    }

    /** @return array<string, int> titre en minuscules → id, pour relier les anciens rendez-vous */
    private function importServices(): array
    {
        $map = [];
        $position = 0;
        foreach ($this->legacy->table('services')->orderBy('id')->get() as $row) {
            $documentsFr = $this->rich($this->fr($row, 'required_documents'));
            $forms = $this->fr($row, 'download_forms');
            if (filled($forms)) {
                // L'ancien champ « formulaires à télécharger » rejoint les pièces à fournir
                $documentsFr = trim(($documentsFr ?? '') . $this->rich(Str::contains($forms, '<') ? $forms : '<p>' . e($forms) . '</p>'));
            }

            $service = $this->keepDates(new Service([
                'title_fr' => $this->fr($row, 'title'),
                'title_en' => $this->en($row, 'title'),
                'description_fr' => $this->rich($this->fr($row, 'description')),
                'description_en' => $this->rich($this->en($row, 'description')),
                'requirements_fr' => $this->rich($this->fr($row, 'requirements')),
                'requirements_en' => $this->rich($this->en($row, 'requirements')),
                'documents_fr' => $documentsFr ?: null,
                'documents_en' => $this->rich($this->en($row, 'required_documents')),
                'fees_fr' => $this->rich($this->fr($row, 'fees')),
                'fees_en' => $this->rich($this->en($row, 'fees')),
                'processing_time_fr' => $this->fr($row, 'processing_time'),
                'processing_time_en' => $this->en($row, 'processing_time'),
                'office_hours_fr' => $this->fr($row, 'office_hours'),
                'office_hours_en' => $this->en($row, 'office_hours'),
                'icon' => 'file-text',
                'position' => ++$position,
                'status' => $this->status($row->status),
            ]), $row);
            $service->save();

            foreach ([$service->title_fr, $service->title_en, $row->title ?? null] as $title) {
                if (filled($title)) {
                    $map[Str::lower(trim($title))] = $service->id;
                }
            }
            $this->count('Services');
        }

        return $map;
    }

    private function importDocuments(): void
    {
        foreach ($this->rows('downloads') as $row) {
            $path = $this->copy($row->file_path);
            if (!$path) {
                $this->warnings[] = "Document « {$row->title} » sans fichier : non importé.";

                continue;
            }
            $this->keepDates(new Document([
                'title_fr' => $row->title,
                'category' => $row->category,
                'file_path' => $path,
                'file_name' => Str::slug($row->title) . '.' . pathinfo($path, PATHINFO_EXTENSION),
                'file_size' => Storage::disk('public')->size($path),
                'download_count' => (int) $row->download_count,
                'status' => $this->status($row->status),
            ]), $row)->save();
            $this->count('Documents');
        }
    }

    private function importHolidays(): void
    {
        foreach ($this->rows('holidays') as $row) {
            $this->keepDates(new Holiday([
                'name_fr' => $row->holiday_name,
                'description_fr' => $row->description,
                'date' => $row->holiday_date,
                'status' => $this->status($row->status),
            ]), $row)->save();
            $this->count('Jours fériés');
        }
    }

    private function importGallery(): void
    {
        $albums = [];
        foreach ($this->rows('gallery_albums') as $row) {
            $albums[$row->id] = Album::firstOrCreate(['name_en' => $row->name], ['name_fr' => $row->name, 'description_fr' => $row->description])->id;
        }

        foreach ($this->rows('gallery') as $row) {
            $path = $this->copy($row->image_path);
            if (!$path) {
                continue;
            }
            $this->keepDates(new Photo([
                'album_id' => $albums[$row->album_id] ?? null,
                'title_fr' => $row->title,
                'caption_fr' => $row->caption ?: $row->alt_text,
                'image_path' => $path,
                'is_featured' => (bool) $row->featured,
            ]), $row)->save();
            $this->count('Photos');
        }
    }

    // ---------- Demandes reçues ----------

    private function importAppointments(array $services): void
    {
        foreach ($this->rows('appointments') as $row) {
            $serviceId = $services[Str::lower(trim((string) $row->requested_service))] ?? null;
            $this->keepDates(new Appointment([
                'name' => $row->applicant_name,
                'email' => $row->email,
                'phone' => $row->phone,
                'nationality' => $row->nationality,
                'service_id' => $serviceId,
                'service_label' => $serviceId ? null : $row->requested_service,
                'preferred_date' => $row->preferred_date,
                'preferred_time' => $this->time($row->preferred_time),
                'status' => match ($row->status) {
                    'approved' => Appointment::STATUS_CONFIRMED,
                    'rejected' => Appointment::STATUS_DECLINED,
                    'rescheduled' => Appointment::STATUS_RESCHEDULED,
                    default => Appointment::STATUS_PENDING,
                },
                'locale' => 'fr',
            ]), $row)->save();
            $this->count('Rendez-vous');
        }
    }

    private function importMessages(): void
    {
        foreach ($this->rows('contact_messages') as $row) {
            $this->keepDates(new Message([
                'name' => $row->sender_name,
                'email' => $row->email,
                'phone' => $row->phone ?: null,
                'subject' => $row->subject,
                'body' => $row->message,
                'status' => in_array($row->status, ['unread', 'read', 'archived'], true) ? $row->status : Message::STATUS_READ,
            ]), $row)->save();
            $this->count('Messages');
        }
    }

    // ---------- Outils ----------

    private function wipeContent(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['appointments', 'messages', 'photos', 'albums', 'news', 'news_categories', 'announcements', 'events', 'services', 'documents', 'holidays'] as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }

    private function rows(string $table)
    {
        if (!$this->legacy->getSchemaBuilder()->hasTable($table)) {
            $this->warnings[] = "Table « {$table} » absente de l'ancienne base.";

            return collect();
        }

        return $this->legacy->table($table)->orderBy('id')->get();
    }

    private function latest(string $table): ?object
    {
        return $this->legacy->getSchemaBuilder()->hasTable($table) ? $this->legacy->table($table)->orderByDesc('id')->first() : null;
    }

    /** Français : la colonne _fr, sinon l'ancienne colonne sans suffixe */
    private function fr(object $row, string $field): ?string
    {
        foreach (["{$field}_fr", $field] as $column) {
            $value = trim((string) ($row->{$column} ?? ''));
            if ($value !== '' && !in_array($value, self::LEGACY_DEFAULTS, true)) {
                return $value;
            }
        }

        return null;
    }

    private function en(object $row, string $field): ?string
    {
        $value = trim((string) ($row->{"{$field}_en"} ?? ''));

        return $value !== '' && !in_array($value, self::LEGACY_DEFAULTS, true) ? $value : null;
    }

    private function rich(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }
        // Texte brut de l'ancien site : les retours à la ligne deviennent des paragraphes
        if (!Str::contains($html, '<')) {
            $html = collect(preg_split("/\R{2,}/", $html))->map(fn ($p) => '<p>' . nl2br(e(trim($p))) . '</p>')->join('');
        }

        return RichText::clean($html);
    }

    private function status(?string $status): string
    {
        return in_array($status, ['draft', 'published', 'archived'], true) ? $status : 'draft';
    }

    private function url(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' && filter_var($value, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $value) ? $value : null;
    }

    private function time(?string $value): string
    {
        return preg_match('/(\d{1,2})[:h](\d{2})/', (string) $value, $m) ? sprintf('%02d:%s', $m[1], $m[2]) : '09:00';
    }

    /** Garde les dates de création de l'ancien site */
    private function keepDates($model, object $row)
    {
        if (!empty($row->created_at)) {
            $model->created_at = $row->created_at;
        }
        if (!empty($row->updated_at)) {
            $model->updated_at = $row->updated_at;
        }

        return $model;
    }

    /** Copie uploads/… vers le disque public (imported/…) ; null si absent */
    private function copy(?string $legacyPath): ?string
    {
        $legacyPath = ltrim(trim((string) $legacyPath), '/');
        if ($legacyPath === '' || Str::startsWith($legacyPath, ['http://', 'https://'])) {
            return null;
        }

        $source = $this->uploadsRoot . '/' . $legacyPath;
        if (!is_file($source)) {
            $this->warnings[] = "Fichier introuvable : {$legacyPath}";

            return null;
        }

        $target = 'imported/' . Str::after($legacyPath, 'uploads/');
        if (!Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
            $this->count('Fichiers copiés');
        }

        return $target;
    }

    private function count(string $label): void
    {
        $this->counts[$label] = ($this->counts[$label] ?? 0) + 1;
    }
}
