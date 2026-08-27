<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo', ['defaultTitle' => 'A6 Apartments', 'indexable' => true])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-sand font-sans text-ink antialiased">
    <div class="relative min-h-dvh overflow-x-hidden">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -left-16 h-80 w-80 rounded-full bg-teal-200/50 blur-3xl"></div>
            <div class="absolute top-1/3 -right-20 h-96 w-96 rounded-full bg-amber-200/40 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-teal-300/30 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid min-h-dvh max-w-7xl lg:grid-cols-2 lg:items-center">
            <aside class="hidden flex-col justify-center p-10 lg:flex xl:p-14">
                <div class="max-w-md">
                    <a href="{{ route('login') }}" class="mb-4 inline-flex max-w-full items-center gap-3">
                        @if (file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="A6 Apartments" class="h-auto w-full max-w-xs object-contain">
                        @else
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-700 text-sm font-semibold text-white">A6</span>
                            <span class="text-sm font-semibold tracking-wide text-teal-800">A6 Apartments</span>
                        @endif
                    </a>

                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-teal-700">Property operations</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight text-ink">Stay on top of rooms, bills, and tenants in one place.</h1>
                    <p class="mt-4 text-base leading-relaxed text-slate-600">Track assignments, payables, and payments with a workspace built for managers, admins, and tenants.</p>
                    <p class="mt-8 text-sm text-slate-500">Secure access for registered tenants and staff.</p>
                </div>
            </aside>

            <main class="flex min-h-dvh items-start px-4 py-6 sm:px-8 sm:py-10 lg:min-h-0 lg:px-12 lg:py-12">
                <div class="mx-auto w-full min-w-0 max-w-lg lg:ml-auto">
                    <div class="mb-6 flex items-center gap-3 lg:hidden">
                        @if (file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="A6 Apartments" class="h-10 w-auto max-w-[9rem] object-contain">
                        @else
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-teal-700 text-sm font-semibold text-white">A6</span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-semibold">A6 Apartments</p>
                            <p class="text-xs text-slate-500">Monitoring System</p>
                        </div>
                    </div>

                    <div class="rounded-[24px] border border-white/70 bg-white/80 p-5 shadow-xl shadow-teal-900/5 backdrop-blur-xl sm:rounded-[28px] sm:p-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
