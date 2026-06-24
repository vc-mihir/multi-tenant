<x-layouts.auth-theme page-id="central-auth-provisioning">
    <div
        id="provisioning"
        class="space-y-6 text-center"
        data-status-url="{{ $statusUrl }}"
        data-tenant-url="{{ $tenantUrl }}"
        data-fallback-url="{{ $fallbackUrl }}"
    >
        <div class="flex justify-center">
            <span class="inline-block w-12 h-12 rounded-full border-4 border-slate-200 border-t-[#DD7F61] animate-spin"></span>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Setting up your workspace</h2>
            <p class="mt-3 text-sm text-slate-600">
                Your account is verified. We're preparing your dedicated environment &mdash; this only takes a few seconds.
            </p>
        </div>
    </div>
</x-layouts.auth-theme>
