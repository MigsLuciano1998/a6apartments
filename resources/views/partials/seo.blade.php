@php
    $pageTitle = trim($__env->yieldContent('title', $defaultTitle ?? 'A6 Apartments'));
    $fullTitle = str_contains($pageTitle, 'A6 Apartments')
        ? $pageTitle
        : $pageTitle.' — A6 Apartments';
    $description = trim($__env->yieldContent(
        'description',
        'A6 Apartments monitoring system for rooms, tenants, bills, payments, and announcements.'
    ));
    $icon = asset('images/icon.png');
    $ogImage = file_exists(public_path('images/logo.png'))
        ? asset('images/logo.png')
        : $icon;
@endphp
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta name="author" content="A6 Apartments">
<link rel="canonical" href="{{ url()->current() }}">
@if (empty($indexable))
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow">
@endif
<link rel="icon" href="{{ $icon }}" type="image/png">
<link rel="shortcut icon" href="{{ $icon }}" type="image/png">
<link rel="apple-touch-icon" href="{{ $icon }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="A6 Apartments">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $icon }}">
