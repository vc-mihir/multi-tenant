<x-tenant.user.header page-id="tenant-user-dashboard">
    <x-slot:title>User Dashboard | {{ config('app.name') }}</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div
            class="relative h-64 rounded-[3rem] overflow-hidden bg-white shadow-2xl shadow-emerald-900/10 border border-emerald-50 mb-12">
            <div class="absolute inset-0 flex items-center justify-center">
                <h1 class="text-3xl font-black text-emerald-950 font-outfit uppercase tracking-widest opacity-20">
                    Workspace</h1>
            </div>
        </div>

        <!-- Quick Access Section -->
        <div class="grid grid-cols-1 gap-6 w-full">
            <div
                class="bg-white p-8 rounded-[2rem] border border-emerald-50 shadow-sm hover:shadow-xl transition-all duration-300 group flex items-center gap-8">
                <div
                    class="w-16 h-16 shrink-0 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-emerald-950 font-outfit mb-1">Project Hub</h3>
                    <p class="text-emerald-800/60 font-medium text-sm leading-relaxed">Access and manage all your active
                        projects and task boards in real-time.</p>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-[2rem] border border-emerald-50 shadow-sm hover:shadow-xl transition-all duration-300 group flex items-center gap-8">
                <div
                    class="w-16 h-16 shrink-0 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-emerald-950 font-outfit mb-1">Team Sync</h3>
                    <p class="text-emerald-800/60 font-medium text-sm leading-relaxed">Connect with your team members
                        and stay updated with recent collaborations.</p>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-[2rem] border border-emerald-50 shadow-sm hover:shadow-xl transition-all duration-300 group flex items-center gap-8">
                <div
                    class="w-16 h-16 shrink-0 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-emerald-950 font-outfit mb-1">Analytics</h3>
                    <p class="text-emerald-800/60 font-medium text-sm leading-relaxed">Track your performance and view
                        detailed insights into your workflow efficiency.</p>
                </div>
            </div>
        </div>
    </div>
</x-tenant.user.header>
