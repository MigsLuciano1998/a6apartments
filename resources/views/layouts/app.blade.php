<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo', ['defaultTitle' => 'Dashboard', 'indexable' => false])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f3ee] font-sans text-ink antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <div
            data-sidebar-backdrop
            class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
            aria-hidden="true"
        ></div>

        <aside
            id="app-sidebar"
            data-sidebar
            class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,calc(100%-3rem))] -translate-x-full flex-col overflow-y-auto border-teal-900/10 bg-teal-950 text-teal-50 shadow-2xl transition-transform duration-200 ease-out data-[open]:translate-x-0 lg:static lg:z-auto lg:w-auto lg:translate-x-0 lg:border-r lg:shadow-none"
        >
            <div class="flex items-center justify-between gap-3 px-5 py-5">
                <a href="{{ auth()->user()->homeUrl() }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-500 text-sm font-semibold text-white">A6</span>
                    <span>
                        <span class="block text-sm font-semibold">A6 Apartments</span>
                        <span class="block text-xs text-teal-200/70">Monitoring System</span>
                    </span>
                </a>
                <button
                    type="button"
                    data-close-sidebar
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-teal-100 transition hover:bg-white/10 lg:hidden"
                    aria-label="Close menu"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <nav class="space-y-1 px-3 pb-5">
                @php
                    $isStaff = auth()->user()->isStaff();
                    $navClass = fn (bool $active) => $active
                        ? 'flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white'
                        : 'flex items-center gap-3 rounded-2xl px-4 py-3 text-sm text-teal-100/80 transition hover:bg-white/5';
                @endphp

                @if ($isStaff)
                    <a href="{{ route('admin.dashboard') }}" class="{{ $navClass(request()->routeIs('admin.dashboard')) }}">
                        @if (request()->routeIs('admin.dashboard'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Dashboard
                    </a>
                    @if (auth()->user()->isManager())
                        <a href="{{ route('admin.admins.index') }}" class="{{ $navClass(request()->routeIs('admin.admins.*')) }}">
                            @if (request()->routeIs('admin.admins.*'))
                                <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                            @endif
                            Administrators
                        </a>
                    @endif
                    <a href="{{ route('admin.rooms.index') }}" class="{{ $navClass(request()->routeIs('admin.rooms.*')) }}">
                        @if (request()->routeIs('admin.rooms.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Rooms
                    </a>
                    <a href="{{ route('admin.tenants.index') }}" class="{{ $navClass(request()->routeIs('admin.tenants.*')) }}">
                        @if (request()->routeIs('admin.tenants.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Tenants
                    </a>
                    <a href="{{ route('admin.payables.index') }}" class="{{ $navClass(request()->routeIs('admin.payables.*')) }}">
                        @if (request()->routeIs('admin.payables.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Payables
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="{{ $navClass(request()->routeIs('admin.payments.*')) }}">
                        @if (request()->routeIs('admin.payments.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Payments
                    </a>
                    <a href="{{ route('admin.expenses.index') }}" class="{{ $navClass(request()->routeIs('admin.expenses.*')) }}">
                        @if (request()->routeIs('admin.expenses.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Expenses
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="{{ $navClass(request()->routeIs('admin.announcements.*')) }}">
                        @if (request()->routeIs('admin.announcements.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Announcements
                    </a>
                    <a href="{{ route('user.edit') }}" class="{{ $navClass(request()->routeIs('user.*')) }}">
                        @if (request()->routeIs('user.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        User
                    </a>
                @else
                    <a href="{{ route('tenant.dashboard') }}" class="{{ $navClass(request()->routeIs('tenant.dashboard') || request()->routeIs('tenant.rooms.billing')) }}">
                        @if (request()->routeIs('tenant.dashboard') || request()->routeIs('tenant.rooms.billing'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Dashboard
                    </a>
                    <a href="{{ route('tenant.payments.index') }}" class="{{ $navClass(request()->routeIs('tenant.payments.*')) }}">
                        @if (request()->routeIs('tenant.payments.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Payments
                    </a>
                    <a href="{{ route('tenant.announcements.index') }}" class="{{ $navClass(request()->routeIs('tenant.announcements.*')) }}">
                        @if (request()->routeIs('tenant.announcements.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        Announcements
                    </a>
                    <a href="{{ route('user.edit') }}" class="{{ $navClass(request()->routeIs('user.*')) }}">
                        @if (request()->routeIs('user.*'))
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                        @endif
                        User
                    </a>
                @endif
            </nav>
        </aside>

        <div class="flex min-h-screen flex-col">
            <header class="flex items-center justify-between gap-3 border-b border-stone-200 bg-white/80 px-4 py-4 backdrop-blur-md sm:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        data-open-sidebar
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-stone-200 bg-white text-slate-700 transition hover:border-teal-700 hover:text-teal-800 lg:hidden"
                        aria-label="Open menu"
                        aria-expanded="false"
                        aria-controls="app-sidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-teal-700">@yield('eyebrow', 'Workspace')</p>
                        <h1 class="truncate text-lg font-semibold">@yield('heading', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('user.edit') }}" class="hidden text-right hover:text-teal-800 sm:block">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-slate-500">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </a>
                    <a href="{{ route('user.edit') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-sm font-semibold text-teal-800" aria-label="User page">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1).substr(auth()->user()->last_name, 0, 1)) }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-stone-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-teal-700 hover:text-teal-800">
                            Log out
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-8 sm:py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-900">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
