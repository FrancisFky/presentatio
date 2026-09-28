<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use App\Services\Images;
use App\Support\Translated;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    /** Plusieurs photos d'un coup ; titre et légende s'ajoutent ensuite, photo par photo */
    public function store(Request $request, Album $album)
    {
        $request->validate([
            'photos' => ['required', 'array', 'max:30'],
            'photos.*' => ['image', 'mimes:jpeg,png,webp', 'max:8192'],
        ], [
            'photos.required' => 'Choisissez au moins une photo.',
            'photos.*.image' => 'Seules les images (JPG, PNG, WebP) sont acceptées.',
            'photos.*.max' => 'Une photo dépasse 8 Mo.',
        ]);

        foreach ($request->file('photos') as $file) {
            $album->photos()->create(['image_path' => Images::store($file, 'gallery', 2000)]);
        }

        $count = count($request->file('photos'));

        return back()->with('success', $count > 1 ? "{$count} photos ajoutées." : 'Photo ajoutée.');
    }

    public function update(Request $request, Photo $photo)
    {
        $photo->update($request->validate([
            ...Translated::rules('title', ['string', 'max:150']),
            ...Translated::rules('caption', ['string', 'max:500']),
        ]));

        return back()->with('success', 'Photo mise à jour.');
    }

    public function toggleFeatured(Photo $photo)
    {
        $photo->update(['is_featured' => !$photo->is_featured]);

        return back()->with('success', $photo->is_featured ? "Photo mise en avant sur l'accueil." : "Photo retirée de l'accueil.");
    }

    public function destroy(Photo $photo)
    {
        Images::delete($photo->image_path);
        $photo->delete();

        return back()->with('success', 'Photo supprimée.');
    }
}
