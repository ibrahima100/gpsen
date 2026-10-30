<footer class="bg-gp-ink text-gp-sand mt-16 border-t border-white/10">
    <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col md:flex-row justify-between gap-6">
        <div>
            <p class="text-xl font-extrabold">GP<span class="text-gp-orange">sen</span></p>
            <p class="text-sm text-gp-border mt-2 max-w-xs leading-relaxed">
                Envoyez malin, voyagez utile. La mise en relation entre expéditeurs et groupeurs vérifiés.
            </p>
            <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-gp-sand">
                <span class="text-gp-orange">Contact & Support :</span>
                <a href="tel:+221778744844" class="hover:text-gp-orange transition font-mono tracking-wide">+221 77 874 48 44</a>
            </div>
        </div>
        <div class="flex flex-col md:items-end justify-between gap-3">
            <div class="flex flex-wrap items-center gap-4 text-xs text-gp-border">
                <a href="{{ route('pages.about') }}" class="hover:text-gp-orange transition font-semibold text-white">À propos de nous</a>
                <span>•</span>
                <a href="{{ route('legal.cgu') }}" class="hover:text-gp-orange transition">Conditions Générales</a>
                <span>•</span>
                <a href="{{ route('legal.privacy') }}" class="hover:text-gp-orange transition">Confidentialité (Loi 2008-12)</a>
            </div>
            <div class="text-xs text-gp-border space-y-1 text-left md:text-right">
                <p>&copy; {{ date('Y') }} GPsen. Tous droits réservés.</p>
                <p class="text-[11px] text-gp-border/80">
                    Développé par <span class="font-bold text-white hover:text-gp-orange transition">Ibrahima Tall</span>
                </p>
            </div>
        </div>
    </div>
</footer>
