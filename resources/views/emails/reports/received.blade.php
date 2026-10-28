<x-mail::message>
# Accusé de réception de votre signalement

Bonjour {{ $report->reporter->name }},

Nous accusons bonne réception de votre signalement enregistré sous la référence :  
**Réf. #SIG-{{ $report->id }}**

**Motif déclaré :**  
{{ $report->reason }}

**Date d'enregistrement :**  
{{ $report->created_at->format('d/m/Y à H:i') }}

### Traitement de votre dossier
Notre équipe de modération étudie attentivement chaque alerte afin de préserver la sécurité et la conformité des échanges au sein de notre communauté.

> **Confidentialité absolue :** Conformément à nos engagements de sécurité et à la loi n° 2008-12 sur les données personnelles, ce signalement est instruit de manière totalement confidentielle par l'administration. Aucune information vous concernant n'est transmise à autrui.

Si des informations complémentaires s'avèrent nécessaires, nos services reviendront directement vers vous.

Cordialement,  
L'équipe de modération **GPsen**
</x-mail::message>
