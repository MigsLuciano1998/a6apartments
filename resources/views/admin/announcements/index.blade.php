@extends('layouts.app')

@section('title', 'Announcements')
@section('description', 'Manage published announcements for A6 Apartments tenants and staff.')
@section('eyebrow', 'Staff workspace')
@section('heading', 'Announcements')

@section('content')
    <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Announcements</h2>
                <p class="mt-1 text-sm text-slate-500">Posted notices appear on the tenant announcements page.</p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                Add announcement
            </a>
        </div>

        <x-live-table table="announcements" :value="$search" placeholder="Title, message, or status">
            @include('admin.announcements.results')
        </x-live-table>
    </section>
@endsection
