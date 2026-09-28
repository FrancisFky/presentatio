<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Site;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->isPublished(), 404);

        return view('site.page', [
            'page' => $page,
            // Les autres pages « À propos », pour poursuivre la lecture
            'siblings' => Page::published()->whereIn('slug', Site::ABOUT_PAGES)->whereKeyNot($page->id)->get(),
        ]);
    }

    public function ambassador()
    {
        abort_unless(Site::ambassadorPublished(), 404);

        return view('site.ambassador');
    }
}
