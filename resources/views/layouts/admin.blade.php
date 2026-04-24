<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | MultiTenant</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(45, 212, 191, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(45, 212, 191, 0.4);
        }
    </style>

    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full font-['Instrument_Sans',sans-serif] text-slate-900 antialiased selection:bg-teal-100 selection:text-teal-900">
    <div class="flex h-full overflow-hidden">
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            @include('admin.partials.header')

            <main class="flex-1">
                <div class="px-6 py-8 mx-auto max-w-7xl">
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 lg:text-3xl">
                            @yield('page-title')
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            @yield('page-subtitle')
                        </p>
                    </div>

                    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        @yield('content')
                    </div>
                </div>
            </main>

            @include('admin.partials.footer')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
