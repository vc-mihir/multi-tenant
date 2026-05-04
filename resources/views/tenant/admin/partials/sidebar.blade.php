<aside class="t-sidebar w-60 flex flex-col shrink-0">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/8">
        <div class="w-8 h-8 rounded-xl bg-indigo-500 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-white leading-none">TenantHub</p>
            <p class="text-[10px] text-indigo-300 uppercase tracking-widest mt-0.5">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto t-scroll">

        <p class="text-[10px] font-bold uppercase tracking-widest text-white/25 px-3 mb-2">Main</p>

        <a href="{{ route('tenant.admin.dashboard') }}"
           class="t-nav-active flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-sm font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="#" class="t-nav-item flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Team Members
        </a>

        <a href="#" class="t-nav-item flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Reports
        </a>

        <a href="#" class="t-nav-item flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
            Database
        </a>

        <p class="text-[10px] font-bold uppercase tracking-widest text-white/25 px-3 pt-5 pb-2">Config</p>

        <a href="#" class="t-nav-item flex items-center gap-3 px-3 py-2.5 rounded-r-xl text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Settings
        </a>

    </nav>

    {{-- User Info --}}
    <div class="p-3 border-t border-white/8">
        <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/6">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-xs font-bold text-white shrink-0">
                {{ strtoupper(substr(Auth::guard('company')->user()->company_name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate leading-none">{{ Auth::guard('company')->user()->company_name }}</p>
                <p class="text-[10px] text-indigo-300 mt-0.5">Tenant Admin</p>
            </div>
        </div>
    </div>

</aside>
