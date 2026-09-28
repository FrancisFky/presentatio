<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Support\Translated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Jours de fermeture de l'ambassade */
class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', today()->year);

        return view('admin.holidays.index', [
            'current' => 'holidays',
            'year' => $year,
            'holidays' => Holiday::whereYear('date', $year)->orderBy('date')->get(),
            'years' => Holiday::pluck('date')->map->year->push(today()->year, today()->year + 1)->unique()->sortDesc()->values(),
        ]);
    }

    public function create()
    {
        return $this->form(new Holiday(['status' => Holiday::STATUS_PUBLISHED, 'date' => today()]));
    }

    public function store(Request $request)
    {
        $holiday = Holiday::create($this->validated($request));

        return redirect()->route('holidays.index', ['year' => $holiday->date->year])->with('success', 'Jour férié ajouté.');
    }

    public function edit(Holiday $holiday)
    {
        return $this->form($holiday);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $holiday->update($this->validated($request));

        return redirect()->route('holidays.index', ['year' => $holiday->date->year])->with('success', 'Jour férié mis à jour.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Jour férié supprimé.');
    }

    private function form(Holiday $holiday)
    {
        return view('admin.holidays.form', ['current' => 'holidays', 'holiday' => $holiday]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            ...Translated::rules('name', ['string', 'max:255'], required: true),
            ...Translated::rules('description', ['string', 'max:1000']),
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(Holiday::STATUSES))],
        ]);
    }
}
