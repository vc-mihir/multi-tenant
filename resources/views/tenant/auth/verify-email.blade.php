<x-layouts.auth-theme>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#DD7F61]/10 mb-4">
            <svg class="w-8 h-8 text-[#DD7F61]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 leading-tight">Verify Your Email</h2>
        <p class="mt-2 text-sm text-slate-500 font-medium leading-relaxed">
            @if (session('email_changed'))
                Your email address has been updated. Please verify your new email address by clicking on the link we just sent to you.
            @else
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
            @endif
        </p>
    </div>

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-3 bg-[#DD7F61] text-white font-black rounded-xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2 text-sm">
                <span>Resend Verification Email</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </form>

        <form method="GET" action="{{ route('tenant.index') }}">
            @csrf
            <button type="submit"
                class="w-full py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 active:scale-[0.98] transition-all duration-300 text-sm">
                Log Out
            </button>
        </form>
    </div>
</x-layouts.auth-theme>
