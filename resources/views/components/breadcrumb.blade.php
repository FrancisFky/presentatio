@props([
'title' => '',
'items' => [], // ['Dashboard' => '/dashboard', 'Technician' => null]
])
<x-card>
    <div class="w-full">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $title }}
                </h1>
                <div class="mt-4 flex md:mt-0">
                    <nav class="text-sm text-gray-600" aria-label="Breadcrumb">
                        <ol class="flex items-center">
                            @foreach ($items as $label => $url)
                            <li class="flex items-center">
                                @if (!$loop->first)
                                <i class="ph ph-caret-right text-gray-400"></i>
                                @endif
                                
                                @if ($url)
                                <a href="{{ $url }}" class="inline-flex items-center {{ $loop->first ? 'pe-3' : 'px-3' }} py-1.5 text-brand-600 font-medium transition">
                                    {{ Str::limit($label, 20) }}
                                </a>
                                @else
                                <span class="inline-flex items-center px-3 py-1.5 text-gray-700 font-medium transition">
                                    {{ Str::limit($label, 35) }}
                                </span>
                                @endif
                            </li>
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Named slot "action" for header actions -->
            @isset($action)
            {{ $action }}
            @endisset
        </div>
    </div>
</x-card>