<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | MultiTenant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @stack('styles')
</head>

<body
    class="h-full font-['Instrument_Sans',sans-serif] text-slate-900 antialiased selection:bg-teal-100 selection:text-teal-900"
    data-page="@yield('page-id')">
    <div class="flex h-full overflow-hidden">
        @include('central.admin.partials.sidebar')

        <!-- Mobile sidebar backdrop -->
        <div id="sidebar-overlay"
            class="fixed inset-0 z-40 hidden bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            @include('central.admin.partials.header')

            <main class="flex-1">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 sm:py-8">
                    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900 lg:text-3xl">
                                @yield('page-title')
                            </h1>
                            <p class="mt-2 text-sm text-slate-500">
                                @yield('page-subtitle')
                            </p>
                        </div>
                        <div>
                            @yield('page-actions')
                        </div>
                    </div>

                    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        @role('SuperAdmin', 'admin')
                            @yield('content')
                        @endrole
                    </div>
                </div>
            </main>

            @include('central.admin.partials.footer')
        </div>
    </div>



    <x-toast-alert />
    @stack('scripts')
</body>

</html>
