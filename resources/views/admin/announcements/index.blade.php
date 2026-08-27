@extends('layouts.app')

@section('title', 'Announcements')
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

        <form method="GET" action="" class="mt-6">
            <label for="q" class="mb-1.5 block text-sm font-medium">Search</label>
            <input
                id="q"
                name="q"
                type="search"
                value="{{ $search }}"
                autocomplete="off"
                placeholder="Title, message, or status"
                data-live-search
                class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:max-w-md"
            >
        </form>

        <div class="mt-6" data-live-search-results>
            @include('admin.announcements.results')
        </div>
    </section>
@endsection
