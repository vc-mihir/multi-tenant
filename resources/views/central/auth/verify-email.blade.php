<x-layouts.auth-theme>
    <div class="space-y-5">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-slate-900">Verify your company email</h2>
            <p class="mt-3 text-sm text-slate-600">
                We sent a verification link to {{ $company->company_email }}. Please click it to activate your company
                account.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Verification link sent successfully.
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('companies.verification.send', ['id' => $company->id]) }}">
            @csrf
            <button type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Resend verification email
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to login</a>
        </div>
    </div>
</x-layouts.auth-theme>
