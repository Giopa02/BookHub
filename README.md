# 📚 BookHub - Bibliothèque Virtuelle

BookHub est une application web de gestion de bibliothèque développée en PHP avec le framework Laravel 12. Elle permet aux usagers d'emprunter des livres et aux bibliothécaires de gérer le catalogue, les exemplaires et les usagers via un back-office dédié.

> Projet réalisé dans le cadre du BTS SIO SLAM — H3 Hitema, Paris.

---

## 🚀 Fonctionnalités

### Fonctionnalités clés
- **Inscription / Connexion** avec validation des données et hashage bcrypt
- **Double authentification (2FA)** par email avec code à 6 chiffres (expiration 10 min)
- **Catalogue de livres** avec recherche par titre, auteur ou catégorie
- **Système d'emprunt** : un usager peut emprunter de 1 à 5 exemplaires pour 30 jours
- **Gestion des retours** : le bibliothécaire enregistre le retour et les exemplaires redeviennent disponibles
- **Profil utilisateur** : consultation de l'emprunt en cours et de l'historique
- **Back-office bibliothécaire** : CRUD des exemplaires, gestion des usagers, recherche avancée
- **Pagination** sur le catalogue, la liste des exemplaires et la liste des usagers

### Fonctionnalités optionnelles
- **Validation RGPD du mot de passe** : minimum 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial
- **Historique des mots de passe** : impossible de réutiliser les 5 derniers, date du dernier changement affichée
- **État physique des exemplaires** : excellent, bon ou moyen (géré par le bibliothécaire)
- **Recherche côté serveur** dans le back-office des exemplaires

### Rôles utilisateurs
| Rôle | Accès |
|------|-------|
| Visiteur | Catalogue, recherche, inscription, connexion |
| Usager | Emprunts, profil, changement de mot de passe |
| Bibliothécaire | Back-office complet (exemplaires, usagers, retours) |

---

## 🏗️ Architecture

Le projet suit l'architecture **MVC (Modèle-Vue-Contrôleur)** de Laravel :

```
Navigateur → Routes (web.php) → Contrôleurs → Modèles Eloquent → Base de données MySQL
                                      ↓
                                Vues Blade (.blade.php)
```



**10 tables** : roles, users, authors, categories, books, book_category, statuses, copies, borrows, borrow_copy, password_histories, sessions

---

## ⚙️ Installation

### Prérequis
- PHP 8.5+
- Composer
- MySQL (via MAMP ou autre)
- Node.js (optionnel, pour les assets)
- Git

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-utilisateur/BookHub.git
cd BookHub

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bookhub
# DB_USERNAME=votre_utilisateur
# DB_PASSWORD=votre_mot_de_passe

# 5. Configurer le mail (mode log pour le développement)
# MAIL_MAILER=log

# 5. Configurer le mail (mode log pour le développement)
# MAIL_MAILER=log

# 6. Créer la base de données dans phpMyAdmin, puis migrer et seeder
php artisan migrate:fresh --step --seed

# 7. Lancer le serveur
php artisan serve
```

### Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Bibliothécaire | marie.dupont@bookhub.fr | password |
| Bibliothécaire | jean.bernard@bookhub.fr | password |
| Bibliothécaire | claire.lefebvre@bookhub.fr | password |
| Usager | lucas.martin@email.com | password |

### Lancer les tests

```bash
php artisan test
```

> 40 tests unitaires couvrant les modèles, contrôleurs, validations et fonctionnalités métier.

---

## 🧪 Technologies

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| PHP | 8.5 | Langage backend |
| Laravel | 12 | Framework MVC |
| MySQL | 8 | Base de données relationnelle |
| Blade | - | Moteur de templates |
| Bootstrap | 5.3 | Framework CSS |
| PHPUnit | 12 | Tests unitaires |
| Faker | - | Génération de données réalistes |
| Bcrypt | - | Hashage des mots de passe |
| Git | - | Gestion de versions |

---

## 📁 Structure du projet

```
BookHub/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── BorrowController.php       # Gestion des emprunts
│   │       ├── CopyController.php         # Gestion des copies
│   │       ├── ExamplarController.php     # Gestion des exemplaires
│   │       ├── SearchController.php       # Recherche
│   │       └── UserController.php         # Utilisateurs / auth
│   ├── Models/
│   │   ├── Author.php
│   │   ├── Book.php
│   │   ├── Borrow.php
│   │   ├── Category.php
│   │   ├── Copy.php
│   │   ├── PasswordHistory.php
│   │   ├── Role.php
│   │   ├── Status.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── database/
│   ├── migrations/                        # 6 migrations (roles, users, books, copies, borrows, features optionnelles)
│   ├── seeders/                           # BookSeeder, BorrowSeeder, CopySeeder, RoleSeeder, StatusSeeder, UserSeeder
│   └── factories/
│       └── UserFactory.php
│
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── connect.blade.php          # Connexion
│       │   ├── subscription.blade.php     # Inscription
│       │   ├── change_password.blade.php
│       │   └── two_factor.blade.php       # 2FA
│       ├── bo/                            # Back-office
│       │   ├── copies.blade.php
│       │   ├── exemplar_form.blade.php
│       │   ├── profil.blade.php
│       │   └── profils.blade.php
│       ├── header.blade.php
│       ├── footer.blade.php
│       ├── template.blade.php             # Layout principal
│       ├── index.blade.php                # Page d'accueil
│       ├── search.blade.php
│       ├── borrowing.blade.php
│       ├── exemplar.blade.php
│       └── profil.blade.php
│
├── routes/
│   └── web.php
│
├── public/
│   ├── css/                               # Styles (app.css, style.css, normalize.css…)
│   ├── js/                                # Scripts (jQuery, plugins…)
│   └── images/                            # Assets visuels
│
├── lang/fr/                               # Traductions françaises
│
├── tests/
│   ├── Feature/
│   │   ├── ControllerTest.php
│   │   ├── ModelRelationTest.php
│   │   └── ValidationTest.php
│   └── Unit/
│       └── ExampleTest.php
│
├── config/                                # Configs Laravel standard
├── routes/web.php
└── vite.config.js

```

---

## 🛑 Difficultés rencontrées

- **Ordre des migrations** : les clés étrangères nécessitent que les tables référencées existent déjà. Le timestamp du fichier de migration détermine l'ordre d'exécution, ce qui a causé des erreurs `Failed to open the referenced table` quand les timestamps n'étaient pas correctement ordonnés.

- **Système de layout Blade** : la compréhension du découpage en partials (`@include`, `@extends`, `@yield`) a demandé un temps d'adaptation, notamment pour éviter de dupliquer le DOCTYPE et le `<head>` dans chaque partial.

- **Compatibilité PHPUnit 12** : les annotations `/** @test */` ne sont plus supportées dans PHPUnit 12 — il faut préfixer les méthodes par `test_`. Ce changement de version a causé un "No tests found" silencieux.

- **Validation du mot de passe** : l'utilisation de plusieurs règles `regex` séparées dans Laravel affichait le même message d'erreur pour chacune. La solution a été de combiner les trois conditions en une seule expression régulière.

- **2FA et sessions** : la gestion du flux de connexion en deux étapes (login → code 2FA) nécessite de stocker l'ID utilisateur en session sans le connecter, puis de le connecter après vérification du code.

---

## ✨ Améliorations apportées

Par rapport au cahier des charges initial, les améliorations suivantes ont été implémentées :

- **Double authentification (2FA)** par email avec code temporaire et option de renvoi
- **Politique de mot de passe RGPD** avec validation en temps réel (majuscule, chiffre, caractère spécial)
- **Historique des 5 derniers mots de passe** empêchant la réutilisation, avec affichage de la date du dernier changement
- **État physique des exemplaires** (excellent, bon, moyen) visible dans le catalogue et modifiable dans le back-office
- **Recherche côté serveur** dans le back-office avec filtre sur tous les champs
- **Pagination** sur toutes les listes (catalogue, exemplaires, usagers)
- **Protection du back-office** avec vérification du rôle bibliothécaire sur chaque route
- **Données de test réalistes** : 60 vrais auteurs, 400 livres avec titres réels (Hugo, Tolkien, Agatha Christie...), 10 catégories classiques
- **40 tests unitaires PHPUnit** couvrant les modèles, contrôleurs, validations et fonctionnalités métier

---

## 📝 Licence

Projet scolaire — BTS SIO SLAM 2025-2026 — H3 Hitema, Paris.

Template frontend basé sur [BookSaw](https://themewagon.com/themes/booksaw/) par TemplatesJungle.