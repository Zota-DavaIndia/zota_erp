<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar dava-side-bar tw-relative tw-hidden tw-h-full tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0">

    <!-- Branded header -->
    <a href="{{route('home')}}" class="dava-side-bar-header tw-shrink-0" title="{{ Session::get('business.name') }}">
        <div class="dava-side-bar-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v9h18v-9c0-1.1-.9-2-2-2z" fill="#1F7A4D"/>
                <path d="M11 11h2v2h-2zm0 3h2v2h-2z" fill="#fff"/>
            </svg>
        </div>
        <div class="dava-side-bar-text">
            <div class="dava-side-bar-name">{{ Session::get('business.name') ?: 'DavaIndia' }}</div>
            <div class="dava-side-bar-tag">Generic Pharmacy</div>
        </div>
        <div class="dava-side-bar-status" title="Online"></div>
    </a>

    <!-- Sidebar Search -->
    <div class="dava-side-bar-search tw-shrink-0">
        <div class="dava-side-bar-search-inner">
            <svg class="tw-size-4 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                <path d="M21 21l-6 -6"/>
            </svg>
            <input type="text" id="sidebar-search" placeholder="Search menu..."
                class="tw-grow tw-min-w-0"
                autocomplete="off" />
            <button id="sidebar-search-clear" type="button" aria-label="Clear search"
                class="tw-hidden tw-shrink-0 tw-text-gray-400 hover:tw-text-gray-600 tw-transition-colors tw-duration-200">
                <svg class="tw-size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

    <!-- No results message -->
    <p id="sidebar-no-results" class="tw-hidden tw-px-4 tw-py-3 tw-text-xs tw-text-gray-400 tw-text-center">
        No menu items found.
    </p>

    <!-- Footer badge -->
    <div class="dava-side-bar-footer tw-shrink-0 tw-mt-auto">
        <span class="dava-side-bar-footer-badge">DavaIndia ERP v1.0</span>
    </div>
</aside>

