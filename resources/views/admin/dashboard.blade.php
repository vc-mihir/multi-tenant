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
                @forelse ($recentCompanies as $company)
                    <div
                        class="flex items-center p-4 bg-slate-50 border border-slate-100 rounded-2xl transition-all hover:bg-teal-50/50">
                        <div
                            class="w-10 h-10 rounded-full bg-teal-100 border border-teal-200 flex items-center justify-center mr-4">
                            <span class="text-teal-700 font-bold text-xs">{{ substr($company->company_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-800">
                                @if ($company->database)
                                    Database for "{{ $company->company_name }}" has been created successfully.
                                @else
                                    Database creation for "{{ $company->company_name }}" is in progress.
                                @endif
                            </p>
                            <p class="text-xs text-slate-500">{{ $company->created_at->diffForHumans() }}</p>
                        </div>
                        @if ($company->database)
                            <span
                                class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase whitespace-nowrap">Created</span>
                        @else
                            <span
                                class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase whitespace-nowrap">Pending</span>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 italic">No recent activity found.</div>
                @endforelse
            </div>
        </div>

        <!-- Tenant Recovery Panel -->
        <div class="p-6 bg-white border border-teal-100 rounded-[2rem] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-6 px-2">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-50 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Verified but Missing DB</h3>
                </div>
                @if ($recoveryCompanies->count() > 0)
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase">
                        {{ $recoveryCompanies->count() }} Pending
                    </span>
                @endif
            </div>

            <div class="flex-1 overflow-hidden flex flex-col">
                <div class="overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                    @forelse ($recoveryCompanies as $company)
                        <div
                            class="flex items-center p-4 bg-slate-50 border border-slate-100 rounded-2xl transition-all hover:bg-teal-50/50 group mb-3 last:mb-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $company->company_name }}</p>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">Verified:
                                        {{ $company->email_verified_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $company->company_email }}</p>
                            </div>
                            <div class="ml-4">
                                <form action="{{ route('admin.recovery.provision', $company) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="px-4 py-2 bg-teal-600 text-white text-[11px] font-bold rounded-xl hover:bg-teal-700 transition-all duration-200 shadow-md shadow-teal-100 flex items-center space-x-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <span>Create DB</span>
                                    </button>
                                </form>                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center p-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">All verified companies have active databases.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#ecfdf5',
                        color: '#065f46',
                        iconColor: '#10b981'
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "{{ session('error') }}",
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#fef2f2',
                        color: '#991b1b',
                        iconColor: '#ef4444'
                    });
                @endif
            });
        </script>
    @endpush
@endsection
