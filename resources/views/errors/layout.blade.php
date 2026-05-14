<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name', 'MultiTenant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full antialiased selection:bg-teal-500 selection:text-white overflow-hidden error-page-body"
    data-page="error-page">
    <div class="relative flex min-h-screen items-center justify-center p-6">
        <!-- Glowing Orbs -->
        <div class="glow -top-20 -left-20"></div>
        <div class="glow -bottom-20 -right-20"></div>

        <div class="mx-auto max-w-4xl text-center w-full">
            <div class="error-card p-12 sm:p-20 shadow-2xl">
                <p class="text-xl font-semibold leading-8 text-teal-600">@yield('code')</p>
                <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900 sm:text-6xl">@yield('title')</h1>
                <p class="mt-8 text-lg leading-8 text-slate-600 max-w-2xl mx-auto">
                    @yield('message')
                </p>

                <div class="mt-12 flex items-center justify-center">
                    <button onclick="smartBack()"
                        class="group relative flex items-center justify-center gap-x-2 rounded-2xl bg-teal-600 px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-xl shadow-teal-900/20 transition-all duration-300 hover:bg-teal-500 hover:shadow-teal-900/40 active:scale-[0.98]">
                        <svg class="h-5 w-5 transition-transform duration-300 group-hover:-translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2.5" />
                        </svg>
                        Go Back to Safety
                    </button>
                </div>
            </div>

            <div class="mt-12">
                <p class="text-xs text-slate-400 font-medium tracking-wide uppercase">
                    &copy; {{ date('Y') }} {{ config('app.name', 'MultiTenant') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
