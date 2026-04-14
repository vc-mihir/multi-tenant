<header class="sticky top-0 z-40 flex items-center justify-between h-20 px-6 bg-white/80 border-b border-teal-100 backdrop-blur-md">
    <!-- Left: Search Bar -->
    <div class="flex flex-1">
        <form action="#" method="GET" class="w-full max-w-sm ml-0 lg:ml-4">
            <div class="relative text-slate-400 focus-within:text-teal-600">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="search" 
                    id="dashboard-search"
                    class="block w-full py-2.5 pl-10 pr-4 text-sm bg-teal-50/50 border border-teal-100/50 rounded-2xl outline-none focus:ring-4 focus:ring-teal-100 focus:border-teal-400 transition-all duration-200"
                    placeholder="Search anything...">
            </div>
        </form>
    </div>

    <!-- Right: Profile & Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notifications (Static) -->
        <button class="p-2.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-xl transition-all duration-200 relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-2 right-2.5 block w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
        </button>

        <div class="h-8 w-px bg-slate-200 mx-2"></div>

        <!-- User Logout Form -->
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" 
                class="flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-600/20 transition-all duration-200 group">
                <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</header>
