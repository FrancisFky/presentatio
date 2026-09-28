{{-- Éditeur Trix : le HTML est nettoyé côté serveur (App\Support\RichText) avant d'être enregistré --}}
@props(['name', 'value' => null])
@php $id = $name . '_input'; @endphp
<div>
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}">
    <trix-editor input="{{ $id }}" class="trix-content prose prose-sm max-w-none"></trix-editor>
</div>
