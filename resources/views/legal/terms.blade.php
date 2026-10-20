@extends('layouts.guest')

@section('title', 'Terms and Conditions')
@section('description', 'Terms and conditions for tenant accounts at A6 Apartments.')

@section('content')
    <p class="text-sm font-medium text-teal-700">A6 Apartments</p>
    <h2 class="mt-1 text-xl font-semibold sm:text-2xl">Terms and Conditions</h2>
    <p class="mt-2 text-sm text-slate-500">Please read these terms before creating a tenant account.</p>

    <div class="mt-7">
        @include('legal.terms-content')
    </div>

    <p class="mt-8 text-center text-sm text-slate-500">
        <a href="{{ route('register') }}" class="font-semibold text-teal-800 hover:underline">Back to registration</a>
    </p>
@endsection
