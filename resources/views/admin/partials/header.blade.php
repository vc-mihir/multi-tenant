<header
    class="sticky top-0 z-40 flex items-center justify-between h-20 px-6 bg-white/80 border-b border-teal-100 backdrop-blur-md">
    <div class="flex flex-1">
        <form action="#" method="GET" class="w-full max-w-sm ml-0 lg:ml-4 relative" id="search-form">
            <div class="relative text-slate-400 focus-within:text-teal-600">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="search" id="dashboard-search" autocomplete="off"
                    class="block w-full py-2.5 pl-10 pr-4 text-sm bg-teal-50/50 border border-teal-100/50 rounded-2xl outline-none focus:ring-4 focus:ring-teal-100 focus:border-teal-400 transition-all duration-200"
                    placeholder="Search anything...">
            </div>

            <!-- Search Results Dropdown -->
            <div id="search-results"
                class="absolute left-0 right-0 mt-2 bg-white border border-teal-100 rounded-2xl shadow-xl overflow-hidden hidden z-50">
                <div class="p-2" id="search-results-content">
                    <!-- Results -->
                </div>
            </div>
        </form>
    </div>

    <div class="flex items-center space-x-4">

        <div class="h-8 w-px bg-slate-200 mx-2"></div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-600/20 transition-all duration-200 group">
                <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</header>
