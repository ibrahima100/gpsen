<footer class="bg-gp-ink text-gp-sand mt-16">
    <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col md:flex-row justify-between gap-6">
        <div>
            <p class="text-xl font-extrabold">GP<span class="text-gp-orange">sen</span></p>
            <p class="text-sm text-gp-border mt-2 max-w-xs">Envoyez malin, voyagez utile. La mise en relation entre expéditeurs et groupeurs vérifiés.</p>
        </div>
        <div class="flex flex-col md:items-end justify-between gap-2">
            <div class="flex items-center gap-4 text-xs text-gp-border">
                <a href="{{ route('legal.cgu') }}" class="hover:text-gp-orange transition">Conditions Générales</a>
                <span>•</span>
                <a href="{{ route('legal.privacy') }}" class="hover:text-gp-orange transition">Confidentialité (Loi 2008-12)</a>
            </div>
            <p class="text-sm text-gp-border">&copy; {{ date('Y') }} GPsen. Tous droits réservés.</p>
        </div>
    </div>
</footer>
