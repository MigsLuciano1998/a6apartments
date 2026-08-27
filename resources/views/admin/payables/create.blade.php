@extends('layouts.app')

@section('title', 'Add payable')
@section('eyebrow', 'Payables')
@section('heading', 'Add payable')

@section('content')
    <section class="mx-auto max-w-2xl rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">New payable</h2>
        <p class="mt-1 text-sm text-slate-500">Use a unique title so room bills stay easy to recognize.</p>

        <form method="POST" action="{{ route('admin.payables.store') }}" class="mt-6 space-y-5">
            @csrf
            @include('admin.payables.form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Save payable</button>
                <a href="{{ route('admin.payables.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</a>
            </div>
        </form>
    </section>
@endsection
