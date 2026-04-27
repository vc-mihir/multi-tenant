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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/js/validation/common-validation.js"></script>
</head>

<body
    class="h-full font-['Instrument_Sans',sans-serif] text-slate-900 antialiased selection:bg-teal-100 selection:text-teal-900">
    <div class="flex h-full overflow-hidden">
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            @include('admin.partials.header')

            <main class="flex-1">
                <div class="px-6 py-8 mx-auto max-w-7xl">
                    <div class="mb-8 flex items-end justify-between">
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
                        @yield('content')
                    </div>
                </div>
            </main>

            @include('admin.partials.footer')
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#dashboard-search').on('input', function() {
                let q = $(this).val();
                if (q.length < 2) return $('#search-results').addClass('hidden');

                $.get("{{ route('admin.companies.search') }}", {
                    q: q
                }, function(data) {
                    let html = data.length ? data.map(item => `
                        <a href="${item.url}" class="flex items-center p-3 hover:bg-teal-50 rounded-xl transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600 font-bold mr-3 group-hover:bg-teal-600 group-hover:text-white transition-colors text-xs">
                                ${item.name.charAt(0).toUpperCase()}
                            </div>  
                            <div>
                                <div class="text-sm font-bold text-slate-800">${item.name}</div>
                                <div class="text-[10px] text-slate-500">${item.email}</div>
                            </div>
                        </a>`).join('') : '<div class="p-4 text-center text-sm text-slate-500">No results found</div>';

                    $('#search-results-content').html(html);
                    $('#search-results').removeClass('hidden');
                });
            });

        });
    </script>

    @stack('scripts')
</body>

</html>
