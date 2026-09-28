{{-- Pagination aux couleurs du site ; libellés traduits --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('site.a11y.pagination') }}" class="mt-14 flex items-center justify-between gap-4 border-t border-brand-900/10 pt-6">
        @if ($paginator->onFirstPage())
            <span class="site-btn site-btn-outline pointer-events-none opacity-40" aria-hidden="true"><i class="ph-bold ph-arrow-left"></i><span class="hidden sm:inline">{{ __('site.pagination.previous') }}</span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="site-btn site-btn-outline"><i class="ph-bold ph-arrow-left" aria-hidden="true"></i><span class="hidden sm:inline">{{ __('site.pagination.previous') }}</span><span class="sr-only sm:hidden">{{ __('site.pagination.previous') }}</span></a>
        @endif

        <ul class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="px-2 text-slate-400" aria-hidden="true">…</li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="flex size-10 items-center justify-center rounded-md bg-brand-700 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex size-10 items-center justify-center rounded-md text-sm font-medium text-brand-600 hover:bg-brand-50" aria-label="{{ __('site.pagination.page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
        <p class="text-sm text-slate-500 sm:hidden">{{ __('site.pagination.status', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="site-btn site-btn-outline"><span class="hidden sm:inline">{{ __('site.pagination.next') }}</span><span class="sr-only sm:hidden">{{ __('site.pagination.next') }}</span><i class="ph-bold ph-arrow-right" aria-hidden="true"></i></a>
        @else
            <span class="site-btn site-btn-outline pointer-events-none opacity-40" aria-hidden="true"><span class="hidden sm:inline">{{ __('site.pagination.next') }}</span><i class="ph-bold ph-arrow-right"></i></span>
        @endif
    </nav>
@endif
