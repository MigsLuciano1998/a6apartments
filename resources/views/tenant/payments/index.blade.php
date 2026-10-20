@extends('layouts.app')

@section('title', 'Payments')
@section('description', 'Review recorded payments for your A6 Apartments account.')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Payments')

@section('content')
    <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">Payment records</h2>
        <p class="mt-1 text-sm text-slate-500">Payments recorded by staff for your account. Search updates the list as you type.</p>

        <x-live-table table="payments" :value="$search" placeholder="Date, room, payable, method, or remarks">
            @include('tenant.payments.results')
        </x-live-table>
    </section>
@endsection
