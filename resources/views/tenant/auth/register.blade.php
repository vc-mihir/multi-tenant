<x-layouts.auth-theme>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-slate-900 leading-tight">Create Account</h2>
        <p class="mt-1 text-sm text-slate-500 font-medium">Register your account to get started.</p>
    </div>

    <form method="POST" action="{{ route('tenant.register.post') }}" class="space-y-3">
        @csrf

        <div class="space-y-1">
            <label for="name" class="text-sm font-bold text-slate-700 ml-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name"
                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                placeholder="John Doe">
            @if ($errors->has('name'))
                <p class="mt-1 text-xs font-bold text-red-600 ml-1">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div class="space-y-1">
            <label for="email" class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="username"
                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                placeholder="you@company.com">
            @if ($errors->has('email'))
                <p class="mt-1 text-xs font-bold text-red-600 ml-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="space-y-1">
            <label for="password" class="text-sm font-bold text-slate-700 ml-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                placeholder="••••••••••••">
            @if ($errors->has('password'))
                <p class="mt-1 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="text-sm font-bold text-slate-700 ml-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password"
                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                placeholder="••••••••••••">
            @if ($errors->has('password_confirmation'))
                <p class="mt-1 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full py-3 bg-[#DD7F61] text-white font-black rounded-xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2 text-sm">
                <span>Register</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

        <div class="text-center pt-2">
            <p class="text-sm font-bold text-slate-500">
                Already have an account?
                <a class="text-[#DD7F61] hover:text-[#D16A4E] transition-colors"
                    href="{{ route('tenant.login') }}">Log in</a>
            </p>
        </div>
    </form>
</x-layouts.auth-theme>