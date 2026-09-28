{{--
    Champ de formulaire accessible : libellé relié, aide et erreur annoncées
    par aria-describedby. `as` : input, select ou textarea (options dans le slot).
    L'erreur est passée explicitement (:error) : le composant n'hérite pas de $errors.
--}}
@props(['name', 'label', 'as' => 'input', 'type' => 'text', 'required' => false, 'hint' => null, 'error' => null, 'id' => null])
@php
    $id ??= 'f-' . str_replace('_', '-', $name);
    $describedBy = trim(($hint ? "{$id}-hint " : '') . ($error ? "{$id}-error" : ''));
    $common = $attributes->merge([
        'id' => $id,
        'name' => $name,
        'aria-invalid' => $error ? 'true' : 'false',
        'aria-describedby' => $describedBy ?: null,
        'aria-required' => $required ? 'true' : null,
        'class' => 'site-input',
    ]);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-brand-700">
        {{ $label }}
        @if ($required)
            <span class="text-flag-red" aria-hidden="true">*</span>
        @else
            <span class="text-xs font-normal text-slate-500">({{ __('site.forms.optional') }})</span>
        @endif
    </label>

    @if ($as === 'select')
        <select {{ $common }}>{{ $slot }}</select>
    @elseif ($as === 'textarea')
        <textarea {{ $common->merge(['rows' => 5]) }}></textarea>
    @else
        <input type="{{ $type }}" {{ $common }}>
    @endif

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @if ($error)
        <p id="{{ $id }}-error" class="mt-1.5 flex items-start gap-1.5 text-sm text-red-700">
            <i class="ph-fill ph-warning-circle mt-0.5" aria-hidden="true"></i>{{ $error }}
        </p>
    @endif
</div>
