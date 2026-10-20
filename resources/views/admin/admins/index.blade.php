@extends('layouts.app')

@section('title', 'Admins')
@section('description', 'Manage staff accounts for the A6 Apartments monitoring system.')
@section('eyebrow', 'Manager workspace')
@section('heading', 'Admins')

@section('content')
    <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Manage admin users</h2>
                <p class="mt-1 text-sm text-slate-500">Only managers can create admin accounts. Search updates the list as you type.</p>
            </div>
            <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                Add admin
            </a>
        </div>

        <x-live-table table="admins" :value="$search" placeholder="Name, email, phone, or status">
            @include('admin.admins.results')
        </x-live-table>
    </section>
@endsection
