<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\News;
use App\Models\Photo;
use App\Models\Service;

/** L'accueil rassemble un aperçu de chaque rubrique ; une rubrique vide disparaît */
class HomeController extends Controller
{
    public function index()
    {
        return view('site.home', [
            // Bandeau : les communiqués épinglés ou importants seulement, l'urgent en tête
            'announcements' => Announcement::visible()
                ->where(fn ($q) => $q->where('is_pinned', true)->orWhereIn('priority', ['urgent', 'important']))
                ->orderByRaw("case priority when 'urgent' then 0 when 'important' then 1 else 2 end")
                ->orderByDesc('published_on')
                ->limit(3)
                ->get(),
            'services' => Service::published()->ordered()->limit(6)->get(),
            'news' => News::visible()->with('category')->orderByDesc('published_on')->orderByDesc('id')->limit(3)->get(),
            'events' => Event::upcoming()->limit(3)->get(),
            'photos' => Photo::with('album')->orderByDesc('is_featured')->orderByDesc('id')->limit(6)->get(),
            'documents' => Document::published()->orderByDesc('download_count')->orderByDesc('id')->limit(4)->get(),
            'holidays' => Holiday::upcoming()->limit(4)->get(),
        ]);
    }
}
