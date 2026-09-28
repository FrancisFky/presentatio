{{-- Niveau d'un communiqué : l'urgent en rouge, l'important en or --}}
@props(['priority' => 'normal', 'pinned' => false])

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold tracking-wider uppercase',
    'bg-red-50 text-red-700 ring-1 ring-red-200' => $priority === 'urgent',
    'bg-gold-50 text-gold-700 ring-1 ring-gold-200' => $priority === 'important',
    'bg-brand-50 text-brand-600 ring-1 ring-brand-100' => ! in_array($priority, ['urgent', 'important']),
]) }}>
    <i @class([
        'ph-fill',
        'ph-warning-octagon' => $priority === 'urgent',
        'ph-warning' => $priority === 'important',
        'ph-push-pin' => $priority === 'normal' && $pinned,
        'ph-megaphone-simple' => $priority === 'normal' && ! $pinned,
    ]) aria-hidden="true"></i>
    {{ __('site.announcements.priority.' . ($priority === 'normal' && $pinned ? 'pinned' : $priority)) }}
</span>
