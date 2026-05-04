<aside class="w-64 flex flex-col shrink-0" style="background-color: #0f172a; border-right: 1px solid #1e293b;">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 py-8">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-lg"
            style="background-color: #10b981; shadow-color: rgba(16, 185, 129, 0.4);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
        </div>
        <div>
            <p class="text-lg font-bold text-white leading-none tracking-tight">TenantHub</p>
            <p class="text-[10px] text-emerald-400 uppercase tracking-widest mt-1 font-bold">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">

        <a href="{{ route('tenant.admin.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all"
            style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <a href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Users
        </a>

        <a href="{{ route('tenant.admin.profile') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all {{ request()->routeIs('tenant.admin.profile') ? 'font-bold text-emerald-400 bg-slate-800' : 'font-medium text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>

    </nav>

    {{-- User Info --}}
    <div class="p-6 border-t border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-inner"
                style="background-color: #10b981;">
                {{ strtoupper(substr(Auth::guard('company')->user()->company_name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate leading-none">
                    {{ Auth::guard('company')->user()->company_name }}</p>
                <p class="text-[10px] text-emerald-400 font-medium mt-1 uppercase">Administrator</p>
            </div>
        </div>
    </div>

</aside>
