@extends('errors.layout')

@section('title', __('Page Not Found'))
@section('code', '404')
@section('message')
    @if ($exception->getMessage())
        {{ $exception->getMessage() }}
    @else
        {{ __('Sorry, we couldn\'t find the page you\'re looking for. It might have been moved or deleted.') }}
    @endif
@endsection
