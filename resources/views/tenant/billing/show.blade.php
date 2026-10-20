@extends('layouts.app')

@section('title', 'Billing · Room '.$room->room_number)
@section('description', 'Published bills for room '.$room->room_number.' at A6 Apartments.')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Room billing')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Billing for room {{ $room->room_number }}</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Published payables
                    @if ($room->room_name)
                        · {{ $room->room_name }}
                    @endif
                </p>
            </div>
            <a href="{{ route('tenant.dashboard') }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Back to dashboard</a>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <x-live-table table="billings" :value="$search" placeholder="Payable, balance, or due date" class="mt-0">
                @include('tenant.billing.results')
            </x-live-table>
        </div>
    </section>
@endsection
