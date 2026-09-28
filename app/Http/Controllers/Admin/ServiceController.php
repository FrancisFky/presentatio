<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /** Champs mis en forme (listes de pièces, conditions…) */
    const RICH = ['description', 'requirements', 'documents', 'fees'];

    public function index()
    {
        return view('admin.services.index', [
            'current' => 'services',
            'services' => Service::withCount('appointments')->ordered()->get(),
        ]);
    }

    public function create()
    {
        return $this->form(new Service(['status' => Service::STATUS_DRAFT, 'position' => (int) Service::max('position') + 1, 'icon' => 'file-text']));
    }

    public function store(Request $request)
    {
        Service::create($this->validated($request));

        return redirect()->route('services.index')->with('success', 'Service créé.');
    }

    public function edit(Service $service)
    {
        return $this->form($service);
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request));

        return redirect()->route('services.index')->with('success', 'Service mis à jour.');
    }

    public function destroy(Service $service)
    {
        // Les rendez-vous déjà demandés gardent le nom du service
        $service->appointments()->whereNull('service_label')->update(['service_label' => $service->title_fr]);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service supprimé.');
    }

    private function form(Service $service)
    {
        return view('admin.services.form', ['current' => 'services', 'service' => $service]);
    }

    private function validated(Request $request): array
    {
        $rules = [
            ...Translated::rules('title', ['string', 'max:255'], required: true),
            ...Translated::rules('processing_time', ['string', 'max:255']),
            ...Translated::rules('office_hours', ['string', 'max:255']),
            'icon' => ['nullable', Rule::in(array_keys(Service::ICONS))],
            'position' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(array_keys(Service::STATUSES))],
        ];
        foreach (self::RICH as $field) {
            $rules += Translated::rules($field, ['string']);
        }

        return Translated::cleanRich($request->validate($rules), self::RICH);
    }
}
