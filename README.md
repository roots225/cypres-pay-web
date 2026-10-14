# 🇨🇮 Cypres Pay — Plateforme E-commerce BNPL (Côte d'Ivoire)

> **Cypres Pay** est une solution e-commerce complète avec facilités de paiement échelonné (**Buy Now, Pay Later — BNPL**) adaptée aux réalités économiques de la Côte d'Ivoire et de la zone UEMOA. Conçue à partir du reverse-engineering technique de _credipay.ci_ et magnifiée par un design moderne haute performance.

---

## 📑 Sommaire

1. [Architecture & Reverse Engineering de Référence](#1-architecture--reverse-engineering-de-référence)
2. [Identité Visuelle & Design System](#2-identité-visuelle--design-system)
3. [Stack Technique](#3-stack-technique)
4. [Moteur Financier & Règles Métier BNPL](#4-moteur-financier--règles-métier-bnpl)
5. [Modules & Portails Fonctionnels](#5-modules--portails-fonctionnels)
6. [Démarrage Rapide (Docker)](#6-démarrage-rapide-docker)
7. [Comptes de Démonstration](#7-comptes-de-démonstration)
8. [Structure du Projet](#8-structure-du-projet)

---

## 1. Architecture & Reverse Engineering de Référence

L'analyse approfondie des flux de _credipay.ci_ a mis en évidence les éléments structurants suivants, intégralement réimplémentés dans **Cypres Pay** :

- **Double tarification systématique** : Chaque produit affiche un prix **Crédit BNPL** et un prix **Comptant Cash** bénéficiant d'une remise immédiate de **7%**.
- **Système de codes d'articles séquentiels** : Références explicites (ex: `N°019 - Pack Électroménager`, `N°731 - Téléviseur Millennium Smart TV`, `N°461 - Ciment Bélier`).
- **Preuve d'authenticité par Quittance à 6 caractères** : Chaque versement (apport initial ou mensualité) génère un code cryptographique unique à 6 caractères sans ambiguïté visuelle (ex: `28B7S0`), immédiatement vérifiable publiquement contre la fraude.
- **Intégration Mobile Money ivoirienne** : Prise en charge native de **Wave Côte d'Ivoire**, **Orange Money CI**, **MTN MoMo**, **Moov Money** et encaissement physique en caisse d'agence.
- **Signature numérique sur canvas** : Signature électronique tactile au doigt ou au stylet pour le contrat de crédit lors du checkout.

---

## 2. Identité Visuelle & Design System

Le design est calqué sur les spécifications exactes fournies :

- **Police** : `Plus Jakarta Sans`
- **Icônes** : `Google Material Symbols Outlined`
- **Couleurs Principales** :
  - Rouge Cypres : `#b70011` (`primary`) / `#dc2626` (`primary-container`)
  - Bleu Nuit Profond : `#081B4B` (`cobalt-deep`)
  - Arrière-plan Canvas : `#F8FAFC` (`canvas-background`)
  - Vert Succès / Agro : `#006646` (`tertiary`) / `#00825a` (`tertiary-container`)
- **Couleurs Opérateurs Mobile Money** :
  - Wave CI : `#1DA1F2`
  - Orange Money : `#FF7900`
  - MTN MoMo : `#FFCC00`
  - Moov Money : `#005CA9`

---

## 3. Stack Technique

- **Backend** : Laravel 11.x (PHP 8.3 FPM)
- **Frontend Réactif** : Livewire 3.5 + React 18 (pour les calculateurs dynamiques complexes et la signature tactile)
- **Styling** : Tailwind CSS v3 avec configuration d'extensions de tokens de design
- **Base de données** : MySQL 8.0 (schéma relationnel strict avec clés étrangères et index de recherche)
- **Cache & Files d'attente** : Redis 7.0
- **Serveur Web** : Nginx 1.25 Alpine
- **Conteneurisation** : Docker & Docker Compose (`docker-compose.yml`)

---

## 4. Moteur Financier & Règles Métier BNPL

Le service `App\Services\BnplCalculationService` gère l'ensemble de l'amortissement :

1. **Option Comptant (Cash)** :
   $$\text{Montant Final} = \text{Prix Normal} \times 0.93 \quad (-7\%)$$
2. **Formules Échelonnées (Crédit)** :
   - **30 Jours Journalier** : Majoration +10%, apport 30%, 30 versements quotidiens.
   - **60 Jours Bi-mensuel** : Majoration +15%, apport 25%, 4 versements.
   - **3 Mois (90 Jours)** : Majoration +20%, apport 20%, 3 mensualités.
   - **6 Mois (180 Jours)** : Majoration +28%, apport 20%, 6 mensualités.
   - **12 Mois** : Majoration +40%, apport 15%, 12 mensualités.
   - **18 Mois & 24 Mois** : Grands équipements, kits solaires complets et BTP.
3. **Livraison Offerte** : Automatique dès 100 000 FCFA d'achats à Abidjan.
4. **Générateur de Reçus `PaymentReceiptService`** :
   - Codes de 6 caractères générés sans caractères ambigus (`0`/`O`, `1`/`I`).
   - Vérifiable publiquement par l'URL `/verifier?code=XXXXXX`.

---

## 5. Modules & Portails Fonctionnels

| Portail / Page               | Route                   | Description                                                                     |
| ---------------------------- | ----------------------- | ------------------------------------------------------------------------------- |
| **Accueil & Ventes Flash**   | `/`                     | Hero BNPL, compte à rebours flash, kits populaires, avis vérifiés               |
| **Catalogue Général**        | `/catalogue`            | Filtres par catégorie, curseur de prix, recherche Livewire instantanée          |
| **Kits Maison & Cuisine**    | `/kits-maison`          | Packs complets (ex: N°019 Pack Électroménager, N°018 Pack Salon)                |
| **Fiche Produit**            | `/produits/{id}-{slug}` | Sélecteur Cash vs Échéances React, fiche technique, ajout au panier             |
| **Simulateur BNPL**          | `/simulateur`           | Simulateur interactif React (Prix d'article vs Budget mensuel)                  |
| **Vérificateur Anti-Fraude** | `/verifier`             | Contrôle d'authenticité en direct des reçus de versement (Livewire)             |
| **Checkout & Signature**     | `/commander`            | Récapitulatif, choix de formule, signature digitale tactile, Mobile Money       |
| **Espace Client**            | `/client/dashboard`     | Suivi de l'échéancier, montant restant, bouton de paiement Wave                 |
| **Portail Commercial**       | `/commercial/dashboard` | Suivi des commissions (3%), code de parrainage, saisie terrain                  |
| **Portail Caisse Agence**    | `/caisse/dashboard`     | Encaissement physique en agence, émission instantanée de reçus 6 car.           |
| **Console Administration**   | `/admin`                | Dashboard KPIs, gestion séparée des users, catalogue, formules, KYC, quittances |

---

## 6. Démarrage Rapide (Docker)

### Option 1 : Démarrage en 1 commande (recommandé)

```bash
./start.sh
```

### Option 2 : Via le Makefile

```bash
# 1. Démarrer les conteneurs
make up

# 2. Exécuter les migrations et charger les données du catalogue CI
make fresh
```

L'application est ensuite accessible sur : **http://localhost:8080**

---

## 7. Comptes de Démonstration

Les utilisateurs suivants sont pré-chargés par le seeder (`UserSeeder`) avec accès rapide en 1-clic depuis `/connexion` :

| Rôle                | Téléphone / Email                         | Mot de passe        | Accès Direct            | Droits & Modules                                                                                                      |
| ------------------- | ----------------------------------------- | ------------------- | ----------------------- | --------------------------------------------------------------------------------------------------------------------- |
| **Administrateur**  | `0700000001` / `admin@cypresspay.ci`      | `CypressAdmin2026!` | `/admin`                | Accès complet : Gestion Clients, Commerciaux, Caissiers, Admins, Catalogue, Formules BNPL, Commandes, KYC, Quittances |
| **Commercial**      | `0586708161` / `commercial@cypresspay.ci` | `Commercial2026!`   | `/commercial/dashboard` | Code parrainage (`COM-YOP-01`), suivi commissions (3%), saisie ventes terrain                                         |
| **Caissier Agence** | `0100000003` / `caisse@cypresspay.ci`     | `Caisse2026!`       | `/caisse/dashboard`     | Encaissements physiques guichet, émission reçus 6 car.                                                                |
| **Client**          | `0708091011` / `client@cypresspay.ci`     | `Client2026!`       | `/client/dashboard`     | Échéancier personnel, quittances et paiement Wave                                                                     |

---

## 8. Structure du Projet

```
cypres-pay-web/
├── app/
│   ├── Http/Controllers/    # Contrôleurs web & API
│   ├── Livewire/            # Composants réactifs (SearchBar, ProductCatalog, ReceiptVerifier, CartDrawer)
│   ├── Models/              # Modèles Eloquent (Product, Order, Installment, PaymentReceipt, etc.)
│   └── Services/            # Moteurs financiers (BnplCalculationService, PaymentReceiptService, MobileMoneyService)
├── config/
│   └── bnpl.php             # Règles d'apport, remises cash et seuils CI
├── database/
│   ├── migrations/          # Schémas de tables MySQL
│   └── seeders/             # Catalogue produits CI réels, formules d'échéances et utilisateurs
├── docker/
│   ├── nginx/               # Configuration Nginx FastCGI
│   └── php/                 # Dockerfile PHP 8.3 FPM optimisé
├── resources/
│   ├── css/                 # Directives Tailwind et polices
│   ├── js/
│   │   ├── components/      # Composants React (BnplSimulator, DigitalSignatureCanvas, etc.)
│   │   └── app.js           # Gestionnaire de panier LocalStorage & toasts
│   └── views/               # Vues Blade (layouts, home, catalog, product, checkout, dashboards)
├── routes/
│   └── web.php              # Définition des routes
├── docker-compose.yml       # Orchestration des 6 services conteneurisés
├── Makefile                 # Raccourcis de commandes
├── start.sh                 # Script d'initialisation automatique
└── README.md
```

---

_Développé pour l'inclusion financière et l'accès facile aux équipements en Côte d'Ivoire._ 🇨🇮
