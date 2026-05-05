<x-layouts.auth-theme>
    <div class="space-y-5">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-slate-900">Verify your company email</h2>
            <p class="mt-3 text-sm text-slate-600">
                We sent a verification link to {{ $company->company_email }}. Please click it to activate your company
                account.
            </p>
        </div>


        <form method="POST" action="{{ route('companies.verification.send', ['id' => $company->id]) }}">
            @csrf
            <button type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Resend verification email
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('register') }}"
                class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-[#DD7F61] transition-colors">
                Back to registration
            </a>
        </div>
    </div>
</x-layouts.auth-theme>
