@props([
'title' => '',
'subtitle' => '',
'footer' => null,
'showContent' => true,
'py' => 'py-4',
'px' => 'px-4',
'isFoldable' => false,
'isFolded' => false,
'class' => '',
'bodyBg' => 'bg-white',
])

<div class="bg-white flex flex-col rounded shadow border border-gray-300 {{ $class }}" x-data="{ isFolded: @js($isFolded) }">
    <!-- Header -->
    @if ($title || $subtitle)
    <div class="{{ $py }} {{ $px }} border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between">
        
        <div class="flex items-center justify-between">
            <div>
                @if($title)
                <h2 class="text-lg font-semibold text-gray-800">{!! $title !!}</h2>
                @endif
                @if($subtitle)
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $subtitle }}</p>
                @endif
            </div>

            <!-- Collapse Toggle Button -->
            @if($isFoldable)
            <button 
                @click="isFolded = !isFolded" 
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                :aria-expanded="!isFolded"
            >
                <svg 
                    class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                    :class="{ 'rotate-180': isFolded }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            @endif
        </div>

        <!-- Named slot "action" for header actions -->
        @isset($action)
        <div class="mt-4 lg:mt-0">
            {{ $action }}
        </div>
        @endisset
    </div>
    @endif

    @isset($header)
    <div class="p-6 border-b border-gray-200 mb-4 transition-all duration-200 ease-in-out">
        {{ $header }}
    </div>
    @endisset

    <!-- Body -->
    @if ($showContent)
    <div 
        class="p-4 sm:p-6 flex-1 min-w-0 transition-all duration-200 ease-in-out {{ $bodyBg }}"
        x-show="!isFolded"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
    >
        {{ $slot }}
    </div>
    @endif

    <!-- Footer -->
    @isset($footer)
    <div 
        class="p-4 sm:p-6 border-t border-gray-200 transition-all duration-200 ease-in-out"
        x-show="!isFolded"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
    >
        {{ $footer }}
    </div>
    @endisset
</div>