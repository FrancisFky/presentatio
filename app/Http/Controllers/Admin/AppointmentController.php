<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentStatusChanged;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/** Demandes de rendez-vous reçues du site */
class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with('service')
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('reference', 'like', '%' . $request->search . '%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('service'), fn ($q) => $q->where('service_id', $request->service))
            ->when($request->input('when') === 'upcoming', fn ($q) => $q->whereDate('preferred_date', '>=', today())->reorder()->orderBy('preferred_date')->orderBy('preferred_time'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.appointments.index', [
            'current' => 'appointments',
            'appointments' => $appointments,
            'services' => Service::ordered()->pluck('title_fr', 'id'),
            'counts' => Appointment::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Appointment $appointment)
    {
        return view('admin.appointments.show', ['current' => 'appointments', 'appointment' => $appointment->load('service')]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Appointment::STATUSES))],
            'preferred_date' => ['required', 'date'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $notify = $request->boolean('notify');
        $message = $data['message'] ?? null;
        unset($data['message']);

        $appointment->update($data);

        if ($notify) {
            Mail::to($appointment->email)->send(new AppointmentStatusChanged($appointment, $message));
        }

        return back()->with('success', $notify ? 'Rendez-vous mis à jour, demandeur prévenu par e-mail.' : 'Rendez-vous mis à jour.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Demande supprimée.');
    }
}
