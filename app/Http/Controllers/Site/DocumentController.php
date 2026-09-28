<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        // Regroupés par catégorie ; les documents sans catégorie viennent en dernier
        $groups = Document::published()
            ->orderBy('title_fr')
            ->get()
            ->groupBy(fn (Document $document) => $document->category ?: '')
            ->sortKeys()
            ->sortBy(fn ($documents, $category) => $category === '' ? 1 : 0);

        return view('site.documents.index', compact('groups'));
    }

    public function download(Document $document)
    {
        abort_unless($document->isPublished() && Storage::disk('public')->exists($document->file_path), 404);

        // Compté sans toucher à updated_at : un téléchargement n'est pas une modification
        Document::whereKey($document->id)->increment('download_count');

        return Storage::disk('public')->download($document->file_path, $document->file_name ?: basename($document->file_path));
    }
}
