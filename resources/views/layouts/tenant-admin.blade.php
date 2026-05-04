<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ Auth::guard('company')->user()->company_name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }

        /* ── Sidebar ── */
        .t-sidebar       { background: #0f172a; }
        .t-nav-active    { background: rgba(79, 70, 229, 0.15); color: #818cf8; border-left: 3px solid #6366f1; }
        .t-nav-item      { color: rgba(255,255,255,0.4); border-left: 3px solid transparent; transition: all 0.2s; }
        .t-nav-item:hover{ background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.9); }

        /* ── Cards ── */
        .t-card { background:#fff; border:1px solid #e0e7ff; border-radius:1.25rem; }
        .t-card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .t-card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05); }

        /* ── Progress ── */
        .t-track { height:8px; border-radius:99px; background:#f1f5f9; }
        .t-fill  { height:100%; border-radius:99px; }

        /* ── Scrollbar ── */
        .t-scroll::-webkit-scrollbar { width:4px; }
        .t-scroll::-webkit-scrollbar-track { background:transparent; }
        .t-scroll::-webkit-scrollbar-thumb { background:rgba(99, 102, 241, 0.2); border-radius:10px; }
        .t-scroll::-webkit-scrollbar-thumb:hover { background:rgba(99, 102, 241, 0.4); }
    </style>

    @stack('styles')
</head>

<body class="h-full antialiased text-slate-900 selection:bg-indigo-100 selection:text-indigo-900">
<div class="flex h-full overflow-hidden">

    @include('tenant.admin.partials.sidebar')

    <div class="flex flex-col flex-1 overflow-hidden">
        @include('tenant.admin.partials.header')

        <main class="flex-1 overflow-y-auto t-scroll bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 py-5">

                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm text-gray-400 mt-1">@yield('page-subtitle')</p>
                </div>

                <div class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    @yield('content')
                </div>
            </div>
        </main>

        @include('tenant.admin.partials.footer')
    </div>

</div>
@stack('scripts')
</body>
</html>
