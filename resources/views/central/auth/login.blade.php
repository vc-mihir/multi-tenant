<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-teal-700 font-['Instrument_Sans',sans-serif] text-slate-900">
    <div class="relative isolate min-h-screen overflow-hidden bg-gradient-to-br from-teal-800 via-teal-700 to-teal-600">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(94,234,212,0.18),transparent_32%)]">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,rgba(45,212,191,0.14),transparent_28%)]">
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center justify-center px-6 py-10 lg:px-8">
            <section class="w-full max-w-md">
                <div
                    class="w-full rounded-[2rem] border border-teal-100 bg-white p-8 shadow-[0_24px_80px_rgba(13,148,136,0.18)] sm:p-10">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-600 text-lg font-bold text-white shadow-lg shadow-teal-600/30">
                            SA
                        </div>
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.25em] text-teal-600">Super Admin</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Login</h2>
                        </div>
                    </div>

                    <p class="mt-6 text-sm leading-6 text-slate-500">
                        Use your elevated credentials to continue to the administrative dashboard.
                    </p>

                    @include('components.toast-alert')

                    <form class="mt-8 space-y-5" method="POST" action="{{ route('admin.login.post') }}">
                        @csrf

                        @if (session('error') || $errors->any())
                            <div
                                class="p-4 rounded-2xl bg-red-50 border border-red-100 flex items-start space-x-3 animate-in fade-in slide-in-from-top-4 duration-300">
                                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1 text-sm font-bold text-red-900 leading-tight">
                                    {{ session('error') ?? 'credentials does not match try again' }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email
                                address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                placeholder="admin@example.com"
                                class="w-full rounded-2xl border border-teal-100 bg-teal-50/40 px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-4">
                                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            </div>
                            <div class="relative">
                                <input id="password" type="password" name="password" required
                                    placeholder="Enter your password"
                                    class="w-full rounded-2xl border border-teal-100 bg-teal-50/40 px-4 py-3.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                                <button type="button"
                                    onclick="togglePasswordVisibility('password', this)"
                                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-teal-600 transition-colors duration-200"
                                    tabindex="-1">
                                    <span class="relative block w-5 h-5">
                                        <svg class="eye-open absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg class="eye-closed hidden absolute inset-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full rounded-2xl bg-teal-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-teal-600/30 transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-200">
                            Sign In to Admin Panel
                        </button>
                    </form>

                </div>
            </section>
        </div>
    </div>
</body>

</html>
