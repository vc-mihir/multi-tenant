<x-layouts.auth-theme page-id="central-auth-register">
    <div class="mb-10 px-2">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-100 -translate-y-1/2"></div>
            <div id="progress-line"
                class="absolute top-1/2 left-0 w-0 h-0.5 bg-[#DD7F61] -translate-y-1/2 transition-all duration-500">
            </div>

            <div class="relative z-10 flex flex-col items-center">
                <div id="step-1-dot"
                    class="w-8 h-8 rounded-full bg-[#DD7F61] text-white flex items-center justify-center text-xs font-black shadow-lg shadow-[#DD7F61]/20 transition-all duration-500">
                    1</div>
                <span class="mt-2 text-[10px] font-black uppercase tracking-tighter text-[#DD7F61]">Account</span>
            </div>
            <div class="relative z-10 flex flex-col items-center">
                <div id="step-2-dot"
                    class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-black transition-all duration-500">
                    2</div>
                <span id="step-2-label"
                    class="mt-2 text-[10px] font-black uppercase tracking-tighter text-slate-400">Company</span>
            </div>
            <div class="relative z-10 flex flex-col items-center">
                <div id="step-3-dot"
                    class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-black transition-all duration-500">
                    3</div>
                <span id="step-3-label"
                    class="mt-2 text-[10px] font-black uppercase tracking-tighter text-slate-400">Location</span>
            </div>
        </div>
    </div>

    @if (request()->query('account_deleted'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof window.showAlert === 'function') {
                    window.showAlert('success', 'Account Deleted',
                        'Your company account and data have been completely erased.');

                    // Clean up the `URL so the query parameter disappears instantly
                    const url = new URL(window.location);
                    url.searchParams.delete('account_deleted');
                    window.history.replaceState({}, '', url);
                }
            });
        </script>
    @endif

    <form method="POST" action="{{ route('register.store') }}" id="registration-form">
        @csrf

        <div id="phase-1" class="space-y-5 transition-all duration-500">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-900">Account Setup</h3>
                <p class="text-sm text-slate-500">Start by entering your company's primary login credentials.</p>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="company_name"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                            Name</label>
                        <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                            required autocomplete="off"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. Acme Corp">
                    </div>
                    <div class="space-y-1.5">
                        <label for="subdomain"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Subdomain</label>
                        <input id="subdomain" type="text" name="subdomain" value="{{ old('subdomain') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="acme-corp">
                    </div>
                </div>
                @error('subdomain')
                    <p class="text-[10px] font-bold text-red-500 ml-1">{{ $message }}</p>
                @enderror

                <div class="space-y-1.5">
                    <label for="company_email"
                        class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                        Email</label>
                    <input id="company_email" type="email" name="company_email" value="{{ old('company_email') }}"
                        required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                        placeholder="admin@co.com">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                class="w-full px-4 py-3.5 pr-11 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                                placeholder="••••••••">
                            <button type="button"
                                onclick="togglePasswordVisibility('password', this)"
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
                    </div>
                    <div class="space-y-1.5">
                        <label for="password_confirmation"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Confirm</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full px-4 py-3.5 pr-11 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                                placeholder="••••••••">
                            <button type="button"
                                onclick="togglePasswordVisibility('password_confirmation', this)"
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
                    </div>
                </div>
                @error('password')
                    <p class="text-[10px] font-bold text-red-500 ml-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div id="phase-2" class="hidden space-y-5 transition-all duration-500">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-900">Business Credentials</h3>
                <p class="text-sm text-slate-500">Tell us more about your organization.</p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="website"
                        class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Company Website</label>
                    <input id="website" type="url" name="website" value="{{ old('website') }}" required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                        placeholder="https://acme.com">
                </div>

                <div class="space-y-1.5">
                    <label for="license_number"
                        class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">License Number</label>
                    <input id="license_number" type="text" name="license_number" value="{{ old('license_number') }}"
                        required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                        placeholder="REG-123456">
                </div>
            </div>
        </div>

        <div id="phase-3" class="hidden space-y-4 transition-all duration-500">
            <div class="mb-4">
                <h3 class="text-xl font-bold text-slate-900">Business Location</h3>
                <p class="text-sm text-slate-500">Where is your primary office located?</p>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="address"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Address</label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300 resize-none">{{ old('address') }}</textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label for="city"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">City</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. SF">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="state"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">State</label>
                        <input id="state" type="text" name="state" value="{{ old('state') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. CA">
                    </div>
                    <div class="space-y-1.5">
                        <label for="country"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Country</label>
                        <input id="country" type="text" name="country" value="{{ old('country') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. USA">
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center justify-between pt-10">
            <button type="button" id="prev-btn"
                class="hidden text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
                Back
            </button>


            <button type="button" id="next-btn"
                class="px-10 py-5 bg-[#DD7F61] text-white font-black rounded-2xl shadow-xl shadow-[#DD7F61]/30 hover:bg-[#D16A4E] hover:shadow-[#DD7F61]/40 active:scale-[0.98] transition-all duration-300 flex items-center">
                <span id="btn-text">Next Step</span>
                <svg id="next-icon" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
            </button>
        </div>
    </form>
</x-layouts.auth-theme>
