<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4 animate-bounce">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">User Dashboard</h2>
        <p class="mt-2 text-slate-500 font-medium">Welcome back, {{ Auth::guard('tenant_user')->user()->name }}!</p>
    </div>

    <div class="space-y-6">
        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
            <p class="text-slate-600 font-medium italic">"You are successfully logged into your tenant account."</p>
        </div>

        <div class="pt-4">
            <form method="POST" action="{{ route('tenant.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full py-5 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:bg-slate-800 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth-theme>
