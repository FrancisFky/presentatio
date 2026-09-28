<x-mail::message>
@if ($submission instanceof \App\Models\Appointment)
# Nouvelle demande de rendez-vous

<x-mail::table>
| | |
|:--|:--|
| Référence | **{{ $submission->reference }}** |
| Demandeur | {{ $submission->name }} |
| E-mail | {{ $submission->email }} |
| Téléphone | {{ $submission->phone ?: '—' }} |
| Nationalité | {{ $submission->nationality ?: '—' }} |
| Service | {{ $submission->serviceName() }} |
| Date souhaitée | {{ $submission->preferred_date->translatedFormat('l j F Y') }}, {{ $submission->preferred_time }} |
| Langue | {{ strtoupper($submission->locale) }} |
</x-mail::table>

@if ($submission->notes)
> {{ $submission->notes }}
@endif

<x-mail::button :url="route('appointments.show', $submission)">Traiter la demande</x-mail::button>
@else
# Nouveau message du site

**De :** {{ $submission->name }} ({{ $submission->email }}{{ $submission->phone ? ', ' . $submission->phone : '' }})<br>
**Objet :** {{ $submission->subject ?: '—' }}

<x-mail::panel>
{{ $submission->body }}
</x-mail::panel>

<x-mail::button :url="route('messages.show', $submission)">Lire dans l'admin</x-mail::button>
@endif

Répondre à cet e-mail écrit directement à l'expéditeur.
</x-mail::message>
