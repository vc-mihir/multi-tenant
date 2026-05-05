@extends('layouts.tenant-admin')

@section('title', 'Create User')
@section('page-title', 'Create New User')
@section('page-subtitle', 'Add a new user to your tenant workspace')

@section('content')
    <div class="max-w-3xl">
        <div class="t-card p-8 shadow-sm">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none @error('password') border-rose-500 @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium" style="color: #ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-4">
                    <button type="submit"
                        class="px-10 py-4 text-white font-black rounded-xl shadow-2xl transition-all active:scale-95"
                        style="background-color: #10b981;">
                        Create User
                    </button>
                    <a href="{{ route('tenant.admin.users.index') }}"
                        class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all active:scale-95">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
