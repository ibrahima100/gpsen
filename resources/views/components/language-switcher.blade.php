<div class="inline-flex rounded-gp border border-gp-border overflow-hidden text-xs font-bold" role="group" aria-label="Langue">
    @foreach (['fr' => 'FR', 'en' => 'EN'] as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
           class="px-2.5 py-1.5 transition {{ app()->getLocale() === $code ? 'bg-gp-orange text-white' : 'bg-gp-cream text-gp-warm hover:bg-gp-sand' }}">{{ $label }}</a>
    @endforeach
</div>
