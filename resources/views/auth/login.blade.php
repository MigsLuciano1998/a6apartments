@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <p class="text-sm font-medium text-teal-700">Welcome back</p>
    <h2 class="mt-1 text-xl font-semibold sm:text-2xl">Sign in to your account</h2>
    <p class="mt-2 text-sm text-slate-500">Use your staff or tenant credentials to continue.</p>

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm"
                placeholder="you@email.com"
            >
            @error('email')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm"
                placeholder="Enter your password"
            >
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-stone-300 text-teal-700 focus:ring-teal-700" {{ old('remember') ? 'checked' : '' }}>
            Remember me
        </label>

        <button type="submit" class="w-full rounded-2xl bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
            Sign in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        New tenant?
        <a href="{{ route('register') }}" class="font-semibold text-teal-800 hover:underline">Create an account</a>
    </p>
@endsection
