@extends('layouts.error')

@section('title', 'Access Denied')
@section('code', '403')
@section('icon', 'fa-lock')
@section('heading', 'Access Denied')
@section('message')
    {{ $exception->getMessage() !== 'Forbidden' ? $exception->getMessage() : 'You do not have permission to access this page. Contact your administrator if you believe this is a mistake.' }}
@endsection
