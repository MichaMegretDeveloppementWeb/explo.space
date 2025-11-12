{{-- Breadcrumb --}}
<nav class="bg-white border-b border-gray-100" aria-label="Breadcrumb">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <ol class="flex items-center justify-center space-x-2 text-sm">
            <li>
                <a href="{{ localRoute('home') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    {{ __('web/common.breadcrumb.home') }}
                </a>
            </li>
            <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </li>
            <li>
                <span class="text-gray-900 font-medium">
                    {{ __('web/pages/legal.breadcrumb.current') }}
                </span>
            </li>
        </ol>
    </div>
</nav>
