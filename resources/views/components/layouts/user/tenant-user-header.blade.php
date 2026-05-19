<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|outfit:600,700,800" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="antialiased user-layout-body" data-page="{{ $pageId ?? 'tenant-user-layout' }}">
    <div class="min-h-screen bg-mint-card">
        <!-- Navigation Header -->
        <header class="bg-white border-b border-emerald-100/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <a href="{{ route('tenant.dashboard') }}" class="flex-shrink-0 flex items-center gap-3 group">
                            <div
                                class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span
                                class="text-xl font-black text-emerald-950 font-outfit tracking-tight font-outfit uppercase">Tenant<span
                                    class="text-emerald-600">Hub</span></span>
                        </a>
                    </div>

                    <div class="flex items-center gap-6">
                        <!-- User Dropdown -->
                        @php $user = Auth::guard('tenant_user')->user(); @endphp
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-emerald-50 transition-all duration-300">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-black text-emerald-900">{{ $user->name }}</p>
                                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Active
                                        User</p>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-black border border-emerald-200">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 bg-white rounded-[1.5rem] shadow-2xl shadow-emerald-900/10 border border-emerald-50 py-2 z-50"
                                style="width: 400px;">

                                <div class="px-4 py-3 border-b border-emerald-50">
                                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-1">Signed
                                        in as</p>
                                    <p class="text-sm font-bold text-emerald-900 truncate">{{ $user->email }}</p>
                                </div>

                                <a href="{{ route('tenant.user.profile') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-emerald-700 hover:bg-emerald-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile Settings
                                </a>

                                <div class="h-px bg-emerald-50 my-1"></div>

                                <form method="POST" action="{{ route('tenant.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="pt-8 pb-12">
            {{ $slot }}
        </main>
    </div>
    <x-toast-alert />
</body>

</html>
