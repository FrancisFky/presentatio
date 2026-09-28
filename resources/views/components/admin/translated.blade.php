{{--
    Un champ traduit : une saisie par langue, seule celle de l'onglet actif est visible.
    <x-admin.translated name="title" label="Titre" :model="$news" required />
    `required` ne s'applique qu'au français (langue par défaut du site).
--}}
@props([
    'name',
    'label',
    'model' => null,
    'type' => 'text',
    'required' => false,
    'help' => null,
    'rows' => 4,
    'maxlength' => null,
    'values' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @foreach (\App\Support\Locales::SUPPORTED as $locale)
        @php
            $field = "{$name}_{$locale}";
            $value = old($field, $values[$field] ?? $model?->{$field});
            $isRequired = $required && $locale === \App\Support\Locales::DEFAULT;
            $error = $errors->first($field);
        @endphp
        <div x-show="lang === '{{ $locale }}'" @if ($locale !== 'fr') x-cloak @endif class="space-y-2">
            <label for="{{ $field }}" class="flex items-center gap-2 text-sm font-medium text-gray-900">
                {{ $label }}
                <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">{{ $locale }}</span>
                @if ($isRequired)<span class="text-red-500">*</span>@endif
            </label>

            @if ($type === 'rich')
                <x-admin.rich-editor :name="$field" :value="$value" />
            @elseif ($type === 'textarea')
                <textarea name="{{ $field }}" id="{{ $field }}" rows="{{ $rows }}" @if ($isRequired) required @endif @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                    class="block w-full rounded-lg border-0 px-4 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset {{ $error ? 'ring-red-500' : 'ring-gray-300' }} focus:ring-2 focus:ring-inset focus:ring-brand-500">{{ $value }}</textarea>
            @else
                <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ $value }}" @if ($isRequired) required @endif @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                    class="block h-11 w-full rounded-lg border-0 px-4 text-sm text-gray-900 shadow-sm ring-1 ring-inset {{ $error ? 'ring-red-500' : 'ring-gray-300' }} focus:ring-2 focus:ring-inset focus:ring-brand-500">
            @endif

            @if ($error)<p class="text-sm text-red-600">{{ $error }}</p>@endif
        </div>
    @endforeach
    @if ($help)<p class="text-sm text-gray-500">{{ $help }}</p>@endif
</div>
