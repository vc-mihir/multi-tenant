@extends('layouts.tenant-admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::guard('company')->user()->company_name)

@section('content')

    {{-- ── QUICK STATS ────────────────────────────── --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4" style="margin-bottom: 25px;">
        <!-- Stat 1: Total Users -->
        <div class="flex items-center p-5 bg-white border border-gray-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-2.5 mr-3 rounded-xl" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="mb-0.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-black text-slate-900">2,842</p>
            </div>
        </div>

        <!-- Stat 2: Active Sessions -->
        <div class="flex items-center p-5 bg-white border border-gray-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-2.5 mr-3 rounded-xl" style="background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <p class="mb-0.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Sessions</p>
                <p class="text-2xl font-black text-slate-900">418</p>
            </div>
        </div>

        <!-- Stat 3: Storage Used -->
        <div class="flex items-center p-5 bg-white border border-gray-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-2.5 mr-3 rounded-xl" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
            </div>
            <div>
                <p class="mb-0.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Storage Used</p>
                <p class="text-2xl font-black text-slate-900">64%</p>
            </div>
        </div>

        <!-- Stat 4: Security Score -->
        <div class="flex items-center p-5 bg-white border border-gray-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-2.5 mr-3 rounded-xl" style="background-color: rgba(99, 102, 241, 0.1); color: #6366f1;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <p class="mb-0.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Security Score</p>
                <p class="text-2xl font-black text-slate-900">98/100</p>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT (WELCOME BANNER) ────────────────────────── --}}
    <div class="p-10 border border-slate-800 rounded-[2.5rem] shadow-2xl relative overflow-hidden" 
         style="background-color: #0f172a; background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);">
        
        <div class="absolute top-0 right-0 w-80 h-80 -mr-20 -mt-20 rounded-full opacity-10 blur-3xl" style="background-color: #10b981;"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
            <div class="max-w-xl text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-5" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: #10b981;"></span>
                    Workspace Active
                </div>
                <h2 class="text-3xl font-black text-white mb-4 leading-tight">Ready to expand your workspace?</h2>
                <p class="text-base text-slate-400 leading-relaxed mb-8">
                    You can now manually provision new users directly from your tenant control panel. Our infrastructure is ready to scale with your needs.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="inline-flex items-center px-10 py-4 text-white font-black rounded-xl shadow-2xl transition-all active:scale-95 group" style="background-color: #10b981;">
                        Create User
                        <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="hidden lg:block shrink-0">
                <div class="w-48 h-48 border border-slate-700 rounded-[2.5rem] flex items-center justify-center p-6 rotate-3 shadow-2xl" style="background-color: rgba(255, 255, 255, 0.02);">
                    <svg class="w-24 h-24" style="color: #10b981; opacity: 0.2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* Absolute background guarantee */
    main { 
        background-color: #f8fafc !important;
        background-image: 
            radial-gradient(at 100% 0%, rgba(15, 23, 42, 0.02) 0px, transparent 50%),
            radial-gradient(at 0% 100%, rgba(16, 185, 129, 0.02) 0px, transparent 50%) !important;
    }
</style>
@endpush
