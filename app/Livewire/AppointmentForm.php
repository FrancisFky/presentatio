<?php

namespace App\Livewire;

use App\Livewire\Concerns\ProtectsPublicForms;
use App\Mail\AppointmentReceived;
use App\Mail\NewSubmission;
use App\Models\Appointment;
use App\Models\Holiday;
use App\Models\Service;
use App\Support\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

/** Demande de rendez-vous consulaire, depuis /fr/rendez-vous */
class AppointmentForm extends Component
{
    use ProtectsPublicForms;

    /** Valeur du choix « Autre » dans la liste des services */
    public const OTHER = 'other';

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $nationality = '';
    public string $service_id = '';
    public string $service_label = '';
    public string $preferred_date = '';
    public string $preferred_time = '';
    public string $notes = '';
    public bool $consent = false;

    /** Rempli une fois la demande envoyée : affiche le récapitulatif */
    #[Locked]
    public ?string $reference = null;

    #[Locked]
    public array $summary = [];

    public function mount(?int $service = null): void
    {
        if ($service && Service::published()->whereKey($service)->exists()) {
            $this->service_id = (string) $service;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+().\s-]{6,}$/'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'service_id' => ['required', Rule::in([...$this->serviceIds(), self::OTHER])],
            'service_label' => ['nullable', 'required_if:service_id,' . self::OTHER, 'string', 'max:255'],
            'preferred_date' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:today', $this->openDayRule()],
            'preferred_time' => ['required', Rule::in(Appointment::TIME_SLOTS)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'consent' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'preferred_date.after_or_equal' => __('site.appointment.errors.past'),
            'preferred_date.date_format' => __('site.appointment.errors.date'),
            'phone.regex' => __('site.forms.phone_invalid'),
            'service_label.required_if' => __('site.appointment.errors.other_service'),
            'consent.accepted' => __('site.appointment.errors.consent'),
        ];
    }

    protected function validationAttributes(): array
    {
        return collect(['name', 'email', 'phone', 'nationality', 'service_id', 'service_label', 'preferred_date', 'preferred_time', 'notes', 'consent'])
            ->mapWithKeys(fn ($field) => [$field => mb_strtolower(__("site.appointment.fields.{$field}"))])
            ->all();
    }

    /** L'ambassade ne reçoit ni le week-end ni les jours fériés publiés */
    private function openDayRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $date = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();

            if ($date->isWeekend()) {
                $fail(__('site.appointment.errors.weekend'));

                return;
            }

            $holiday = Holiday::published()->whereDate('date', $date)->first();
            if ($holiday) {
                $fail(__('site.appointment.errors.holiday', ['name' => $holiday->t('name')]));
            }
        };
    }

    public function updated(string $property): void
    {
        if ($property === 'service_id' && $this->service_id !== self::OTHER) {
            $this->service_label = '';
            $this->resetValidation('service_label');
        }

        if (array_key_exists($property, $this->rules())) {
            $this->validateOnly($property);
        }
    }

    public function submit(): void
    {
        if ($this->tooManyAttempts()) {
            return;
        }

        // Robot : on fait comme si tout s'était bien passé, sans rien enregistrer
        if ($this->isSpam()) {
            $this->reference = Appointment::newReference();

            return;
        }

        $data = $this->validate();
        $this->countAttempt();

        $isOther = $data['service_id'] === self::OTHER;

        $appointment = Appointment::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'] ?: null,
            'service_id' => $isOther ? null : (int) $data['service_id'],
            'service_label' => $isOther ? $data['service_label'] : null,
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'],
            'notes' => $data['notes'] ?: null,
            'status' => Appointment::STATUS_PENDING,
            'locale' => $this->locale,
        ]);

        $this->sendSafely(fn () => Mail::to($appointment->email, $appointment->name)->send(new AppointmentReceived($appointment)));

        if ($to = Site::notificationEmail()) {
            $this->sendSafely(fn () => Mail::to($to)->send(new NewSubmission($appointment)));
        }

        $this->reference = $appointment->reference;
        $this->summary = [
            'service' => $appointment->serviceName(),
            'date' => $appointment->preferred_date->translatedFormat('l j F Y'),
            'time' => $appointment->preferred_time,
            'email' => $appointment->email,
        ];
        $this->resetExcept('locale', 'reference', 'summary');
    }

    /** Nouvelle demande après un envoi réussi */
    public function startOver(): void
    {
        $this->reset();
        $this->resetValidation();
        $this->locale = app()->getLocale();
    }

    private function serviceIds(): array
    {
        return Service::published()->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function render()
    {
        return view('livewire.appointment-form', [
            'services' => Service::published()->ordered()->get(),
            'timeSlots' => Appointment::TIME_SLOTS,
            'minDate' => today()->toDateString(),
        ]);
    }
}
