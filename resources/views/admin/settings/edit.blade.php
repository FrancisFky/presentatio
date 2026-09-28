@extends('layouts.app')
@section('title', $definition['title'] . ' - Admin Ambassade')

@php
    $hasTranslated = collect($definition['sections'])->flatten(1)->contains(fn ($field) => $field['translated'] ?? false);
@endphp

@section('content')
<x-admin.header :title="$definition['title']" :subtitle="$definition['intro'] ?? null" :items="['Tableau de bord' => route('dashboard'), $definition['title'] => null]" />

<form method="POST" action="{{ route('settings.update', $group) }}" enctype="multipart/form-data" x-data="{ lang: 'fr' }" class="max-w-4xl space-y-6">
    @csrf
    @method('PUT')

    @if ($hasTranslated)<x-admin.lang-tabs />@endif

    @foreach ($definition['sections'] as $sectionTitle => $fields)
        <x-admin.panel :title="$sectionTitle">
            @foreach ($fields as $name => $field)
                @if ($field['translated'] ?? false)
                    <x-admin.translated :name="$name" :label="$field['label']" :type="$field['type'] === 'rich' ? 'rich' : ($field['type'] === 'textarea' ? 'textarea' : 'text')"
                        :values="$values" :help="$field['help'] ?? null" rows="3" />
                @elseif ($field['type'] === 'image')
                    <x-admin.image-field :name="$name" :label="$field['label']" :path="$values[$name] ?? null" :help="$field['help'] ?? null" />
                @elseif ($field['type'] === 'boolean')
                    <x-admin.toggle :name="$name" :label="$field['label']" :checked="($values[$name] ?? '1') === '1'" :help="$field['help'] ?? null" />
                @elseif ($field['type'] === 'textarea' || $field['type'] === 'rich')
                    <div class="space-y-2">
                        <label for="{{ $name }}" class="block text-sm font-medium text-gray-900">{{ $field['label'] }}</label>
                        @if ($field['type'] === 'rich')
                            <x-admin.rich-editor :name="$name" :value="old($name, $values[$name] ?? null)" />
                        @else
                            <textarea name="{{ $name }}" id="{{ $name }}" rows="3" class="block w-full rounded-lg border-0 px-4 py-2.5 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500">{{ old($name, $values[$name] ?? null) }}</textarea>
                        @endif
                        @if (!empty($field['help']))<p class="text-sm text-gray-500">{{ $field['help'] }}</p>@endif
                        @error($name)<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @else
                    <x-admin.field :name="$name" :label="$field['label']" :type="$field['type']" :value="$values[$name] ?? null" :help="$field['help'] ?? null" />
                @endif
            @endforeach
        </x-admin.panel>
    @endforeach

    <div class="sticky bottom-4 flex justify-end">
        <x-button type="submit" class="shadow-lg"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer les modifications</x-button>
    </div>
</form>
@endsection
