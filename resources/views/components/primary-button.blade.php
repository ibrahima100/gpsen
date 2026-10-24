<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gp-orange border border-transparent rounded-gp font-semibold text-sm text-white tracking-wide shadow-gp hover:bg-gp-orange-dark focus:bg-gp-orange-dark active:scale-95 focus:outline-none focus:ring-2 focus:ring-gp-orange focus:ring-offset-2 transition duration-150']) }}>
    {{ $slot }}
</button>
