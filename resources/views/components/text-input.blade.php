@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-gp-border text-gp-ink focus:border-gp-orange focus:ring-gp-orange rounded-gp shadow-sm']) }}>
