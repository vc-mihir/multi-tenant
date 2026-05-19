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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="h-full antialiased text-slate-900 selection:bg-indigo-100 selection:text-indigo-900"
    data-page="@yield('page-id', 'tenant-admin-dashboard')">
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
    <x-toast-alert />
    @stack('scripts')
</body>

</html>
