@extends('layouts.admin')

@section('title', 'General Settings')
@section('page-title', 'General Settings')
@section('page-subtitle', 'Manage your super admin profile information')

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm ring-1 ring-slate-900/5">
            <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Full Name -->
                        <div class="space-y-1.5">
                            <label for="name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Full
                                Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="Admin Name">
                            @error('name')
                                <span class="text-xs font-semibold text-red-600 ml-1 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-1.5">
                            <label for="email"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Email
                                Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="admin@example.com">
                            @error('email')
                                <span class="text-xs font-semibold text-red-600 ml-1 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h3 class="text-sm font-bold text-slate-800 mb-4">Change Password (Optional)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- New Password -->
                                <div class="space-y-1.5">
                                    <label for="password"
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">New
                                        Password</label>
                                    <input id="password" type="password" name="password"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                        placeholder="••••••••">
                                    @error('password')
                                        <span class="text-xs font-semibold text-red-600 ml-1 mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-1.5">
                                    <label for="password_confirmation"
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Confirm
                                        Password</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-8 border-t border-slate-100">
                        <button type="submit"
                            class="px-8 py-3 bg-teal-600 text-white font-bold rounded-2xl hover:bg-teal-700 focus:ring-4 focus:ring-teal-500/20 transition-all duration-300 shadow-lg shadow-teal-600/20">
                            Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#0d9488',
                borderRadius: '1.5rem'
            });
        @endif
    </script>
@endpush
