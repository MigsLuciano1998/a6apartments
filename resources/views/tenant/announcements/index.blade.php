@extends('layouts.app')

@section('title', 'Announcements')
@section('description', 'Read the latest A6 Apartments announcements for your unit.')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Announcements')

@section('content')
    <div class="space-y-4">
        @forelse ($announcements as $announcement)
            <article class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ $announcement->created_at?->format('M d, Y') }}</p>
                <h2 class="mt-2 text-xl font-semibold">{{ $announcement->title }}</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $announcement->body }}</p>
                @if ($announcement->author)
                    <p class="mt-4 text-xs text-slate-400">Posted by {{ $announcement->author->name }}</p>
                @endif
            </article>
        @empty
            <section class="rounded-[28px] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm text-slate-500">No announcements yet.</p>
            </section>
        @endforelse

        @if ($announcements->hasPages())
            <div class="pt-2">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
@endsection
