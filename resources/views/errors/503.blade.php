@extends('errors.layout')

@section('title', __('Under Maintenance'))
@section('code', '503')
@section('message')
    {{ __('We\'re currently performing some scheduled maintenance to improve your experience. We\'ll be back online shortly!') }}
@endsection
