<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Service;
use App\Support\Locales;
use App\Support\Site;

/**
 * Plan du site pour les moteurs de recherche : chaque adresse existe en
 * français et en anglais, reliées par des balises hreflang.
 */
class SitemapController extends Controller
{
    public function index()
    {
        $entries = collect();

        $add = function (string $route, array $parameters = [], $updated = null) use ($entries) {
            $urls = collect(Locales::SUPPORTED)->mapWithKeys(fn ($locale) => [$locale => route($route, [...$parameters, 'locale' => $locale])]);
            $entries->push(['urls' => $urls, 'updated' => $updated]);
        };

        foreach (['site.home', 'site.news.index', 'site.announcements.index', 'site.services.index', 'site.events.index', 'site.gallery.index', 'site.documents.index', 'site.appointment', 'site.contact'] as $route) {
            $add($route);
        }

        if (Site::ambassadorPublished()) {
            $add('site.ambassador');
        }

        Page::published()->get()->each(fn ($page) => $add('site.page', ['page' => $page], $page->updated_at));
        Service::published()->ordered()->get()->each(fn ($service) => $add('site.services.show', ['service' => $service], $service->updated_at));
        News::visible()->orderByDesc('published_on')->get()->each(fn ($news) => $add('site.news.show', ['news' => $news], $news->updated_at));
        Event::published()->orderByDesc('starts_on')->get()->each(fn ($event) => $add('site.events.show', ['event' => $event], $event->updated_at));

        return response()
            ->view('site.sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $lines = ['User-agent: *', 'Disallow: /admin', '', 'Sitemap: ' . route('sitemap')];

        return response(implode("\n", $lines) . "\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
