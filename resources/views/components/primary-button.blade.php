<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary-container border border-transparent rounded-lg font-semibold text-sm text-on-primary hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>