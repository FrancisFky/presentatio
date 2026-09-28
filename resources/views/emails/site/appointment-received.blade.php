<x-mail::message>
# {{ __('site.mail.appointment_received.title') }}

{{ __('site.mail.greeting', ['name' => $appointment->name]) }}

{{ __('site.mail.appointment_received.intro') }}

<x-mail::panel>
**{{ __('site.appointment.reference') }} :** {{ $appointment->reference }}<br>
**{{ __('site.appointment.service') }} :** {{ $appointment->serviceName() }}<br>
**{{ __('site.appointment.date') }} :** {{ $appointment->preferred_date->translatedFormat('l j F Y') }}, {{ $appointment->preferred_time }}
</x-mail::panel>

{{ __('site.mail.appointment_received.next') }}

{{ __('site.mail.signature') }}<br>
{{ \App\Models\Setting::localized('site.name', default: config('app.name')) }}
</x-mail::message>
