<div {{ $attributes->merge(['class' => 'relative']) }}>
    <span class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-4 text-sm text-slate-500">₱</span>
    {{ $slot }}
</div>
