<header class="bg-white border-b border-gray-100 px-6 h-16 flex items-center justify-between shrink-0">

    <div>
        <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
    </div>

    <div class="flex items-center gap-3">
        {{-- Online Status --}}
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Online</span>
        </div>

        {{-- Subdomain --}}
        <div class="hidden sm:block px-3 py-1.5 bg-indigo-50 border border-indigo-100 rounded-lg">
            <span class="text-[11px] font-semibold text-indigo-600">{{ Auth::guard('company')->user()->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</span>
        </div>

        <div class="w-px h-6 bg-gray-200"></div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('tenant.admin.logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-red-500 rounded-xl hover:bg-red-600 active:scale-95 transition-all shadow-lg shadow-red-200 group">
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>

</header>
