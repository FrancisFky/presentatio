<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Deux onglets en simples liens : ils marchent sans JavaScript et se partagent
        $tab = $request->query('tab') === 'past' ? 'past' : 'upcoming';

        $events = ($tab === 'past' ? Event::past() : Event::upcoming())
            ->paginate(9)
            ->withQueryString();

        return view('site.events.index', compact('events', 'tab'));
    }

    public function show(Event $event)
    {
        abort_unless($event->isPublished(), 404);

        return view('site.events.show', [
            'event' => $event,
            'upcoming' => Event::upcoming()->whereKeyNot($event->id)->limit(3)->get(),
        ]);
    }
}
