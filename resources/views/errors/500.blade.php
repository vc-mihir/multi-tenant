@extends('errors.layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
    {{ __('Whoops! Something went wrong on our end. We\'ve been notified and are looking into it. Please try again later.') }}
@endsection
