<header class="bg-white border-b border-gray-100 px-4 sm:px-6 h-16 flex items-center justify-between shrink-0">

    <div class="flex items-center gap-2 min-w-0">
        <button type="button" id="sidebar-toggle" aria-label="Open menu"
            class="-ml-1 flex-shrink-0 rounded-xl p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <p class="text-xs text-gray-400 truncate">{{ now()->format('l, d F Y') }}</p>
    </div>

    <div class="flex flex-shrink-0 items-center gap-4">
        {{-- Subdomain --}}
        <div class="hidden sm:block px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl">
            <span class="text-[11px] font-bold text-gray-600 tracking-tight">{{ Auth::guard('company')->user()->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</span>
        </div>

        <div class="hidden sm:block w-px h-6 bg-gray-200"></div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('tenant.admin.logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-4 sm:px-6 py-2.5 text-sm font-bold text-white rounded-xl transition-all shadow-lg active:scale-95 group"
                style="background-color: #FF2D20;">
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Sign Out</span>
            </button>
        </form>
    </div>

</header>
