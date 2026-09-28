<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

/** Les deux pages à formulaire : le formulaire lui-même est un composant Livewire */
class ContactController extends Controller
{
    public function appointment(Request $request)
    {
        return view('site.appointment', [
            // ?service=ID depuis la fiche d'un service : pré-sélectionné dans le formulaire
            'service' => $request->integer('service') ?: null,
            'holidays' => Holiday::upcoming()->limit(5)->get(),
        ]);
    }

    public function contact()
    {
        return view('site.contact');
    }
}
