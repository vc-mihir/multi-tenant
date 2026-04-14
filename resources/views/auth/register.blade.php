<x-layouts.auth-theme>
    <!-- Progress Indicator -->
    <div class="mb-10 px-2">
        <div class="flex items-center justify-between relative">
            <!-- Background Line -->
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-100 -translate-y-1/2"></div>
            <!-- Active Progress Line -->
            <div id="progress-line"
                class="absolute top-1/2 left-0 w-0 h-0.5 bg-[#DD7F61] -translate-y-1/2 transition-all duration-500">
            </div>

            <!-- Steps -->
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

    <form method="POST" action="{{ route('register') }}" id="registration-form">
        @csrf

        <!-- Phase 1: Account Setup -->
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
                            required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. Acme Corp">
                    </div>
                    <div class="space-y-1.5">
                        <label for="company_email"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                            Email</label>
                        <input id="company_email" type="email" name="company_email" value="{{ old('company_email') }}"
                            required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="admin@co.com">
                    </div>
                </div>
                @error('company_email')
                    <p class="text-[10px] font-bold text-red-500 ml-1">{{ $message }}</p>
                @enderror

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Password</label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="••••••••">
                    </div>
                    <div class="space-y-1.5">
                        <label for="password_confirmation"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="••••••••">
                    </div>
                </div>
                @error('password')
                    <p class="text-[10px] font-bold text-red-500 ml-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Phase 2: Company Details -->
        <div id="phase-2" class="hidden space-y-5 transition-all duration-500">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-900">Business Credentials</h3>
                <p class="text-sm text-slate-500">Tell us more about your organization.</p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="website"
                        class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Company Website</label>
                    <input id="website" type="url" name="website" value="{{ old('website') }}"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                        placeholder="https://acme.com">
                </div>

                <div class="space-y-1.5">
                    <label for="license_number"
                        class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">License Number</label>
                    <input id="license_number" type="text" name="license_number" value="{{ old('license_number') }}"
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                        placeholder="REG-123456">
                </div>
            </div>
        </div>

        <!-- Phase 3: Location Details -->
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
                        <input id="address" type="text" name="address" value="{{ old('address') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="Street...">
                    </div>
                    <div class="space-y-1.5">
                        <label for="city"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">City</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. SF">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="state"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">State</label>
                        <input id="state" type="text" name="state" value="{{ old('state') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. CA">
                    </div>
                    <div class="space-y-1.5">
                        <label for="country"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Country</label>
                        <input id="country" type="text" name="country" value="{{ old('country') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[#DD7F61]/10 focus:border-[#DD7F61] transition-all duration-300"
                            placeholder="e.g. USA">
                    </div>
                </div>

            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between pt-10">
            <button type="button" id="prev-btn"
                class="hidden text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
                Back
            </button>
            <div id="back-to-login" class="block">
                <a class="text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors"
                    href="{{ route('login') }}">
                    Already registered?
                </a>
            </div>

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

    <script>
        const form = document.getElementById('registration-form');
        const phases = ['phase-1', 'phase-2', 'phase-3'];
        const dots = ['step-1-dot', 'step-2-dot', 'step-3-dot'];
        const labels = ['', 'step-2-label', 'step-3-label'];
        const progressLine = document.getElementById('progress-line');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const backToLogin = document.getElementById('back-to-login');
        const btnText = document.getElementById('btn-text');
        const nextIcon = document.getElementById('next-icon');

        let currentPhase = 0;

        function updateUI() {
            // Toggle Phases
            phases.forEach((id, index) => {
                document.getElementById(id).classList.toggle('hidden', index !== currentPhase);
            });

            // Update Progress Dots
            dots.forEach((id, index) => {
                const dot = document.getElementById(id);
                if (index <= currentPhase) {
                    dot.classList.remove('bg-slate-100', 'text-slate-400');
                    dot.classList.add('bg-[#DD7F61]', 'text-white', 'shadow-lg', 'shadow-[#DD7F61]/20');
                } else {
                    dot.classList.remove('bg-[#DD7F61]', 'text-white', 'shadow-lg', 'shadow-[#DD7F61]/20');
                    dot.classList.add('bg-slate-100', 'text-slate-400');
                }
            });

            // Update Labels
            labels.forEach((id, index) => {
                if (!id) return;
                const label = document.getElementById(id);
                if (index <= currentPhase) {
                    label.classList.remove('text-slate-400');
                    label.classList.add('text-[#DD7F61]');
                } else {
                    label.classList.remove('text-[#DD7F61]');
                    label.classList.add('text-slate-400');
                }
            });

            // Update Progress Line
            const progress = (currentPhase / (phases.length - 1)) * 100;
            progressLine.style.width = `${progress}%`;

            // Update Buttons
            prevBtn.classList.toggle('hidden', currentPhase === 0);
            backToLogin.classList.toggle('hidden', currentPhase !== 0);

            if (currentPhase === phases.length - 1) {
                btnText.textContent = 'Register Now';
                nextIcon.classList.add('hidden');
            } else {
                btnText.textContent = 'Next Step';
                nextIcon.classList.remove('hidden');
            }
        }

        nextBtn.addEventListener('click', () => {
            if (currentPhase < phases.length - 1) {
                currentPhase++;
                updateUI();
            } else {
                form.submit();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentPhase > 0) {
                currentPhase--;
                updateUI();
            }
        });
    </script>
</x-layouts.auth-theme>
