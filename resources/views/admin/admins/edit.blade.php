@extends('layouts.app')

@section('title', 'Edit admin')
@section('description', 'Update a staff account for the A6 Apartments monitoring system.')
@section('eyebrow', 'Admins')
@section('heading', 'Edit admin')

@section('content')
    <section class="mx-auto max-w-2xl rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">Edit {{ $admin->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">Leave the password blank to keep the current password.</p>

        <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')
            @include('admin.admins.form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Update admin</button>
                <a href="{{ route('admin.admins.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</a>
            </div>
        </form>
    </section>
@endsection
