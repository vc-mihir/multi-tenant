<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Recover Password</h2>
        <p class="mt-4 text-sm text-slate-500 font-medium leading-relaxed">
            Forgot your password? No problem. Just let us know your email address and we'll send you a link to reset it.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-sm font-bold text-emerald-800 animate-in fade-in slide-in-from-top-4 duration-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="you@company.com">
            @if ($errors->has('email'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4">
            <a class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors" href="{{ route('login') }}">
                Back to Login
            </a>

            <button type="submit"
                class="px-8 py-4 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300">
                Email Reset Link
            </button>
        </div>
    </form>
</x-layouts.auth-theme>
