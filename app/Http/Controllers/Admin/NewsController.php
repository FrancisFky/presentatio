<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Services\Images;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    use HandlesUploads;

    public function index(Request $request)
    {
        $news = News::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title_fr', 'like', '%' . $request->search . '%')
                ->orWhere('title_en', 'like', '%' . $request->search . '%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('category'), fn ($q) => $q->where('news_category_id', $request->category))
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.news.index', [
            'current' => 'news',
            'news' => $news,
            'categories' => NewsCategory::withCount('news')->orderBy('name_fr')->get(),
        ]);
    }

    public function create()
    {
        return $this->form(new News([
            'published_on' => today(),
            'status' => News::STATUS_DRAFT,
            'author' => auth('admin')->user()->name,
        ]));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = News::uniqueSlug($data['title_fr']);

        $news = News::create($data);
        $this->syncImage($request, $news, 'news');
        $this->syncFile($request, $news, 'news/attachments');

        return redirect()->route('news.index')->with('success', 'Article créé.');
    }

    public function edit(News $news)
    {
        return $this->form($news);
    }

    public function update(Request $request, News $news)
    {
        $news->update($this->validated($request, $news));
        $this->syncImage($request, $news, 'news');
        $this->syncFile($request, $news, 'news/attachments');

        return redirect()->route('news.index')->with('success', 'Article mis à jour.');
    }

    public function destroy(News $news)
    {
        Images::delete($news->image_path);
        $this->deleteFile($news->attachment_path);
        $news->delete();

        return redirect()->route('news.index')->with('success', 'Article supprimé.');
    }

    private function form(News $news)
    {
        return view('admin.news.form', [
            'current' => 'news',
            'news' => $news,
            'categories' => NewsCategory::orderBy('name_fr')->pluck('name_fr', 'id'),
        ]);
    }

    private function validated(Request $request, ?News $news = null): array
    {
        $data = $request->validate([
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            ...Translated::rules('excerpt', ['string', 'max:500']),
            ...Translated::rules('body', ['string']),
            'news_category_id' => ['nullable', 'exists:news_categories,id'],
            'author' => ['nullable', 'string', 'max:150'],
            'published_on' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(News::STATUSES))],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('news', 'slug')->ignore($news?->id)],
            'image' => $this->imageRules(),
            'attachment' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        unset($data['image'], $data['attachment']);

        // Adresse de l'article : modifiable, sinon inchangée
        if (blank($data['slug'] ?? null)) {
            unset($data['slug']);
        }

        return Translated::cleanRich($data, ['body']);
    }
}
