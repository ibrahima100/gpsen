<x-mail::message>
# Avertissement concernant votre compte GPsen

Bonjour {{ $warning->groupeur->name }},

L'équipe d'administration de **GPsen** vous informe qu'un avertissement officiel a été émis à l'encontre de votre compte groupeur.

**Motif notifié :**  
{{ $warning->reason }}

@if($warning->details)
**Précisions de l'administration :**  
{{ $warning->details }}
@endif

**Délai de régularisation accordé :** **{{ $warning->delay_days }} jours** à compter de la présente notification (jusqu'au {{ $warning->sent_at->addDays($warning->delay_days)->format('d/m/Y') }}).

### Que devez-vous faire ?
1. Vérifiez vos engagements et assurez-vous de respecter strictement les [Conditions Générales d'Utilisation]({{ route('pages.cgu') }}).
2. Traitez sans délai les demandes et signalements en cours auprès de vos expéditeurs.
3. Si vous souhaitez apporter des éclaircissements ou contester cette mesure, contactez directement l'équipe de modération par réponse à ce courriel ou via la plateforme.

> **Rappel important :** Sans régularisation ou réponse satisfaisante de votre part avant l'expiration de ce délai, l'administration se réserve le droit de procéder à la suspension ou à la fermeture manuelle de votre compte.

Merci pour votre rigueur et votre contribution à la sécurité de la communauté,  
L'équipe d'administration **GPsen**
</x-mail::message>
