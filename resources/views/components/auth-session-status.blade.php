@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-secondary mb-4 px-3 py-2 rounded-lg bg-secondary/10']) }}>
        {{ $status }}
    </div>
@endif