@extends('layouts.tenant-admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::guard('company')->user()->company_name)

@section('content')

{{-- ── STAT CARDS ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    @php
    $stats = [
        ['label' => 'Active Users',   'value' => '1,240', 'note' => '+8% this month',  'bg' => 'bg-indigo-50',  'icon_color' => 'text-indigo-600',
         'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['label' => 'API Requests',   'value' => '42.8k', 'note' => 'Last 24 hours',   'bg' => 'bg-amber-50',   'icon_color' => 'text-amber-600',
         'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        ['label' => 'Storage Used',   'value' => '64%',   'note' => 'Of 100 GB quota', 'bg' => 'bg-sky-50',     'icon_color' => 'text-sky-600',
         'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
        ['label' => 'Security Score', 'value' => '98/100','note' => 'Excellent rating', 'bg' => 'bg-emerald-50', 'icon_color' => 'text-emerald-600',
         'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
    ];
    @endphp

    @foreach ($stats as $s)
    <div class="t-card t-card-hover p-5">
        <div class="w-10 h-10 {{ $s['bg'] }} {{ $s['icon_color'] }} rounded-xl flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 mb-0.5">{{ $s['value'] }}</p>
        <p class="text-sm font-semibold text-gray-600">{{ $s['label'] }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $s['note'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── WELCOME BANNER ───────────────────────────────────────────── --}}
<div class="rounded-2xl bg-indigo-700 p-7 mb-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 -mr-16 -mt-16 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest mb-2">Workspace Active</p>
            <h2 class="text-2xl font-bold text-white mb-2">Your tenant environment is fully operational.</h2>
            <p class="text-indigo-200 text-sm leading-relaxed max-w-lg">
                Database nodes connected, security protocols active, and infrastructure ready to scale.
            </p>
            <div class="flex flex-wrap items-center gap-3 mt-5">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/15 rounded-xl text-sm font-medium text-white">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
                    All Nodes Connected
                </div>
                <div class="px-4 py-2 bg-white/10 border border-white/15 rounded-xl text-sm font-medium text-white">
                    {{ Auth::guard('company')->user()->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                </div>
            </div>
        </div>
        <div class="hidden lg:flex w-36 h-36 rounded-2xl bg-white/8 border border-white/15 items-center justify-center shrink-0">
            <svg class="w-18 h-18 w-16 h-16 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── BOTTOM GRID ──────────────────────────────────────────────── --}}
<div class="grid gap-6 lg:grid-cols-2">

    {{-- Infrastructure Health --}}
    <div class="t-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-gray-800">Infrastructure Health</h3>
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 uppercase tracking-wide">Live</span>
        </div>
        <div class="space-y-5">
            @php
            $metrics = [
                ['label' => 'Query Latency', 'val' => '12ms', 'pct' => 12, 'fill' => '#6366f1'],
                ['label' => 'CPU Usage',     'val' => '34%',  'pct' => 34, 'fill' => '#f59e0b'],
                ['label' => 'Memory Load',   'val' => '58%',  'pct' => 58, 'fill' => '#0ea5e9'],
                ['label' => 'Disk Storage',  'val' => '64%',  'pct' => 64, 'fill' => '#10b981'],
            ];
            @endphp
            @foreach ($metrics as $m)
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-gray-600 font-medium">{{ $m['label'] }}</span>
                    <span class="font-bold text-gray-800">{{ $m['val'] }}</span>
                </div>
                <div class="t-track">
                    <div class="t-fill" style="width:{{ $m['pct'] }}%;background:{{ $m['fill'] }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="t-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-gray-800">Recent Activity</h3>
            <button class="text-[10px] font-bold text-indigo-500 uppercase tracking-wide hover:text-indigo-700 transition-colors">View All</button>
        </div>
        <div class="space-y-3">
            @php
            $activities = [
                ['color' => '#10b981', 'title' => 'Database migration completed',   'time' => '2 min ago',  'badge' => 'Success', 'badge_cls' => 'bg-emerald-50 text-emerald-700'],
                ['color' => '#6366f1', 'title' => 'New team member registered',      'time' => '18 min ago', 'badge' => 'User',    'badge_cls' => 'bg-indigo-50 text-indigo-700'],
                ['color' => '#f59e0b', 'title' => 'Backup snapshot created',         'time' => '1 hr ago',   'badge' => 'Backup',  'badge_cls' => 'bg-amber-50 text-amber-700'],
                ['color' => '#0ea5e9', 'title' => 'Security audit scan passed',      'time' => '3 hr ago',   'badge' => 'Secure',  'badge_cls' => 'bg-sky-50 text-sky-700'],
            ];
            @endphp
            @foreach ($activities as $a)
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100 hover:bg-indigo-50/40 hover:border-indigo-100 transition-all">
                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $a['color'] }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $a['title'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $a['time'] }}</p>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $a['badge_cls'] }} whitespace-nowrap">{{ $a['badge'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
