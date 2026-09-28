<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Support\Translated;
use Illuminate\Http\Request;

/** Rubriques des actualités, gérées depuis la liste des articles */
class NewsCategoryController extends Controller
{
    public function store(Request $request)
    {
        NewsCategory::create($request->validate(Translated::rules('name', ['string', 'max:100'], required: true)));

        return back()->with('success', 'Rubrique ajoutée.');
    }

    public function update(Request $request, NewsCategory $newsCategory)
    {
        $newsCategory->update($request->validate(Translated::rules('name', ['string', 'max:100'], required: true)));

        return back()->with('success', 'Rubrique renommée.');
    }

    public function destroy(NewsCategory $newsCategory)
    {
        // Les articles de la rubrique restent, sans rubrique (clé étrangère nullOnDelete)
        $newsCategory->delete();

        return back()->with('success', 'Rubrique supprimée.');
    }
}
