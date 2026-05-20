<x-layouts.auth-theme page-id="tenant-auth-login">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Welcome Back</h2>
        <p class="mt-2 text-slate-500 font-medium">Please enter your credentials to log in.</p>
    </div>


    <form method="POST" action="{{ route('tenant.login.post') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label for="email" class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username"
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
                    <a class="text-xs font-bold text-[#DD7F61] hover:text-[#D16A4E] transition-colors"
                        href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-5 py-4 pr-11 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
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
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div class="flex items-center ml-1">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded-lg border-slate-300 text-[#DD7F61] shadow-sm focus:ring-[#DD7F61] focus:ring-offset-0 transition-all">
                <span
                    class="ms-3 text-sm font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Remember
                    me</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="w-full py-5 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2">
                <span>Log In</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

        <div class="text-center pt-6">
            <p class="text-sm font-bold text-slate-500">
                Don't have an account?
                <a class="text-[#DD7F61] hover:text-[#D16A4E] transition-colors"
                    href="{{ route('tenant.register') }}">Register</a>
            </p>
        </div>
    </form>
</x-layouts.auth-theme>
