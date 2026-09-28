@props([
    'currentStep' => 1,
    'totalSteps' => 8,
    'merchant' => null,
    'steps' => []
])

@php
    // Define the steps with their titles and route names
    $defaultSteps = [
        1 => [
            'title' => 'Informations de base',
            'route' => 'dashboard.merchants.step1',
            'icon' => 'ph ph-info'
        ],
        2 => [
            'title' => 'Activités',
            'route' => 'dashboard.merchants.step2',
            'icon' => 'ph ph-clock'
        ],
        3 => [
            'title' => 'Contacts',
            'route' => 'dashboard.merchants.step3',
            'icon' => 'ph ph-phone'
        ],
        4 => [
            'title' => 'Localisation',
            'route' => 'dashboard.merchants.step4',
            'icon' => 'ph ph-map-pin'
        ],
        5 => [
            'title' => 'Coordonnées géographiques',
            'route' => 'dashboard.merchants.step5',
            'icon' => 'ph ph-globe'
        ],
        6 => [
            'title' => 'Logo',
            'route' => 'dashboard.merchants.step6',
            'icon' => 'ph ph-image'
        ],
        7 => [
            'title' => 'Documents administratifs',
            'route' => 'dashboard.merchants.step7',
            'icon' => 'ph ph-file-text'
        ],
        8 => [
            'title' => 'Récapitulatif',
            'route' => 'dashboard.merchants.step8',
            'icon' => 'ph ph-check-circle'
        ]
    ];

    // Use provided steps or default steps
    $stepDefinitions = empty($steps) ? $defaultSteps : $steps;
    
    // Filter steps based on totalSteps
    $visibleSteps = array_slice($stepDefinitions, 0, $totalSteps, true);
@endphp

<x-basics.card>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Progression du formulaire</h3>
        <span class="text-sm text-gray-500">Étape {{ $currentStep }} sur {{ $totalSteps }}</span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-1 mb-6">
        <div class="bg-green-600 h-1 rounded-full transition-all duration-300" 
             style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
    </div>

    <!-- Steps Navigation - Horizontal Layout -->
    <div class="flex flex-wrap gap-2">
        @foreach($visibleSteps as $stepNumber => $step)
            @php
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent = $stepNumber == $currentStep;
                $isUpcoming = $stepNumber > $currentStep;
                
                // Determine if the step is clickable (completed or current)
                $isClickable = $isCompleted || $isCurrent;
                
                // Base classes for the step item - now compact for horizontal layout
                $stepClasses = 'flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm ';
                
                if ($isClickable) {
                    $stepClasses .= 'hover:shadow-md cursor-pointer ';
                } else {
                    $stepClasses .= 'cursor-not-allowed ';
                }
                
                if ($isCurrent) {
                    $stepClasses .= 'bg-brand-600 text-white shadow-md ';
                } elseif ($isCompleted) {
                    $stepClasses .= 'bg-green-600 text-white shadow-sm ';
                } else {
                    $stepClasses .= 'bg-gray-100 text-gray-500 ';
                }
                
                // Icon classes - smaller for horizontal layout
                $iconClasses = 'text-base mr-2 ';
                if ($isCurrent || $isCompleted) {
                    $iconClasses .= 'text-white';
                } else {
                    $iconClasses .= 'text-gray-400';
                }
            @endphp
            
            @if($isClickable && $merchant)
                <a href="{{ route($step['route'], $merchant) }}" 
                   class="{{ $stepClasses }}"
                   title="{{ $step['title'] }} - Étape {{ $stepNumber }}">
                    @if($isCompleted)
                        <i class="ph ph-check-circle {{ $iconClasses }}"></i>
                    @else
                        <span class="w-5 h-5 rounded-full bg-current flex items-center justify-center text-xs font-bold mr-2">
                            {{ $stepNumber }}
                        </span>
                    @endif
                    
                    <span class="font-medium whitespace-nowrap">
                        {{ $step['title'] }}
                    </span>
                </a>
            @else
                <div class="{{ $stepClasses }}"
                     title="{{ $step['title'] }} - Étape {{ $stepNumber }}">
                    <span class="w-5 h-5 rounded-full bg-current flex items-center justify-center text-xs font-bold mr-2">
                        {{ $stepNumber }}
                    </span>
                    
                    <span class="font-medium whitespace-nowrap">
                        {{ $step['title'] }}
                    </span>
                </div>
            @endif
            
            <!-- Connector line between steps (except for last step) -->
            @if(!$loop->last)
                <div class="flex items-center">
                    <div class="w-4 h-px {{ $stepNumber < $currentStep ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                </div>
            @endif
        @endforeach
    </div>
</x-basics.card>
