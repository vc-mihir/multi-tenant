<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login | {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:700"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .bg-mint-card {
            background-color: #dff5e9;
        }

        .btn-dark {
            background-color: #1c2e30;
        }

        .btn-dark:hover {
            background-color: #142123;
        }
    </style>
</head>

<body class="antialiased font-['Instrument_Sans',sans-serif] bg-mint-card text-slate-700 h-screen overflow-hidden">
    <div class="h-full w-full flex items-center justify-center p-6">

        <!-- Centered Login Card -->
        <div
            class="w-full max-w-lg bg-white rounded-[3rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.15)] p-10 lg:p-16 flex flex-col items-center">

            <!-- Logo -->
            <div class="mb-12 flex flex-col items-center gap-4">
                <div class="w-14 h-14 bg-mint-card rounded-2xl flex items-center justify-center shadow-sm">
                    <svg class="w-8 h-8 text-[#1c2e30]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21H23L12 2ZM12 6L19.53 19H4.47L12 6Z" />
                    </svg>
                </div>
                <span class="font-serif text-3xl tracking-widest text-[#1c2e30] uppercase">Admin<span
                        class="font-light">Panel</span></span>
            </div>

            <div class="w-full">
                <div class="text-center mb-10">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.25em] mb-2">Secure Access</h3>
                    <h1 class="text-3xl font-black text-[#1c2e30]">Login to Account</h1>
                </div>

                @if (session('status'))
                    <div class="mb-8 p-4 rounded-2xl bg-green-50 text-sm font-bold text-green-700 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.admin.login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-bold text-slate-500 ml-1">Admin email</label>
                        <input id="email" name="email" type="email" required autofocus
                            value="{{ old('email') }}"
                            class="w-full px-6 py-4 rounded-2xl border-2 border-slate-50 bg-slate-50 text-[#1c2e30] text-base font-medium placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-[#d1f2e1] focus:border-[#8fd9b6] transition-all"
                            placeholder="name@tenant.com">
                        @if ($errors->has('email'))
                            <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="text-sm font-bold text-slate-500">Password</label>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="w-full px-6 py-4 rounded-2xl border-2 border-slate-50 bg-slate-50 text-[#1c2e30] text-base font-medium placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-[#d1f2e1] focus:border-[#8fd9b6] transition-all"
                            placeholder="••••••••">
                        @if ($errors->has('password'))
                            <p class="text-xs font-bold text-red-500 mt-2 ml-1">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full btn-dark text-white font-black py-5 rounded-2xl shadow-xl transition-all active:scale-[0.98] text-lg mt-2 tracking-wide">
                        Sign in
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>

</html>
