@extends('errors.layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message')
    {{ __('Whoa there! You\'re sending requests too quickly. Please take a breather and try again in a moment.') }}
@endsection
