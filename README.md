# GPsen — Plateforme de Groupage Participatif de Colis

> Projet de Mémoire de Licence 3 — Génie Logiciel (ESTM, Dakar)  
> Stack technique : **Laravel 11**, **Blade**, **Tailwind CSS**, **Alpine.js**, **MySQL (utf8mb4)**.

---

## 🚀 Installation rapide en 5 commandes

Prérequis : **PHP 8.2+**, **Composer**, **Node.js 18+**, **MySQL**.

```bash
# 1. Cloner et entrer dans le projet
git clone https://github.com/ibrahima100/gpsen.git && cd gpsen/gpsen

# 2. Installer les dépendances PHP et JavaScript
composer install && npm install

# 3. Préparer l'environnement et générer la clé
cp .env.example .env && php artisan key:generate

# 4. Exécuter les migrations et le jeu de données démo
php artisan migrate:fresh --seed

# 5. Compiler les assets et démarrer le serveur
npm run build && php artisan serve --port=8000
```

L'application est disponible sur **http://localhost:8000**.

---

## 🔑 Comptes de démonstration

Tous les comptes de test sont initialisés avec les identifiants suivants :

| Rôle | Nom | Email | Mot de passe | Description |
|---|---|---|---|---|
| **Administrateur** | Admin GPsen | `admin@gpsen.sn` | `Admin@2026` | Validation CNI, modération signalements, gestion utilisateurs |
| **Groupeur (validé)** | Fatou Ndiaye | `fatou@gpsen.sn` | `Demo@2026` | Trajets Dakar–Paris avec avis 5 étoiles |
| **Groupeur (validé)** | Moussa Sow | `moussa@gpsen.sn` | `Demo@2026` | Trajets Dakar–Milan |
| **Groupeur (en attente)**| Cheikh Fall | `cheikh@gpsen.sn` | `Demo@2026` | Compte soumis, en attente d'approbation CNI |
| **Groupeur (refusé)** | Ibrahima Ba | `ibrahima@gpsen.sn` | `Demo@2026` | Compte refusé avec motif d'aide |
| **Expéditeur** | Aminata Diop | `aminata@gpsen.sn` | `Demo@2026` | Demandes créées, en cours et livrées |
| **Expéditeur** | Omar Gueye | `omar@gpsen.sn` | `Demo@2026` | Suivi colis et auteur de signalement |

---

## 🧪 Tests automatisés

Pour exécuter l'ensemble de la suite de tests et les tests critiques :

```bash
php artisan test
```

---

## 📂 Architecture et Sécurité

- **Pièces d'identité CNI / Passeport** : Stockées de manière isolée sur le disque local privé (`storage/app/private/id_documents`), jamais accessibles via l'URL publique. Consultation réservée à l'administrateur par flux sécurisé contrôlé.
- **Transactions & Verrous** : Verrou pessimiste `lockForUpdate` pour empêcher toute surréservation de capacité sur les trajets.
- **Confidentialité WhatsApp** : Numéro et lien direct masqués avant acceptation de la demande d'envoi.
- **Machine à états stricte** : `en_attente` → `acceptee` → `collectee` → `en_transit` → `livree`.
