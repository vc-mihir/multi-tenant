@extends('layouts.tenant-admin')

@section('title', 'Create User')
@section('page-title', 'Create New User')
@section('page-subtitle', 'Add a new user to your tenant workspace')

@section('page-id', 'tenant-admin-users-create')

@section('content')
    <div class="max-w-3xl">
        <div class="t-card p-6 shadow-sm sm:p-8">
            <form action="{{ route('tenant.admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none @error('name') border-rose-500 @enderror"
                        placeholder="Enter user's full name" required autofocus>
                    @error('name')
                        <p class="mt-1.5 text-xs font-medium" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none @error('email') border-rose-500 @enderror"
                        placeholder="user@example.com" required>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Is Active --}}
                <div x-data="{
                    open: false,
                    selected: '{{ old('is_active', '1') }}',
                    options: [
                        { value: '1', label: 'Active', dot: 'bg-emerald-500' },
                        { value: '0', label: 'Inactive', dot: 'bg-slate-400' },
                    ],
                    get current() { return this.options.find(o => o.value === this.selected) || this.options[0]; },
                }" x-on:keydown.escape.window="open = false">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>

                    <input type="hidden" name="is_active" :value="selected">

                    <div class="relative" x-on:click.outside="open = false">
                        <button type="button" x-on:click="open = !open"
                            class="flex w-full items-center justify-between px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                            :class="{ 'ring-2 ring-indigo-200 border-indigo-500': open }">
                            <span class="flex items-center gap-2.5">
                                <span class="h-2.5 w-2.5 rounded-full" :class="current.dot"></span>
                                <span x-text="current.label">{{ old('is_active', '1') == '1' ? 'Active' : 'Inactive' }}</span>
                            </span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <ul x-show="open" x-transition.opacity.duration.150ms style="display: none;"
                            class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl shadow-slate-900/5">
                            <template x-for="option in options" :key="option.value">
                                <li>
                                    <button type="button" x-on:click="selected = option.value; open = false"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-indigo-50 hover:text-indigo-700"
                                        :class="{ 'bg-indigo-50/60 font-semibold text-indigo-700': selected === option.value }">
                                        <span class="flex items-center gap-2.5">
                                            <span class="h-2.5 w-2.5 rounded-full" :class="option.dot"></span>
                                            <span x-text="option.label"></span>
                                        </span>
                                        <svg x-show="selected === option.value" class="w-4 h-4 text-indigo-600"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>

                    @error('is_active')
                        <p class="mt-1.5 text-xs font-medium" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-3 pr-10 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none @error('password') border-rose-500 @enderror"
                                placeholder="••••••••" required>
                            <button type="button" onclick="togglePasswordVisibility('password', this)"
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-indigo-500 transition-colors duration-200"
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
                            <p class="mt-1.5 text-xs font-medium" style="color: #ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm
                            Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full px-4 py-3 pr-10 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none"
                                placeholder="••••••••" required>
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)"
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-indigo-500 transition-colors duration-200"
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

                <div class="pt-4 flex items-center gap-4">
                    <button type="submit"
                        class="px-10 py-4 text-white font-black rounded-xl shadow-2xl transition-all active:scale-95"
                        style="background-color: #10b981;">
                        Create User
                    </button>
                    <a href="{{ route('tenant.admin.dashboard') }}"
                        class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all active:scale-95">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
