@props([
    'param' => 'q',
    'table' => 'default',
    'pageName' => 'page',
    'placeholder' => 'Search',
    'value' => '',
    'label' => 'Search',
])

<div {{ $attributes->merge(['class' => 'mt-6']) }} data-live-search-table data-search-param="{{ $param }}" data-search-table="{{ $table }}" data-page-name="{{ $pageName }}">
    <form method="GET" action="">
        <label for="{{ $param }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>
        <input
            id="{{ $param }}"
            name="{{ $param }}"
            type="search"
            value="{{ $value }}"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            data-live-search
            class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:max-w-md"
        >
    </form>
    <div class="mt-4" data-live-search-results>
        {{ $slot }}
    </div>
</div>
