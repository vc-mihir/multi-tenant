<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#FDFDFC] font-['Instrument_Sans',sans-serif] text-slate-900">
    <div
        class="relative isolate min-h-screen overflow-hidden bg-gradient-to-br from-[#E67E5F] via-[#DD7F61] to-[#D16A4E] flex items-center justify-center p-6 sm:p-12">
        <!-- Abstract background elements -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent_40%)]">
        </div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(0,0,0,0.05),transparent_35%)]">
        </div>

        <div class="relative w-full max-w-[480px]">
            <div
                class="backdrop-blur-xl bg-white/95 rounded-[2.5rem] border border-white/20 p-10 sm:p-12 shadow-[0_40px_100px_rgba(180,70,30,0.25)]">
                
                {{ $slot }}

                <div class="mt-12 text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.25em]">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }} Management
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
