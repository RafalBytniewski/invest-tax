@if ($paginator->hasPages())
    <nav class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <p class="text-sm text-gray-500 dark:text-zinc-400">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium text-gray-700 dark:text-zinc-200">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium text-gray-700 dark:text-zinc-200">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-medium text-gray-700 dark:text-zinc-200">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex items-center justify-between gap-2 sm:justify-end">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm font-medium text-gray-400 dark:border-zinc-800 dark:bg-zinc-950/40 dark:text-zinc-600" aria-disabled="true">
                    <span class="sm:hidden">{{ __('pagination.previous') }}</span>
                    <svg class="hidden size-4 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="inline-flex h-9 items-center rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-600 transition hover:border-gray-400 hover:bg-gray-50 hover:text-gray-900 focus:outline-hidden focus:ring-2 focus:ring-gray-400/40 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-white">
                    <span class="sm:hidden">{{ __('pagination.previous') }}</span>
                    <svg class="hidden size-4 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex size-9 items-center justify-center text-sm text-gray-500 dark:text-zinc-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex size-9 items-center justify-center rounded-lg bg-gray-900 text-sm font-semibold text-white dark:bg-zinc-100 dark:text-zinc-900" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="inline-flex size-9 items-center justify-center rounded-lg text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-hidden focus:ring-2 focus:ring-gray-400/40 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="inline-flex h-9 items-center rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-600 transition hover:border-gray-400 hover:bg-gray-50 hover:text-gray-900 focus:outline-hidden focus:ring-2 focus:ring-gray-400/40 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-white">
                    <span class="sm:hidden">{{ __('pagination.next') }}</span>
                    <svg class="hidden size-4 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="inline-flex h-9 items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm font-medium text-gray-400 dark:border-zinc-800 dark:bg-zinc-950/40 dark:text-zinc-600" aria-disabled="true">
                    <span class="sm:hidden">{{ __('pagination.next') }}</span>
                    <svg class="hidden size-4 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
