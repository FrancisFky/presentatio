{{-- Suppression : visible seulement des administrateurs (les éditeurs ne suppriment pas) --}}
@props(['action', 'confirm' => 'Supprimer définitivement cet élément ?', 'label' => null, 'size' => 'xs'])
@if (auth('admin')->user()?->canManageAdmins())
    <form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" class="inline">
        @csrf
        @method('DELETE')
        <x-button type="submit" variant="ghost" :size="$size" title="Supprimer" class="text-red-600 hover:bg-red-50">
            <i class="ph ph-trash"></i>@if ($label)<span class="ml-1">{{ $label }}</span>@endif
        </x-button>
    </form>
@endif
