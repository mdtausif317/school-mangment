@extends('layouts.error')

@section('title', 'Request Error')
@section('code', $exception->getStatusCode())
@section('icon', 'fa-exclamation-triangle')
@section('heading', 'Request Could Not Be Completed')
@section('message')
    {{ $exception->getMessage() !== '' && $exception->getMessage() !== 'Forbidden' ? $exception->getMessage() : 'The request could not be processed. Please go back and try again.' }}
@endsection
