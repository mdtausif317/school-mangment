@extends('layouts.error')

@section('title', 'Unauthorized')
@section('code', '401')
@section('icon', 'fa-user-lock')
@section('heading', 'Unauthorized')
@section('message')
    You need to be logged in to access this page.
@endsection
