<?php

namespace Database\Seeders;

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
use App\Services\Images;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Contenu de démonstration : de quoi voir le site rempli (articles,
 * communiqués, événements, services, documents, galerie…).
 *
 *   php artisan migrate:fresh --seed && php artisan db:seed --class=DemoSeeder
 *
 * Les textes sont inventés : à ne jamais lancer en production.
 */
class DemoSeeder extends Seeder
{
    /** Photos de démonstration (database/seeders/demo) et du site (public/images/site) */
    private array $stored = [];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoSeeder refusé en production.');

            return;
        }

        $this->settings();
        $this->pages();
        $services = $this->services();
        $this->news();
        $this->announcements();
        $this->events();
        $this->documents();
        $this->holidays();
        $this->gallery();
        $this->requests($services);

        Setting::flush();
        $this->command?->info('Contenu de démonstration ajouté.');
    }

    // ---------- Réglages ----------

    private function settings(): void
    {
        $values = [
            'home.hero_title_fr' => "Bienvenue à l'Ambassade de la République du Congo au Kenya",
            'home.hero_title_en' => 'Welcome to the Embassy of the Republic of the Congo in Kenya',
            'home.hero_subtitle_fr' => "Au service des Congolais du Kenya, de l'Ouganda et de la Tanzanie, et de tous ceux qui souhaitent découvrir, visiter ou investir au Congo.",
            'home.hero_subtitle_en' => 'Serving Congolese nationals in Kenya, Uganda and Tanzania, and everyone who wishes to discover, visit or invest in the Congo.',
            'home.hero_image' => $this->image('site:ambassade-2.jpg', 'settings', 2400),
            'home.welcome_fr' => "L'Ambassade accompagne la communauté congolaise dans ses démarches consulaires, fait connaître le Congo et renforce chaque jour l'amitié entre Brazzaville et Nairobi.",
            'home.welcome_en' => 'The Embassy supports the Congolese community with consular services, promotes the Congo and strengthens the friendship between Brazzaville and Nairobi every day.',
            'home.mission_fr' => 'Représenter la République du Congo auprès du Kenya et des organisations internationales établies à Nairobi, protéger ses ressortissants et défendre ses intérêts.',
            'home.mission_en' => 'Represent the Republic of the Congo in Kenya and to the international organisations based in Nairobi, protect its nationals and defend its interests.',
            'home.vision_fr' => "Un partenariat Congo–Kenya fort, au service des deux peuples, et une diaspora fière, informée et bien accompagnée.",
            'home.vision_en' => 'A strong Congo–Kenya partnership serving both peoples, and a proud, informed and well-supported diaspora.',
            'home.objectives_fr' => "Développer les échanges commerciaux, faciliter les voyages, promouvoir la culture congolaise et coopérer avec le PNUE et ONU-Habitat.",
            'home.objectives_en' => 'Grow trade, make travel easier, promote Congolese culture and cooperate with UNEP and UN-Habitat.',

            'ambassador.name' => 'S.E. Léon François Yendouma',
            'ambassador.position_fr' => 'Ambassadeur Extraordinaire et Plénipotentiaire',
            'ambassador.position_en' => 'Ambassador Extraordinary and Plenipotentiary',
            'ambassador.photo' => $this->image('site:ambassadeur.jpg', 'settings', 1000),
            'ambassador.published' => '1',
            'ambassador.message_fr' => "Chers compatriotes, chers amis du Congo, soyez les bienvenus. Notre porte est ouverte : l'Ambassade est votre maison à Nairobi. Nous travaillons chaque jour pour vous accompagner et pour rapprocher nos deux pays.",
            'ambassador.message_en' => 'Dear compatriots, dear friends of the Congo, welcome. Our door is open: the Embassy is your home in Nairobi. We work every day to support you and to bring our two countries closer.',
            'ambassador.biography_fr' => '<p>Diplomate de carrière, l\'Ambassadeur a servi dans plusieurs postes en Afrique et en Europe avant sa nomination à Nairobi.</p><h3>Parcours</h3><ul><li>Diplômé en relations internationales</li><li>Conseiller au ministère des Affaires étrangères</li><li>Chef de mission adjoint dans plusieurs ambassades</li></ul><p>Il est également Représentant permanent du Congo auprès du PNUE et d\'ONU-Habitat.</p>',
            'ambassador.biography_en' => '<p>A career diplomat, the Ambassador served in several posts in Africa and Europe before his appointment to Nairobi.</p><h3>Career</h3><ul><li>Graduate in international relations</li><li>Adviser at the Ministry of Foreign Affairs</li><li>Deputy head of mission in several embassies</li></ul><p>He is also the Congo\'s Permanent Representative to UNEP and UN-Habitat.</p>',
            'ambassador.signature' => 'Léon François Yendouma',

            'contacts.hotline' => '+254 700 000 111',
            'contacts.whatsapp' => '+254 700 000 222',
            'contacts.email' => 'urgence@ambassade-congo.ke',
            'contacts.duty_officer' => 'Agent consulaire de permanence',
            'contacts.instructions_fr' => "En cas d'arrestation, d'accident, de décès d'un proche ou de perte de passeport, appelez la ligne d'urgence à toute heure. Munissez-vous si possible d'une copie de votre pièce d'identité.",
            'contacts.instructions_en' => 'In case of arrest, accident, death of a relative or loss of passport, call the emergency line at any time. If possible, keep a copy of your ID at hand.',

            'site.map_url' => 'https://maps.google.com/?q=United+Crescent+Gigiri+Nairobi',
            'site.facebook' => 'https://www.facebook.com/',
            'site.x' => 'https://x.com/',
            'site.youtube' => 'https://www.youtube.com/',
            'site.footer_fr' => "Site officiel de l'Ambassade de la République du Congo au Kenya, accréditée également en Ouganda et en Tanzanie.",
            'site.footer_en' => 'Official website of the Embassy of the Republic of the Congo in Kenya, also accredited to Uganda and Tanzania.',
            'site.seo_description_fr' => "Services consulaires, actualités et rendez-vous en ligne de l'Ambassade de la République du Congo à Nairobi.",
            'site.seo_description_en' => 'Consular services, news and online appointments of the Embassy of the Republic of the Congo in Nairobi.',
        ];

        foreach ($values as $key => $value) {
            Setting::set($key, $value);
        }
    }

    // ---------- Pages ----------

    private function pages(): void
    {
        $pages = [
            'about-congo' => [
                'image' => 'demo:lac-montagne.jpg',
                'fr' => '<p>La République du Congo s\'étend de l\'océan Atlantique au cœur du bassin du Congo, deuxième massif forestier tropical du monde. Sa capitale, Brazzaville, fait face à Kinshasa de part et d\'autre du fleuve Congo.</p><h2>Repères</h2><ul><li><strong>Capitale :</strong> Brazzaville</li><li><strong>Langue officielle :</strong> français ; langues nationales : lingala et kituba</li><li><strong>Monnaie :</strong> franc CFA (XAF)</li><li><strong>Fête nationale :</strong> 15 août</li></ul><h2>Une nature exceptionnelle</h2><p>Les parcs nationaux d\'Odzala-Kokoua et de Nouabalé-Ndoki abritent gorilles des plaines, éléphants de forêt et une biodiversité unique, qui attirent chercheurs et voyageurs du monde entier.</p><blockquote>Le Congo, c\'est un fleuve, une forêt et un peuple accueillant.</blockquote>',
                'en' => '<p>The Republic of the Congo stretches from the Atlantic Ocean to the heart of the Congo Basin, the second largest tropical forest in the world. Its capital, Brazzaville, faces Kinshasa across the Congo River.</p><h2>Key facts</h2><ul><li><strong>Capital:</strong> Brazzaville</li><li><strong>Official language:</strong> French; national languages: Lingala and Kituba</li><li><strong>Currency:</strong> CFA franc (XAF)</li><li><strong>National day:</strong> 15 August</li></ul><h2>Outstanding nature</h2><p>Odzala-Kokoua and Nouabalé-Ndoki national parks are home to lowland gorillas, forest elephants and unique biodiversity, attracting researchers and travellers from around the world.</p>',
            ],
            'about-embassy' => [
                'image' => 'site:ambassade-4.jpg',
                'fr' => '<p>Installée à Gigiri, à Nairobi, l\'Ambassade représente la République du Congo au Kenya et est également accréditée en Ouganda et en Tanzanie.</p><h2>Nos missions</h2><ul><li>Délivrer passeports, visas, laissez-passer et actes d\'état civil</li><li>Protéger et accompagner les ressortissants congolais</li><li>Promouvoir les échanges économiques, culturels et universitaires</li><li>Représenter le Congo auprès du PNUE et d\'ONU-Habitat</li></ul><h2>Horaires</h2><p>Du lundi au vendredi, de 9 h à 17 h. Dépôt des dossiers consulaires le matin, retrait l\'après-midi.</p>',
                'en' => '<p>Located in Gigiri, Nairobi, the Embassy represents the Republic of the Congo in Kenya and is also accredited to Uganda and Tanzania.</p><h2>Our missions</h2><ul><li>Issue passports, visas, travel documents and civil status records</li><li>Protect and support Congolese nationals</li><li>Promote economic, cultural and academic exchanges</li><li>Represent the Congo to UNEP and UN-Habitat</li></ul><h2>Opening hours</h2><p>Monday to Friday, 9 am to 5 pm. Consular applications in the morning, collection in the afternoon.</p>',
            ],
            'invest-in-congo' => [
                'image' => 'demo:brazzaville-centre.jpg',
                'fr' => '<p>Le Congo offre un accès direct à l\'océan Atlantique par le port en eau profonde de Pointe-Noire et une position centrale en Afrique.</p><h2>Secteurs porteurs</h2><ul><li>Agriculture et agro-industrie</li><li>Bois et économie verte</li><li>Mines et énergie</li><li>Tourisme et écotourisme</li><li>Numérique et services</li></ul><h2>Être accompagné</h2><p>Le service économique de l\'Ambassade oriente les investisseurs kenyans vers les bons interlocuteurs à Brazzaville et à Pointe-Noire. Écrivez-nous depuis la page Contact.</p>',
                'en' => '<p>The Congo offers direct access to the Atlantic Ocean through the deep-water port of Pointe-Noire and a central position in Africa.</p><h2>Growth sectors</h2><ul><li>Agriculture and agribusiness</li><li>Timber and the green economy</li><li>Mining and energy</li><li>Tourism and ecotourism</li><li>Digital and services</li></ul><h2>Getting support</h2><p>The Embassy\'s economic desk connects Kenyan investors with the right contacts in Brazzaville and Pointe-Noire. Write to us from the Contact page.</p>',
            ],
        ];

        foreach ($pages as $slug => $page) {
            Page::updateOrCreate(['slug' => $slug], [
                'body_fr' => $page['fr'],
                'body_en' => $page['en'],
                'image_path' => $this->image($page['image'], 'pages', 2400),
                'status' => Page::STATUS_PUBLISHED,
            ]);
        }
    }

    // ---------- Services ----------

    private function services(): array
    {
        $services = [
            ['identification-card', 'Passeport biométrique', 'Biometric passport',
                'Première demande ou renouvellement du passeport biométrique congolais.', 'First application or renewal of the Congolese biometric passport.',
                '<ul><li>Acte de naissance</li><li>Ancien passeport (renouvellement)</li><li>Deux photos d\'identité récentes</li><li>Justificatif de domicile au Kenya</li></ul>',
                '<ul><li>Birth certificate</li><li>Previous passport (renewal)</li><li>Two recent passport photos</li><li>Proof of address in Kenya</li></ul>',
                '<p>100 000 FCFA, payables en shillings kenyans au taux du jour.</p>', '<p>100,000 FCFA, payable in Kenyan shillings at the daily rate.</p>',
                '4 à 6 semaines', '4 to 6 weeks'],
            ['airplane-tilt', 'Visa pour le Congo', 'Visa to the Congo',
                'Visa de tourisme, d\'affaires ou de visite familiale pour les ressortissants étrangers.', 'Tourist, business or family visit visa for foreign nationals.',
                '<ul><li>Formulaire de demande rempli et signé</li><li>Passeport valable 6 mois</li><li>Certificat de vaccination contre la fièvre jaune</li><li>Réservation d\'hôtel ou lettre d\'invitation</li><li>Billet aller-retour</li></ul>',
                '<ul><li>Completed and signed application form</li><li>Passport valid for 6 months</li><li>Yellow fever vaccination certificate</li><li>Hotel booking or invitation letter</li><li>Return ticket</li></ul>',
                '<ul><li>Visa 1 mois : 60 USD</li><li>Visa 3 mois : 120 USD</li></ul>', '<ul><li>1-month visa: USD 60</li><li>3-month visa: USD 120</li></ul>',
                '3 jours ouvrés', '3 working days'],
            ['stamp', 'Légalisation de documents', 'Document legalisation',
                'Légalisation de diplômes, actes et documents commerciaux destinés au Congo.', 'Legalisation of diplomas, certificates and commercial documents for use in the Congo.',
                '<ul><li>Document original</li><li>Copie du document</li><li>Pièce d\'identité du demandeur</li></ul>', '<ul><li>Original document</li><li>Copy of the document</li><li>Applicant\'s ID</li></ul>',
                '<p>10 000 FCFA par document.</p>', '<p>10,000 FCFA per document.</p>', '48 heures', '48 hours'],
            ['baby', 'Transcription de naissance', 'Birth registration',
                'Enregistrement à l\'état civil congolais d\'un enfant né au Kenya.', 'Registration in the Congolese civil register of a child born in Kenya.',
                '<ul><li>Acte de naissance kenyan</li><li>Passeports des parents</li><li>Acte de mariage le cas échéant</li></ul>', '<ul><li>Kenyan birth certificate</li><li>Parents\' passports</li><li>Marriage certificate if applicable</li></ul>',
                '<p>Gratuit.</p>', '<p>Free of charge.</p>', '2 semaines', '2 weeks'],
            ['certificate', 'Laissez-passer', 'Emergency travel document',
                'Document de voyage d\'urgence en cas de perte ou de vol du passeport.', 'Emergency travel document in case of lost or stolen passport.',
                '<ul><li>Déclaration de perte auprès de la police kenyane</li><li>Toute pièce prouvant la nationalité</li><li>Deux photos</li></ul>', '<ul><li>Police report of loss</li><li>Any proof of nationality</li><li>Two photos</li></ul>',
                '<p>25 000 FCFA.</p>', '<p>25,000 FCFA.</p>', '24 à 48 heures', '24 to 48 hours'],
            ['users-three', 'Immatriculation consulaire', 'Consular registration',
                'Inscription des Congolais résidant au Kenya, en Ouganda ou en Tanzanie.', 'Registration of Congolese nationals living in Kenya, Uganda or Tanzania.',
                '<ul><li>Passeport ou carte d\'identité</li><li>Titre de séjour</li><li>Une photo</li></ul>', '<ul><li>Passport or ID card</li><li>Residence permit</li><li>One photo</li></ul>',
                '<p>Gratuit.</p>', '<p>Free of charge.</p>', 'Immédiat', 'Same day'],
        ];

        $ids = [];
        foreach ($services as $position => [$icon, $titleFr, $titleEn, $descFr, $descEn, $docsFr, $docsEn, $feesFr, $feesEn, $delayFr, $delayEn]) {
            $ids[] = Service::create([
                'icon' => $icon,
                'title_fr' => $titleFr, 'title_en' => $titleEn,
                'description_fr' => "<p>{$descFr}</p>", 'description_en' => "<p>{$descEn}</p>",
                'requirements_fr' => '<p>Se présenter en personne, sur rendez-vous.</p>', 'requirements_en' => '<p>Apply in person, by appointment.</p>',
                'documents_fr' => $docsFr, 'documents_en' => $docsEn,
                'fees_fr' => $feesFr, 'fees_en' => $feesEn,
                'processing_time_fr' => $delayFr, 'processing_time_en' => $delayEn,
                'office_hours_fr' => 'Lundi au vendredi, 9 h – 12 h', 'office_hours_en' => 'Monday to Friday, 9 am – 12 pm',
                'position' => $position + 1,
                'status' => Service::STATUS_PUBLISHED,
            ])->id;
        }

        return $ids;
    }

    // ---------- Actualités ----------

    private function news(): void
    {
        $categories = NewsCategory::pluck('id', 'name_en');
        $articles = [
            ['Diplomatic Relations', 'demo:brazzaville-centre.jpg', 3,
                'Brazzaville et Nairobi renforcent leur coopération économique', 'Brazzaville and Nairobi strengthen economic cooperation',
                'Une commission mixte réunit les deux pays pour relancer les échanges commerciaux et les liaisons aériennes.', 'A joint commission brings both countries together to boost trade and air links.'],
            ['Community', 'demo:pagne-savane.jpg', 8,
                'La diaspora congolaise célèbre la culture du pagne à Nairobi', 'The Congolese diaspora celebrates pagne culture in Nairobi',
                'Défilé, ateliers et exposition : une journée consacrée aux tissus et aux créateurs congolais.', 'Fashion show, workshops and exhibition: a day dedicated to Congolese fabrics and designers.'],
            ['Consular Services', 'demo:brazzaville-avenue.jpg', 12,
                'Passeports biométriques : de nouveaux créneaux de dépôt', 'Biometric passports: new application slots',
                'Pour réduire l\'attente, l\'Ambassade ouvre des créneaux supplémentaires le jeudi après-midi.', 'To reduce waiting times, the Embassy is opening extra slots on Thursday afternoons.'],
            ['Embassy News', 'demo:gorille.jpg', 18,
                'Le Congo présente ses parcs nationaux au PNUE', 'The Congo presents its national parks at UNEP',
                'Odzala-Kokoua et Nouabalé-Ndoki à l\'honneur lors d\'une rencontre sur la protection des grands singes.', 'Odzala-Kokoua and Nouabalé-Ndoki in the spotlight at a meeting on great ape conservation.'],
            ['Events', 'site:ambassadeur-discours.jpg', 25,
                'Réception de la Fête de l\'Indépendance', 'Independence Day reception',
                'L\'Ambassadeur a reçu la communauté et le corps diplomatique pour le 66e anniversaire de l\'indépendance.', 'The Ambassador welcomed the community and the diplomatic corps for the 66th anniversary of independence.'],
            ['Public Information', 'demo:lac-fleurs.jpg', 33,
                'Voyager au Congo : ce qu\'il faut savoir avant de partir', 'Travelling to the Congo: what to know before you go',
                'Visa, vaccination contre la fièvre jaune, monnaie : nos conseils pratiques pour préparer votre séjour.', 'Visa, yellow fever vaccination, currency: our practical tips for your trip.'],
            ['Diplomatic Relations', 'site:presidents-congo-kenya.jpg', 41,
                'Visite de travail d\'une délégation ministérielle', 'Working visit by a ministerial delegation',
                'Commerce, énergie et formation au programme des entretiens bilatéraux à Nairobi.', 'Trade, energy and training on the agenda of bilateral talks in Nairobi.'],
            ['Community', 'demo:marche-quartier.jpg', 55,
                'Rencontre avec les commerçants congolais d\'Afrique de l\'Est', 'Meeting with Congolese traders in East Africa',
                'Échanges sur les formalités douanières et les opportunités du marché commun africain.', 'Discussions on customs formalities and African free trade opportunities.'],
            ['Embassy News', 'demo:ville-au-bord-du-lac.jpg', 70,
                'Mission consulaire itinérante à Kampala', 'Mobile consular mission to Kampala',
                'Les agents consulaires se rendent en Ouganda pour recevoir les demandes de passeport.', 'Consular staff travel to Uganda to receive passport applications.'],
        ];

        foreach ($articles as [$category, $image, $daysAgo, $titleFr, $titleEn, $excerptFr, $excerptEn]) {
            News::create([
                'slug' => News::uniqueSlug($titleFr),
                'news_category_id' => $categories[$category] ?? null,
                'title_fr' => $titleFr, 'title_en' => $titleEn,
                'excerpt_fr' => $excerptFr, 'excerpt_en' => $excerptEn,
                'body_fr' => "<p>{$excerptFr}</p><p>La rencontre s'est tenue en présence de représentants des deux pays, qui ont salué la qualité de leurs relations et convenu d'un calendrier de travail pour les prochains mois.</p><h2>Les prochaines étapes</h2><ul><li>Un groupe de travail se réunira chaque trimestre</li><li>Un compte rendu sera publié sur ce site</li></ul><p>Pour toute question, écrivez-nous depuis la page Contact.</p>",
                'body_en' => "<p>{$excerptEn}</p><p>The meeting was attended by representatives of both countries, who welcomed the quality of their relations and agreed on a work plan for the coming months.</p><h2>Next steps</h2><ul><li>A working group will meet every quarter</li><li>A report will be published on this website</li></ul><p>For any question, write to us from the Contact page.</p>",
                'image_path' => $this->image($image, 'news'),
                'author' => 'Service de presse',
                'published_on' => today()->subDays($daysAgo),
                'status' => News::STATUS_PUBLISHED,
            ]);
        }

        // Un brouillon et un article programmé : visibles dans l'admin seulement
        News::create(['slug' => 'brouillon-rapport-annuel', 'title_fr' => 'Rapport d\'activité annuel (brouillon)', 'published_on' => today(), 'status' => News::STATUS_DRAFT]);
        News::create(['slug' => 'semaine-culturelle', 'title_fr' => 'Semaine culturelle congolaise', 'title_en' => 'Congolese cultural week', 'excerpt_fr' => 'Programme complet bientôt disponible.', 'published_on' => today()->addDays(10), 'status' => News::STATUS_PUBLISHED]);
    }

    private function announcements(): void
    {
        $items = [
            ['urgent', true, -2, 12, 'Fermeture exceptionnelle le 1er octobre', 'Exceptional closure on 1 October', 'Consulaire',
                'L\'Ambassade sera exceptionnellement fermée. Les rendez-vous de ce jour sont reportés au lendemain ; en cas d\'urgence, appelez la ligne de permanence.',
                'The Embassy will be exceptionally closed. Appointments on that day are moved to the next day; in an emergency, call the duty line.'],
            ['important', true, -5, null, 'Nouveau tarif des visas à compter du 1er novembre', 'New visa fees from 1 November', 'Visa',
                'Les frais de visa sont désormais payables uniquement par virement bancaire ou M-Pesa. Aucun paiement en espèces ne sera accepté.',
                'Visa fees are now payable only by bank transfer or M-Pesa. No cash payments will be accepted.'],
            ['normal', false, -15, null, 'Recensement de la communauté congolaise', 'Census of the Congolese community', 'Communauté',
                'Tous les Congolais résidant au Kenya, en Ouganda et en Tanzanie sont invités à se faire immatriculer auprès de l\'Ambassade.',
                'All Congolese nationals living in Kenya, Uganda and Tanzania are invited to register with the Embassy.'],
            ['normal', false, -60, -30, 'Élections : révision des listes électorales', 'Elections: electoral roll review', 'Consulaire',
                'La période de révision des listes est close.', 'The electoral roll review period has ended.'],
        ];

        foreach ($items as [$priority, $pinned, $from, $to, $titleFr, $titleEn, $category, $bodyFr, $bodyEn]) {
            Announcement::create([
                'title_fr' => $titleFr, 'title_en' => $titleEn,
                'body_fr' => "<p>{$bodyFr}</p>", 'body_en' => "<p>{$bodyEn}</p>",
                'category' => $category,
                'priority' => $priority,
                'is_pinned' => $pinned,
                'published_on' => today()->addDays($from),
                'expires_on' => $to === null ? null : today()->addDays($to),
                'status' => Announcement::STATUS_PUBLISHED,
            ]);
        }
    }

    private function events(): void
    {
        $events = [
            [12, '18:00', 'demo:pagne-savane.jpg', 'Soirée culturelle congolaise', 'Congolese cultural evening', 'Résidence de l\'Ambassadeur, Gigiri', 'Ambassador\'s Residence, Gigiri',
                'Musique, danse et gastronomie congolaises pour célébrer la richesse culturelle du pays.', 'Congolese music, dance and food to celebrate the country\'s cultural richness.', 'https://example.com/inscription'],
            [21, '10:00', 'demo:brazzaville-centre.jpg', 'Forum économique Congo–Kenya', 'Congo–Kenya Business Forum', 'Hôtel Tribe, Nairobi', 'Tribe Hotel, Nairobi',
                'Rencontre entre entreprises kenyanes et congolaises : agriculture, logistique, numérique.', 'Kenyan and Congolese companies meet: agriculture, logistics, digital.', 'https://example.com/forum'],
            [35, '09:00', 'demo:ville-au-bord-du-lac.jpg', 'Mission consulaire à Kampala', 'Consular mission to Kampala', 'Kampala, Ouganda', 'Kampala, Uganda',
                'Dépôt des demandes de passeport et d\'immatriculation pour les Congolais d\'Ouganda.', 'Passport and registration applications for Congolese nationals in Uganda.', null],
            [48, '15:00', 'demo:gorille.jpg', 'Conférence : protéger les forêts du bassin du Congo', 'Talk: protecting the Congo Basin forests', 'Siège du PNUE, Gigiri', 'UNEP Headquarters, Gigiri',
                'Table ronde avec chercheurs et gestionnaires de parcs nationaux.', 'Round table with researchers and national park managers.', null],
            [-44, '18:30', 'site:ambassadeur-discours.jpg', 'Réception de la Fête de l\'Indépendance', 'Independence Day reception', 'Résidence de l\'Ambassadeur', 'Ambassador\'s Residence',
                'Célébration du 15 août avec la communauté et le corps diplomatique.', 'Celebration of 15 August with the community and the diplomatic corps.', null],
            [-90, '14:00', 'site:diaspora.jpg', 'Assemblée de la diaspora', 'Diaspora meeting', 'Chancellerie', 'Chancery',
                'Échanges avec l\'Ambassadeur sur les services consulaires.', 'Discussion with the Ambassador on consular services.', null],
        ];

        foreach ($events as [$days, $time, $image, $titleFr, $titleEn, $venueFr, $venueEn, $descFr, $descEn, $url]) {
            Event::create([
                'title_fr' => $titleFr, 'title_en' => $titleEn,
                'venue_fr' => $venueFr, 'venue_en' => $venueEn,
                'description_fr' => "<p>{$descFr}</p><p>Entrée libre sur inscription, dans la limite des places disponibles.</p>",
                'description_en' => "<p>{$descEn}</p><p>Free admission upon registration, subject to availability.</p>",
                'starts_on' => today()->addDays($days),
                'starts_at' => $time,
                'organizer' => 'Ambassade du Congo',
                'image_path' => $this->image($image, 'events'),
                'registration_url' => $url,
                'status' => Event::STATUS_PUBLISHED,
            ]);
        }
    }

    private function documents(): void
    {
        $documents = [
            ['Visas', 'Formulaire de demande de visa', 'Visa application form', 312, 148],
            ['Visas', 'Liste des pièces pour un visa d\'affaires', 'Business visa checklist', 96, 64],
            ['Passeports', 'Formulaire de demande de passeport', 'Passport application form', 280, 211],
            ['Passeports', 'Déclaration de perte de passeport', 'Lost passport declaration', 88, 37],
            ['État civil', 'Demande de transcription d\'acte de naissance', 'Birth registration request', 120, 42],
            ['Communauté', 'Fiche d\'immatriculation consulaire', 'Consular registration form', 150, 93],
            ['Économie', 'Guide de l\'investisseur au Congo', 'Congo investor guide', 1840, 58],
        ];

        foreach ($documents as [$category, $titleFr, $titleEn, $kb, $downloads]) {
            $path = 'documents/demo/' . \Illuminate\Support\Str::slug($titleFr) . '.pdf';
            Storage::disk('public')->put($path, $this->pdf($titleFr));
            Document::create([
                'title_fr' => $titleFr, 'title_en' => $titleEn,
                'category' => $category,
                'file_path' => $path,
                'file_name' => \Illuminate\Support\Str::slug($titleEn) . '.pdf',
                'file_size' => $kb * 1024,
                'download_count' => $downloads,
                'status' => Document::STATUS_PUBLISHED,
            ]);
        }
    }

    private function holidays(): void
    {
        $year = today()->year;
        $holidays = [
            ["{$year}-01-01", 'Jour de l\'an', 'New Year\'s Day'],
            ["{$year}-05-01", 'Fête du Travail', 'Labour Day'],
            ["{$year}-06-01", 'Madaraka Day (Kenya)', 'Madaraka Day (Kenya)'],
            ["{$year}-06-10", 'Fête de la Réconciliation nationale', 'National Reconciliation Day'],
            ["{$year}-08-15", 'Fête de l\'Indépendance', 'Independence Day'],
            ["{$year}-10-20", 'Mashujaa Day (Kenya)', 'Mashujaa Day (Kenya)'],
            ["{$year}-11-01", 'Toussaint', 'All Saints\' Day'],
            ["{$year}-11-28", 'Proclamation de la République', 'Proclamation of the Republic'],
            ["{$year}-12-12", 'Jamhuri Day (Kenya)', 'Jamhuri Day (Kenya)'],
            ["{$year}-12-25", 'Noël', 'Christmas Day'],
        ];

        foreach ($holidays as [$date, $fr, $en]) {
            Holiday::create(['date' => $date, 'name_fr' => $fr, 'name_en' => $en, 'status' => Holiday::STATUS_PUBLISHED]);
        }
    }

    private function gallery(): void
    {
        $albums = Album::pluck('id', 'name_en');
        $photos = [
            ['Embassy & Chancery', 'site:ambassade-1.jpg', 'La chancellerie', 'The chancery', true],
            ['Embassy & Chancery', 'site:bureaux-2.jpg', 'Les bureaux consulaires', 'Consular offices', false],
            ['Embassy & Chancery', 'site:bureaux-3.jpg', 'Salle d\'attente', 'Waiting room', false],
            ['Diplomatic Events', 'site:ambassadeur-discours.jpg', 'Allocution de l\'Ambassadeur', 'Ambassador\'s address', true],
            ['Official Meetings', 'site:ruto-nguesso.jpg', 'Rencontre au sommet', 'Summit meeting', true],
            ['Official Meetings', 'site:drapeaux-congo-kenya.jpg', 'Les drapeaux des deux pays', 'Flags of both countries', false],
            ['Community & Diaspora', 'demo:pagne-savane.jpg', 'Élégance du pagne', 'The elegance of pagne', true],
            ['Community & Diaspora', 'demo:marche-quartier.jpg', 'Vie de quartier', 'Neighbourhood life', false],
            ['National Celebrations', 'site:drapeau-congo.jpg', 'Le drapeau national', 'The national flag', false],
            ['Consular Services', 'demo:brazzaville-avenue.jpg', 'Brazzaville, le boulevard', 'Brazzaville boulevard', false],
            ['Consular Services', 'demo:brazzaville-centre.jpg', 'Le centre-ville de Brazzaville', 'Downtown Brazzaville', true],
            ['Diplomatic Events', 'demo:gorille.jpg', 'Gorille des plaines, Odzala', 'Lowland gorilla, Odzala', true],
            ['National Celebrations', 'demo:lac-montagne.jpg', 'Paysage du Congo', 'Congolese landscape', false],
            ['National Celebrations', 'demo:lac-fleurs.jpg', 'Lac et bougainvilliers', 'Lake and bougainvillea', true],
            ['Community & Diaspora', 'demo:ville-au-bord-du-lac.jpg', 'Une ville au bord du lac', 'A lakeside town', false],
        ];

        foreach ($photos as [$album, $image, $fr, $en, $featured]) {
            Photo::create([
                'album_id' => $albums[$album] ?? null,
                'title_fr' => $fr, 'title_en' => $en,
                'image_path' => $this->image($image, 'gallery', 2000),
                'is_featured' => $featured,
            ]);
        }
    }

    private function requests(array $services): void
    {
        $people = [
            ['Grâce Mabiala', 'grace.mabiala@example.com', '+254 711 000 101', 'Congolaise', 'pending', 3, 'fr'],
            ['Brian Otieno', 'brian.otieno@example.com', '+254 722 000 202', 'Kényane', 'pending', 5, 'en'],
            ['Chancel Ngoma', 'chancel.ngoma@example.com', null, 'Congolaise', 'confirmed', 2, 'fr'],
            ['Amina Wanjiru', 'amina.wanjiru@example.com', '+254 733 000 303', 'Kényane', 'rescheduled', 7, 'en'],
            ['Destin Moukala', 'destin.moukala@example.com', '+254 744 000 404', 'Congolaise', 'done', -6, 'fr'],
        ];
        foreach ($people as $i => [$name, $email, $phone, $nationality, $status, $days, $locale]) {
            $date = today()->addDays($days);
            while ($date->isWeekend()) {
                $date->addDay();
            }
            Appointment::create([
                'name' => $name, 'email' => $email, 'phone' => $phone, 'nationality' => $nationality,
                'service_id' => $services[$i % count($services)],
                'preferred_date' => $date, 'preferred_time' => ['09:30', '10:00', '11:00', '14:30', '15:00'][$i],
                'status' => $status, 'locale' => $locale,
                'notes' => $i === 0 ? 'Je viens avec mes deux enfants pour leur premier passeport.' : null,
            ]);
        }

        $messages = [
            ['Samuel Kiptoo', 'samuel.kiptoo@example.com', 'Investir dans l\'agriculture', "Bonjour,\n\nNotre coopérative souhaite exporter du thé vers Brazzaville. Qui contacter ?\n\nMerci.", 'unread'],
            ['Nadège Loemba', 'nadege.loemba@example.com', 'Retrait de passeport', "Bonjour, mon passeport est-il prêt ? Dossier déposé il y a cinq semaines.", 'unread'],
            ['Peter Mwangi', 'peter.mwangi@example.com', 'Tourist visa question', "Hello, can I apply for a visa by mail?", 'read'],
        ];
        foreach ($messages as [$name, $email, $subject, $body, $status]) {
            Message::create(['name' => $name, 'email' => $email, 'subject' => $subject, 'body' => $body, 'status' => $status, 'read_at' => $status === 'read' ? now() : null]);
        }
    }

    // ---------- Outils ----------

    /** « demo:fichier.jpg » ou « site:fichier.jpg » → photo WebP sur le disque public (une seule fois par taille) */
    private function image(string $source, string $folder, int $maxSide = 1600): string
    {
        [$origin, $name] = explode(':', $source);
        $path = $origin === 'demo' ? database_path("seeders/demo/{$name}") : public_path("images/site/{$name}");

        return $this->stored["{$source}:{$folder}:{$maxSide}"] ??= Images::store(new UploadedFile($path, $name, null, null, true), $folder, $maxSide);
    }

    /** Un petit PDF d'une page, pour que les téléchargements fonctionnent */
    private function pdf(string $title): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], iconv('UTF-8', 'Windows-1252//TRANSLIT', $title));
        $stream = "BT /F1 18 Tf 72 760 Td ({$text}) Tj 0 -28 Td /F1 11 Tf (Document de demonstration - Ambassade du Congo au Kenya) Tj ET";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n{$stream}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf . 'trailer' . "\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
