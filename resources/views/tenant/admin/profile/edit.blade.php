@extends('layouts.tenant-admin')

@section('title', 'Company Profile')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your organization profile and security')

@section('page-id', 'tenant-admin-profile-edit')

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

                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100 mx-1">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wide">Leave password
                                    blank to keep current</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-1.5">
                                <label for="password" class="text-xs font-semibold text-slate-500 ml-1">New
                                    Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password"
                                        class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                                        placeholder="Leave blank to keep current">
                                    <button type="button" onclick="togglePasswordVisibility('password', this)"
                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-emerald-500 transition-colors duration-200"
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
                                @error('password')
                                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="password_confirmation"
                                    class="text-xs font-semibold text-slate-500 ml-1">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="w-full bg-white border-slate-200 rounded-lg px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                                        placeholder="Confirm new password">
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-emerald-500 transition-colors duration-200"
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

        {{-- Danger Zone --}}
        <div class="mt-8 bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-red-50 bg-red-50/30">
                <h2 class="text-sm font-bold text-red-800 uppercase tracking-tight">Danger Zone</h2>
            </div>
            <div class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Delete Organization Account</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-xl">
                        Permanently delete your company account, subdomain, and all associated data. This action will
                        completely drop your isolated database and cannot be undone.
                    </p>
                </div>
                <form id="delete-account-form" action="{{ route('tenant.admin.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="delete-account-btn"
                        class="px-5 py-2.5 bg-white text-red-600 border border-red-200 rounded-lg text-sm font-bold shadow-sm transition-all active:scale-95 whitespace-nowrap">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
