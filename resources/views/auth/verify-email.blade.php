<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Check your email</h2>
        <p class="mt-4 text-sm text-slate-500 font-medium leading-relaxed italic">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you?
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-sm font-bold text-emerald-800 animate-in fade-in slide-in-from-top-4 duration-300">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-5 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit"
                class="text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors uppercase tracking-widest">
                Log Out
            </button>
        </form>
    </div>
</x-layouts.auth-theme>
