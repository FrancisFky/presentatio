@props([
    // The <input> name attribute, e.g. "document" or "documents_one"
    'name',

    // Optional label shown above the file input
    'label' => '',

    // Help text shown below the label / above the input
    'help' => 'PDF, DOCX, or PPTX (Max. 10 MB)',
])

<div {{ $attributes->merge(['class' => 'max-w-3xl mx-auto']) }}>
    {{-- Optional Label --}}
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Optional Help Text --}}
    @if($help)
        <p class="text-xs text-gray-500 mb-1">{{ $help }}</p>
    @endif

    <div class="mb-4">
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $name }}"
            accept=".pdf,.docx,.pptx"
            class="w-full px-4 py-2 border-0 rounded-lg shadow-sm ring-1 ring-inset ring-gray-300
                   focus:outline-none focus:ring-1 focus:ring-inset focus:ring-brand-600
                   transition-all duration-200"
        />

        @error($name)
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
