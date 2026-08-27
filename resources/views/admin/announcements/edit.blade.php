@extends('layouts.app')

@section('title', 'Edit announcement')
@section('description', 'Update an A6 Apartments announcement for tenants and staff.')
@section('eyebrow', 'Announcements')
@section('heading', 'Edit announcement')

@section('content')
    <section class="mx-auto max-w-2xl rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">Edit announcement</h2>
        <p class="mt-1 text-sm text-slate-500">Drafts stay hidden from tenants until published.</p>

        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')
            @include('admin.announcements.form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Update announcement</button>
                <a href="{{ route('admin.announcements.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</a>
            </div>
        </form>
    </section>
@endsection
