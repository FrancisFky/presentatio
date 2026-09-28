<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Images;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    use HandlesUploads;

    public function index(Request $request)
    {
        $events = Event::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title_fr', 'like', '%' . $request->search . '%')
                ->orWhere('title_en', 'like', '%' . $request->search . '%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            // À venir d'abord (le plus proche en tête), puis les passés du plus récent au plus ancien
            ->orderByRaw('starts_on < ? asc', [today()->toDateString()])
            ->orderByRaw('case when starts_on >= ? then starts_on end asc', [today()->toDateString()])
            ->orderByDesc('starts_on')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.index', ['current' => 'events', 'events' => $events]);
    }

    public function create()
    {
        return $this->form(new Event(['starts_on' => today()->addWeek(), 'status' => Event::STATUS_DRAFT]));
    }

    public function store(Request $request)
    {
        $event = Event::create($this->validated($request));
        $this->syncImage($request, $event, 'events');

        return redirect()->route('events.index')->with('success', 'Événement créé.');
    }

    public function edit(Event $event)
    {
        return $this->form($event);
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validated($request));
        $this->syncImage($request, $event, 'events');

        return redirect()->route('events.index')->with('success', 'Événement mis à jour.');
    }

    public function destroy(Event $event)
    {
        Images::delete($event->image_path);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Événement supprimé.');
    }

    private function form(Event $event)
    {
        return view('admin.events.form', ['current' => 'events', 'event' => $event]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            ...Translated::rules('description', ['string']),
            ...Translated::rules('venue', ['string', 'max:255']),
            'starts_on' => ['required', 'date'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'organizer' => ['nullable', 'string', 'max:150'],
            'speaker' => ['nullable', 'string', 'max:150'],
            'registration_url' => ['nullable', 'url:https,http', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Event::STATUSES))],
            'image' => $this->imageRules(),
        ]);

        unset($data['image']);

        return Translated::cleanRich($data, ['description']);
    }
}
