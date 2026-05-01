<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Welcome Back</h2>
        <p class="mt-2 text-slate-500 font-medium">Please enter your credentials to log in.</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-sm font-bold text-emerald-800 animate-in fade-in slide-in-from-top-4 duration-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label for="email" class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="you@company.com">
            @if ($errors->has('email'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between ml-1">
                <label for="password" class="text-sm font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-[#DD7F61] hover:text-[#D16A4E] transition-colors" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="••••••••••••">
            @if ($errors->has('password'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div class="flex items-center ml-1">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" 
                    class="rounded-lg border-slate-300 text-[#DD7F61] shadow-sm focus:ring-[#DD7F61] focus:ring-offset-0 transition-all">
                <span class="ms-3 text-sm font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Remember me</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="w-full py-5 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2">
                <span>Log In</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</x-layouts.auth-theme>
