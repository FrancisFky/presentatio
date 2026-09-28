<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Album;

class GalleryController extends Controller
{
    public function index()
    {
        // Un album sans photo n'a rien à montrer
        $albums = Album::whereHas('photos')
            ->with('cover')
            ->withCount('photos')
            ->latest('id')
            ->paginate(12);

        return view('site.gallery.index', compact('albums'));
    }

    public function show(Album $album)
    {
        return view('site.gallery.show', [
            'album' => $album,
            'photos' => $album->photos()->orderByDesc('is_featured')->orderBy('id')->get(),
        ]);
    }
}
