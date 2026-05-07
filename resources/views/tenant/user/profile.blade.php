<x-layouts.user.tenant-user-header>
    <x-slot:title>My Profile | {{ config('app.name') }}</x-slot:title>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div>
            <!-- Header -->
            <div class="flex flex-col items-center text-center gap-8 mb-8">
                <div>
                    <h1 class="text-4xl font-black text-emerald-950 font-outfit tracking-tight">Account Settings</h1>
                    <p class="text-emerald-800/60 font-medium mt-1">Manage your personal information and security
                        preferences.</p>
                </div>
                <a href="{{ route('tenant.dashboard') }}"
                    class="flex items-center gap-3 px-6 py-3 bg-white border border-emerald-100 rounded-2xl text-emerald-600 font-bold text-sm hover:bg-emerald-50 transition-all duration-300 shadow-sm group w-fit">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Main Form Card -->
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-900/10 border border-emerald-50 overflow-hidden mb-5">
                <form action="{{ route('tenant.user.profile.update') }}" method="POST" class="p-8 sm:p-12 space-y-10">
                    @csrf
                    @method('PUT')

                    <div class="p-4 mb-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[10px] font-black text-amber-800 uppercase tracking-widest">Security Note: Leave password fields blank to keep your current password.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Profile Info Section -->
                        <div class="space-y-8">
                            <h3 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em]">Personal
                                Information</h3>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="name"
                                        class="text-xs font-black text-emerald-900 uppercase tracking-widest px-1">Full
                                        Name</label>
                                    <input type="text" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required
                                        class="w-full px-6 py-4 bg-emerald-50 border-transparent rounded-2xl focus:bg-white focus:border-emerald-200 focus:ring-0 transition-all duration-300 font-bold text-emerald-950 placeholder-emerald-300"
                                        placeholder="Enter your full name">
                                    @error('name')
                                        <p class="text-rose-500 text-[10px] font-bold uppercase tracking-widest px-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="email"
                                        class="text-xs font-black text-emerald-900 uppercase tracking-widest px-1">Email
                                        Address</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required
                                        class="w-full px-6 py-4 bg-emerald-50 border-transparent rounded-2xl focus:bg-white focus:border-emerald-200 focus:ring-0 transition-all duration-300 font-bold text-emerald-950 placeholder-emerald-300"
                                        placeholder="your@email.com">
                                    @error('email')
                                        <p class="text-rose-500 text-[10px] font-bold uppercase tracking-widest px-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <h3 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em]">Security</h3>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="password"
                                        class="text-xs font-black text-emerald-900 uppercase tracking-widest px-1">New
                                        Password</label>
                                    <input type="password" id="password" name="password"
                                        class="w-full px-6 py-4 bg-emerald-50 border-transparent rounded-2xl focus:bg-white focus:border-emerald-200 focus:ring-0 transition-all duration-300 font-bold text-emerald-950 placeholder-emerald-300"
                                        placeholder="Leave blank to keep current">
                                    @error('password')
                                        <p class="text-rose-500 text-[10px] font-bold uppercase tracking-widest px-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="password_confirmation"
                                        class="text-xs font-black text-emerald-900 uppercase tracking-widest px-1">Confirm
                                        Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="w-full px-6 py-4 bg-emerald-50 border-transparent rounded-2xl focus:bg-white focus:border-emerald-200 focus:ring-0 transition-all duration-300 font-bold text-emerald-950 placeholder-emerald-300"
                                        placeholder="Repeat your new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="pt-10 flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-emerald-50">
                        <p class="text-sm text-emerald-800/50 font-medium italic">Last updated
                            {{ $user->updated_at->diffForHumans() }}</p>
                        <button type="submit"
                            class="w-full sm:w-auto px-12 py-5 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-200 hover:bg-emerald-700 hover:scale-105 transition-all duration-300 uppercase tracking-widest text-xs">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div
                class="p-8 sm:p-12 bg-rose-50 rounded-[2.5rem] border border-rose-100/50 flex flex-col sm:flex-row items-center justify-between gap-8">
                <div class="text-center sm:text-left">
                    <h4 class="text-rose-900 font-black text-lg">Danger Zone</h4>
                    <p class="text-rose-900/60 font-medium text-sm">Once you delete your account, there is no going
                        back. Please be certain.</p>
                </div>
                <form id="delete-account-form" action="{{ route('tenant.user.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()"
                        class="px-8 py-4 bg-rose-600 text-white font-black rounded-xl hover:bg-rose-700 transition-all duration-300 uppercase tracking-widest text-[10px] shadow-lg shadow-rose-200">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Your account will be permanently deleted. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
                background: '#ffffff',
                color: '#4c0519',
                customClass: {
                    popup: 'rounded-[2.5rem] border border-rose-50',
                    confirmButton: 'rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3',
                    cancelButton: 'rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting Account...',
                        text: 'Please wait while we process your request.',
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            document.getElementById('delete-account-form').submit();
                        }
                    });
                }
            })
        }
    </script>
    </div>
    </div>
</x-layouts.user.tenant-user-header>
