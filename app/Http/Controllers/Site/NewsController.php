<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        // Seules les catégories qui ont au moins un article visible servent de filtre
        $categories = NewsCategory::whereHas('news', fn ($q) => $q->visible())->orderBy('name_fr')->get();
        $category = $categories->firstWhere('id', (int) $request->query('category'));

        $news = News::visible()
            ->with('category')
            ->when($category, fn ($q) => $q->where('news_category_id', $category->id))
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        return view('site.news.index', compact('news', 'categories', 'category'));
    }

    public function show(News $news)
    {
        // Brouillon, archive ou article programmé : introuvable, même avec l'adresse exacte
        abort_unless(News::visible()->whereKey($news->id)->exists(), 404);

        return view('site.news.show', [
            'news' => $news->load('category'),
            'related' => News::visible()->with('category')->whereKeyNot($news->id)
                ->orderByDesc('published_on')->orderByDesc('id')->limit(3)->get(),
        ]);
    }
}
