@php
    $isDelivered = in_array($request->status?->value ?? (string)$request->status, ['livree']);
    $user = auth()->user();
    $myReview = \App\Models\Review::where('shipment_request_id', $request->id)->where('author_id', $user->id)->first();
    $otherReview = \App\Models\Review::where('shipment_request_id', $request->id)->where('author_id', '!=', $user->id)->first();
    $isSender = $request->sender_id === $user->id;
    $targetName = $isSender ? $request->trip->groupeur->name : $request->sender->name;
    $targetRole = $isSender ? 'le groupeur' : 'l\'expéditeur';
@endphp

@if($isDelivered)
    <div class="bg-white p-6 rounded-gp border border-gp-border shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-gp-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gp-ink flex items-center gap-2">
                <svg class="w-4 h-4 text-gp-orange" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Évaluation & Avis
            </h3>
            <span class="text-xs text-gp-warm">Demande livrée</span>
        </div>

        @if(session('status'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-gp text-xs text-emerald-800 font-semibold">
                {{ session('status') }}
            </div>
        @endif

        {{-- Formulaire ou affichage de l'avis de l'utilisateur connecté --}}
        @if($myReview)
            <div class="p-4 bg-gp-sand/30 rounded-gp border border-gp-border">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gp-ink uppercase tracking-wider">Votre avis sur {{ $targetName }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Déposé</span>
                </div>
                <div class="flex items-center gap-1 mt-2 text-gp-orange">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="text-base">{{ $i <= $myReview->rating ? '★' : '☆' }}</span>
                    @endfor
                    <span class="text-xs font-bold text-gp-ink ml-1">{{ $myReview->rating }}/5</span>
                </div>
                @if($myReview->comment)
                    <p class="text-xs text-gp-warm mt-2 italic bg-white p-3 rounded border border-gp-border/60">
                        "{{ $myReview->comment }}"
                    </p>
                @endif
                <span class="text-[10px] text-gp-warm/70 mt-2 block">Déposé le {{ $myReview->created_at->translatedFormat('d M Y à H:i') }}</span>
            </div>
        @else
            <form method="POST" action="{{ route('reviews.store', $request) }}" x-data="{ rating: 5, hoverRating: 0 }" class="space-y-3">
                @csrf
                <p class="text-xs text-gp-warm">
                    Comment s'est passée votre expérience avec <strong>{{ $targetName }}</strong> ({{ $targetRole }}) ?
                </p>

                <div>
                    <label class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1">Votre note</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="rating = {{ $i }}" 
                                    @mouseenter="hoverRating = {{ $i }}" 
                                    @mouseleave="hoverRating = 0"
                                    class="text-2xl transition focus:outline-none"
                                    :class="(hoverRating || rating) >= {{ $i }} ? 'text-gp-orange' : 'text-gp-border hover:text-gp-orange'">
                                ★
                            </button>
                        @endfor
                        <span class="text-xs font-bold text-gp-ink ml-2" x-text="rating + ' / 5 étoiles'"></span>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    @error('rating')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="review-comment" class="block text-xs font-bold text-gp-ink uppercase tracking-wider mb-1">
                        Commentaire (optionnel)
                    </label>
                    <textarea id="review-comment" 
                              name="comment" 
                              rows="3" 
                              maxlength="500" 
                              placeholder="Partagez quelques mots sur la ponctualité, le soin apporté au colis, la communication..."
                              class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange"></textarea>
                    @error('comment')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-gp bg-gp-orange hover:bg-gp-orange-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                    Publier mon avis
                </button>
            </form>
        @endif

        {{-- Affichage de l'avis reçu de l'autre partie --}}
        @if($otherReview)
            <div class="pt-3 border-t border-gp-border">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gp-ink">Avis de {{ $otherReview->author->name }} à votre sujet</span>
                    <span class="text-xs font-bold text-gp-orange">{{ $otherReview->rating }}/5 ★</span>
                </div>
                @if($otherReview->comment)
                    <p class="text-xs text-gp-warm mt-1.5 italic bg-gp-sand/20 p-2.5 rounded border border-gp-border/50">
                        "{{ $otherReview->comment }}"
                    </p>
                @endif
            </div>
        @endif
    </div>
@endif
