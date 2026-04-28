<aside id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 translate-x-0 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
    <div class="flex flex-col h-full bg-teal-900 text-white shadow-2xl border-r border-teal-800/50 backdrop-blur-xl">
        <div class="flex items-center justify-center h-20 border-b border-teal-800/50 bg-teal-950/30">
            <span class="text-2xl font-bold tracking-wider text-teal-400">
                ADMIN<span class="text-white">PORTAL</span>
            </span>
        </div>

        <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-xl group {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/30' : 'text-teal-300 hover:bg-teal-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 transition-colors duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <div class="pt-4 pb-2 text-[10px] font-bold tracking-[0.2em] text-teal-500 uppercase px-4">
                Management
            </div>

            <a href="{{ route('admin.companies.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-xl group {{ request()->routeIs('admin.companies.*') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/30' : 'text-teal-300 hover:bg-teal-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Companies
            </a>

            {{-- <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-teal-300 transition-all duration-200 rounded-xl group hover:bg-teal-800/50 hover:text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Users
            </a> --}}

            <div class="pt-4 pb-2 text-[10px] font-bold tracking-[0.2em] text-teal-500 uppercase px-4">
                Settings
            </div>

            <a href="{{ route('admin.settings') }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-xl group {{ request()->routeIs('admin.settings') ? 'bg-teal-600 text-white shadow-lg shadow-teal-600/30' : 'text-teal-300 hover:bg-teal-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                General Settings
            </a>
        </nav>

        <div class="p-4 border-t border-teal-800/50 bg-teal-950/20">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-teal-600 text-xs font-bold text-white uppercase shadow-inner">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <span class="text-[10px] text-teal-400 uppercase tracking-tighter">Super Admin</span>
                </div>
            </div>
        </div>
    </div>
</aside>
