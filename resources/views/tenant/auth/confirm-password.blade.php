<x-layouts.auth-theme>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">Confirm Password</h2>
        <p class="mt-4 text-sm text-slate-500 font-medium leading-relaxed">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label for="password" class="text-sm font-bold text-slate-700 ml-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                placeholder="••••••••••••">
            @if ($errors->has('password'))
                <p class="mt-2 text-xs font-bold text-red-600 ml-1">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit"
                class="w-full py-5 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300">
                Confirm
            </button>
        </div>
    </form>
</x-layouts.auth-theme>
