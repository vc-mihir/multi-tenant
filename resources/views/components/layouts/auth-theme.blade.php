<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="/js/validation/common-validation.js"></script>

    <style>
        .error {
            color: #ef4444;
            font-size: 10px;
            font-weight: 700;
            margin-left: 0.25rem;
            margin-top: 0.25rem;
            display: block;
        }

        input.error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
    </style>
</head>

<body class="min-h-screen bg-[#FDFDFC] font-['Instrument_Sans',sans-serif] text-slate-900 overflow-hidden">
    <div
        class="relative isolate min-h-screen overflow-hidden bg-gradient-to-br from-[#E67E5F] via-[#DD7F61] to-[#D16A4E] flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent_40%)]">
        </div>
        <div class="absolute inset-0 bg-[radial_gradient(circle_at_bottom_left,rgba(0,0,0,0.05),transparent_35%)]">
        </div>

        <div class="relative w-full max-w-[480px] h-full flex flex-col">
            <div
                class="backdrop-blur-xl bg-white/95 rounded-[2.5rem] border border-white/20 p-6 sm:p-8 shadow-[0_40px_100px_rgba(180,70,30,0.25)] flex-1">

                @if (session('status'))
                    <div class="mb-4 p-3 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-6 h-6 rounded-full bg-rose-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-rose-800">{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}

                <div class="mt-4 text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.25em]">
                        &copy; {{ date('Y') }} Tenant Management
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
