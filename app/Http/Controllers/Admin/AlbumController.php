<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Services\Images;
use App\Support\Translated;
use Illuminate\Http\Request;

/** Galerie : les albums, et les photos de chaque album */
class AlbumController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', [
            'current' => 'gallery',
            'albums' => Album::withCount('photos')->with('cover')->orderBy('name_fr')->get(),
        ]);
    }

    public function show(Album $album)
    {
        return view('admin.gallery.album', [
            'current' => 'gallery',
            'album' => $album,
            'photos' => $album->photos()->orderByDesc('is_featured')->latest('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $album = Album::create($this->validated($request));

        return redirect()->route('albums.show', $album)->with('success', 'Album créé. Ajoutez-y des photos.');
    }

    public function update(Request $request, Album $album)
    {
        $album->update($this->validated($request));

        return back()->with('success', 'Album mis à jour.');
    }

    public function destroy(Album $album)
    {
        foreach ($album->photos as $photo) {
            Images::delete($photo->image_path);
        }
        $album->photos()->delete();
        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Album et photos supprimés.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            ...Translated::rules('name', ['string', 'max:150'], required: true),
            ...Translated::rules('description', ['string', 'max:1000']),
        ]);
    }
}
