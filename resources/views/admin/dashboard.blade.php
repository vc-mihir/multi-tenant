@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'System Overview')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

@section('content')
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <!-- Quick Stats 1: Total Tenants -->
        <div
            class="flex items-center p-6 bg-white border border-teal-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-3 mr-4 text-teal-600 bg-teal-50 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-slate-500">Total Tenants</p>
                <p class="text-2xl font-bold text-slate-900">{{ $totalCompanies }}</p>
            </div>
        </div>

        <!-- Quick Stats 2: Pending Actions -->
        <div
            class="flex items-center p-6 bg-white border border-teal-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-3 mr-4 text-amber-600 bg-amber-50 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-slate-500">Pending Actions</p>
                <p class="text-2xl font-bold text-slate-900">{{ $pendingCompanies }}</p>
            </div>
        </div>

        <!-- Quick Stats 3: Inactive Tenants -->
        <div
            class="flex items-center p-6 bg-white border border-teal-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-3 mr-4 text-slate-600 bg-slate-100 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-slate-500">Inactive Tenants</p>
                <p class="text-2xl font-bold text-slate-900">{{ $inactiveCompanies }}</p>
            </div>
        </div>

        <!-- Quick Stats 4: Suspended Tenants -->
        <div
            class="flex items-center p-6 bg-white border border-teal-100 rounded-2xl shadow-sm transition-all hover:shadow-md">
            <div class="p-3 mr-4 text-red-600 bg-red-50 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-slate-500">Suspended Tenants</p>
                <p class="text-2xl font-bold text-slate-900">{{ $suspendedCompanies }}</p>
            </div>
        </div>
    </div>

    <!-- Main Section: Welcome Card -->
    <div
        class="p-8 mb-8 bg-gradient-to-br from-teal-700 to-teal-900 rounded-[2rem] text-white shadow-xl shadow-teal-700/20 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 -mr-20 -mt-20 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-xl">
                <h2 class="text-3xl font-bold mb-4">Start your tenant onboarding.</h2>
                <p class="text-teal-50/80 text-lg leading-relaxed mb-6">
                    Ready to expand your ecosystem? You can now manually provision new multi-tenant databases directly from
                    the control panel.
                </p>
                <button
                    class="px-8 py-3.5 bg-white text-teal-700 font-bold rounded-2xl hover:bg-teal-50 transition-all shadow-lg">
                    Create New Company
                </button>
            </div>
            <div class="hidden lg:block">
                <div
                    class="w-48 h-48 bg-teal-500/20 backdrop-blur-2xl rounded-3xl border border-white/20 flex items-center justify-center p-4 rotate-3">
                    <svg class="w-24 h-24 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Table Mockup: Recent Activity -->
        <div class="p-6 bg-white border border-teal-100 rounded-[2rem] shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 px-2">Recent System Activity</h3>
            <div class="space-y-4">
                @for ($i = 1; $i <= 4; $i++)
                    <div
                        class="flex items-center p-4 bg-slate-50 border border-slate-100 rounded-2xl transition-all hover:bg-teal-50/50">
                        <div
                            class="w-10 h-10 rounded-full bg-teal-100 border border-teal-200 flex items-center justify-center mr-4">
                            <span class="text-teal-700 font-bold text-xs">MT</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-800">New company "ViitorCloud" was provisioned.</p>
                            <p class="text-xs text-slate-500">Today, 2:45 PM</p>
                        </div>
                        <span
                            class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase">Success</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="p-6 bg-white border border-teal-100 rounded-[2rem] shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 px-2">Administrative Shortcuts</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <a href="#"
                    class="p-5 bg-teal-50 border border-teal-100 rounded-2xl hover:bg-teal-100 transition-all group">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm text-teal-600 transition-transform group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-800 mb-1">Database Config</p>
                    <p class="text-xs text-slate-500">Manage master connections.</p>
                </a>
                <a href="#"
                    class="p-5 bg-teal-50 border border-teal-100 rounded-2xl hover:bg-teal-100 transition-all group">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm text-teal-600 transition-transform group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-800 mb-1">Usage Reports</p>
                    <p class="text-xs text-slate-500">View platform statistics.</p>
                </a>
            </div>
        </div>
    </div>
@endsection
