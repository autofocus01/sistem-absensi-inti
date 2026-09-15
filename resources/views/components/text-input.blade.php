@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-0 bg-surface-container-low font-medium text-on-surface focus:ring-2 focus:ring-primary-container']) }}>