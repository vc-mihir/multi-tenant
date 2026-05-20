<x-layouts.auth-theme page-id="tenant-auth-register">
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
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full px-3 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                    placeholder="••••••••••••">
                <button type="button" onclick="togglePasswordVisibility('password', this)"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#DD7F61] transition-colors duration-200"
                    tabindex="-1">
                    <span class="relative block w-5 h-5">
                        <svg class="eye-open absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg class="eye-closed hidden absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </span>
                </button>
            </div>
            @if ($errors->has('password'))
                <p class="mt-1 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="text-sm font-bold text-slate-700 ml-1">Confirm Password</label>
            <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="w-full px-3 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 text-sm"
                    placeholder="••••••••••••">
                <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-[#DD7F61] transition-colors duration-200"
                    tabindex="-1">
                    <span class="relative block w-5 h-5">
                        <svg class="eye-open absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg class="eye-closed hidden absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </span>
                </button>
            </div>
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