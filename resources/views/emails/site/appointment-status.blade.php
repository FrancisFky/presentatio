<x-mail::message>
# {{ __('site.mail.appointment_status.title.' . $appointment->status) }}

{{ __('site.mail.greeting', ['name' => $appointment->name]) }}

{{ __('site.mail.appointment_status.intro.' . $appointment->status) }}

<x-mail::panel>
**{{ __('site.appointment.reference') }} :** {{ $appointment->reference }}<br>
**{{ __('site.appointment.service') }} :** {{ $appointment->serviceName() }}<br>
**{{ __('site.appointment.date') }} :** {{ $appointment->preferred_date->translatedFormat('l j F Y') }}, {{ $appointment->preferred_time }}
</x-mail::panel>

@if ($note)
{{ $note }}
@endif

@if ($address = \App\Models\Setting::get('site.address'))
**{{ __('site.contact.address') }} :** {{ $address }}
@endif

{{ __('site.mail.signature') }}<br>
{{ \App\Models\Setting::localized('site.name', default: config('app.name')) }}
</x-mail::message>
