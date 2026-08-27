@extends('layouts.app')

@section('title', 'Add tenant')
@section('description', 'Register a new tenant in the A6 Apartments monitoring system.')
@section('eyebrow', 'Tenants')
@section('heading', 'Add tenant')

@section('content')
    <section class="mx-auto max-w-2xl rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">New tenant</h2>
        <p class="mt-1 text-sm text-slate-500">Creates a tenant account they can use to sign in.</p>

        <form method="POST" action="{{ route('admin.tenants.store') }}" class="mt-6 space-y-5">
            @csrf
            @include('admin.tenants.form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Save tenant</button>
                <a href="{{ route('admin.tenants.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</a>
            </div>
        </form>
    </section>
@endsection
