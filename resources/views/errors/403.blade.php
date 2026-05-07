@extends('errors.layout')

@section('title', __('Access Denied'))
@section('code', '403')
@section('message')
    @if($exception->getMessage())
        {{ $exception->getMessage() }}
    @else
        {{ __('You do not have permission to access this resource. Please contact your administrator if you think this is a mistake.') }}
    @endif
@endsection
