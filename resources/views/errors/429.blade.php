@extends('errors.layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message')
    {{ __('Whoa there! You\'re sending requests too quickly. Please take a breather and try again in a moment.') }}
@endsection

@section('page_id', 'error-429')

@section('extra')
@php
    $retryAfter = 60;
    if (isset($exception) && method_exists($exception, 'getHeaders')) {
        $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60;
    }
@endphp

<div class="mt-10 flex flex-col items-center gap-3">
    <p class="text-sm font-semibold uppercase tracking-widest text-slate-400">You can retry in</p>
    <div id="countdown-display"
        class="flex items-center justify-center w-28 h-28 rounded-full border-4 border-teal-100 bg-teal-50 shadow-inner">
        <span id="countdown-value" class="text-4xl font-black text-teal-600"
            data-seconds="{{ $retryAfter }}">{{ $retryAfter }}</span>
    </div>
    <p id="countdown-label" class="text-sm font-medium text-slate-500">seconds</p>
</div>
@endsection
