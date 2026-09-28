<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Pages fixes du menu « À propos » */
class PageController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.pages.index', ['current' => 'pages', 'pages' => Page::orderBy('id')->get()]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', ['current' => 'pages', 'page' => $page]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            ...Translated::rules('body', ['string']),
            'status' => ['required', Rule::in(array_keys(Page::STATUSES))],
            'image' => $this->imageRules(),
        ]);
        unset($data['image']);

        $page->update(Translated::cleanRich($data, ['body']));
        $this->syncImage($request, $page, 'pages', maxSide: 2400);

        return redirect()->route('pages.index')->with('success', "Page « {$page->title_fr} » mise à jour.");
    }
}
