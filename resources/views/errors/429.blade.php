<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Too Many Requests</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-900 font-['Instrument_Sans',sans-serif] text-slate-100">
    <div
        class="relative isolate min-h-screen overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950 flex items-center justify-center">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(20,184,166,0.1),transparent_40%)]">
        </div>

        <div class="relative w-full max-w-lg px-6">
            <div
                class="backdrop-blur-3xl bg-slate-900/60 rounded-[3rem] border border-white/5 p-12 text-center shadow-[0_48px_120px_rgba(0,0,0,0.5)]">
                <div
                    class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-teal-500/10 text-teal-500 mb-8 border border-teal-500/20 animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <h1 class="text-4xl font-black text-white tracking-tight mb-4 uppercase">Security Lock</h1>
                <p class="text-teal-500 font-bold mb-6 tracking-widest uppercase text-sm">Too Many Attempts Detected</p>

                <p class="text-slate-400 mb-10 text-lg leading-relaxed">
                    For your security, we have temporarily restricted access due to multiple failed attempts. Please
                    wait for the cooldown period.
                </p>

                <div class="bg-slate-950/50 rounded-2xl p-6 border border-white/5 mb-10 shadow-inner">
                    <span class="text-sm text-slate-500 block mb-2 font-medium uppercase tracking-tighter">System
                        unlocking in</span>
                    <div id="timer" class="text-5xl font-black text-teal-400 tabular-nums">00:00</div>
                </div>

                <a href="{{ route('admin.login') }}" id="retry-btn"
                    class="inline-flex items-center justify-center w-full px-10 py-5 bg-white/5 text-slate-500 font-bold rounded-2xl border border-white/5 cursor-not-allowed transition-all pointer-events-none">
                    Log In Again
                </a>
            </div>
        </div>
    </div>

    @php
        $retryAfter = 60;
        try {
            if (isset($exception)) {
                $headers = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];
                $retryAfter = $headers['Retry-After'] ?? ($headers['retry-after'] ?? 60);
            }
        } catch (\Exception $e) {
        }
    @endphp

    <script>
        let timeLeft = {{ $retryAfter }};
        const timerDisplay = document.getElementById('timer');
        const retryBtn = document.getElementById('retry-btn');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (timeLeft <= 0) {
                timerDisplay.textContent = "READY";
                timerDisplay.classList.replace('text-teal-400', 'text-emerald-400');
                retryBtn.classList.remove('bg-white/5', 'text-slate-500', 'cursor-not-allowed', 'pointer-events-none');
                retryBtn.classList.add('bg-teal-600', 'text-white', 'hover:bg-teal-700', 'shadow-xl', 'shadow-teal-600/30');
                return;
            }

            timeLeft--;
            setTimeout(updateTimer, 1000);
        }

        updateTimer();
    </script>
</body>

</html>
