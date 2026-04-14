<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Create Account</h2>
        <p class="mt-2 text-slate-500 font-medium">Join us and start managing your company.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="text-sm font-bold text-slate-700 ml-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="John Doe">
            @if ($errors->has('name'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-bold text-slate-700 ml-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="username"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="john@example.com">
            @if ($errors->has('email'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-sm font-bold text-slate-700 ml-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="••••••••••••">
            @if ($errors->has('password'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="text-sm font-bold text-slate-700 ml-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="••••••••••••">
            @if ($errors->has('password_confirmation'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4">
            <a class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors"
                href="{{ route('login') }}">
                Already registered?
            </a>

            <button type="submit"
                class="px-10 py-4 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300">
                Register
            </button>
        </div>
    </form>
</x-layouts.auth-theme>
