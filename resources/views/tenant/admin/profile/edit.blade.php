@extends('layouts.tenant-admin')

@section('title', 'Company Profile')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your organization profile and security')

@section('content')
    <div class="max-w-5xl">
        @if (session('success'))
            <div
                class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('tenant.admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Organization Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-tight">Organization Details</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="company_name" class="text-xs font-semibold text-slate-500 ml-1">Company Name</label>
                            <input type="text" name="company_name" id="company_name"
                                value="{{ old('company_name', $company->company_name) }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed outline-none"
                                readonly>
                            @error('company_name')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="company_email" class="text-xs font-semibold text-slate-500 ml-1">Contact
                                Email</label>
                            <input type="email" name="company_email" id="company_email"
                                value="{{ old('company_email', $company->company_email) }}"
                                class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                                required>
                            @error('company_email')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="website" class="text-xs font-semibold text-slate-500 ml-1">Website URL</label>
                            <input type="url" name="website" id="website"
                                value="{{ old('website', $company->website) }}" placeholder="https://example.com"
                                class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="license_number" class="text-xs font-semibold text-slate-500 ml-1">License
                                Number</label>
                            <input type="text" name="license_number" id="license_number"
                                value="{{ old('license_number', $company->license_number) }}"
                                class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                {{-- Address Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-tight">Location Information</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-1.5">
                            <label for="address" class="text-xs font-semibold text-slate-500 ml-1">Street Address</label>
                            <textarea name="address" id="address" rows="2"
                                class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none resize-none">{{ old('address', $company->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1.5">
                                <label for="city" class="text-xs font-semibold text-slate-500 ml-1">City</label>
                                <input type="text" name="city" id="city"
                                    value="{{ old('city', $company->city) }}"
                                    class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label for="state" class="text-xs font-semibold text-slate-500 ml-1">State</label>
                                <input type="text" name="state" id="state"
                                    value="{{ old('state', $company->state) }}"
                                    class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label for="country" class="text-xs font-semibold text-slate-500 ml-1">Country</label>
                                <input type="text" name="country" id="country"
                                    value="{{ old('country', $company->country) }}"
                                    class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-tight">Security & Credentials</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subdomain</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $company->subdomain }}.multi-tenant.test
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-1.5">
                                <label for="password" class="text-xs font-semibold text-slate-500 ml-1">New
                                    Password</label>
                                <input type="password" name="password" id="password"
                                    class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                                    placeholder="Leave blank to keep current">
                                @error('password')
                                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="password_confirmation"
                                    class="text-xs font-semibold text-slate-500 ml-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                                    placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('tenant.admin.dashboard') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold shadow-md shadow-emerald-600/10 transition-all active:scale-95">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
