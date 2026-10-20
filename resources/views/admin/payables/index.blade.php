@extends('layouts.app')

@section('title', 'Payables')
@section('description', 'Manage tenant bills, balances, and due dates at A6 Apartments.')
@section('eyebrow', 'Staff workspace')
@section('heading', 'Payables')

@section('content')
    <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Manage payables</h2>
                <p class="mt-1 text-sm text-slate-500">These are the bill types you can assign to rooms. Search updates the list as you type.</p>
            </div>
            <a href="{{ route('admin.payables.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                Add payable
            </a>
        </div>

        <x-live-table table="payables" :value="$search" placeholder="Title, status, or description">
            @include('admin.payables.results')
        </x-live-table>
    </section>
@endsection
