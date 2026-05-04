<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

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
        .t-sidebar       { background: #1e1b4b; }
        .t-nav-active    { background: rgba(99,102,241,0.25); color: #a5b4fc; border-left: 3px solid #6366f1; }
        .t-nav-item      { color: rgba(255,255,255,0.5); border-left: 3px solid transparent; transition: all 0.15s; }
        .t-nav-item:hover{ background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.85); }

        /* ── Cards ── */
        .t-card { background:#fff; border:1px solid #e5e7eb; border-radius:1rem; }
        .t-card-hover { transition: all 0.2s; }
        .t-card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,0.1); }

        /* ── Progress ── */
        .t-track { height:6px; border-radius:99px; background:#f3f4f6; }
        .t-fill  { height:100%; border-radius:99px; }

        /* ── Scrollbar ── */
        .t-scroll::-webkit-scrollbar { width:4px; }
        .t-scroll::-webkit-scrollbar-track { background:transparent; }
        .t-scroll::-webkit-scrollbar-thumb { background:#c7d2fe; border-radius:4px; }
    </style>

    @stack('styles')
</head>

<body class="h-full antialiased text-gray-900">
<div class="flex h-full overflow-hidden">

    @include('tenant.admin.partials.sidebar')

    <div class="flex flex-col flex-1 overflow-hidden">
        @include('tenant.admin.partials.header')

        <main class="flex-1 overflow-y-auto t-scroll bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 py-8">

                <div class="mb-6">
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
