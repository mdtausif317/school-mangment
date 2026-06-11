@extends('layouts.error')

@section('title', 'Server Error')
@section('code', '500')
@section('icon', 'fa-server')
@section('heading', 'Something Went Wrong')
@section('message')
    We're sorry, but something went wrong on our end. Please try again later.
@endsection
